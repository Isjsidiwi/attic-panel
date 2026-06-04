const express = require('express');
const router = express.Router();
const auth = require('../../middleware/auth');
const db = require('../../database');
const { loadConfig } = require('../../config');
const {
  generateKey,
  fmtDate,
  durationToSeconds,
  parseSerials,
  getPriceMatrix,
  getVisibleGames,
  normalizePrice,
  GAME_OPTIONS,
  normalizeAllowedGames
} = require('../../utils/adminUtils');

const MAX_DEVICES_LIMIT = 500;

router.get('/', auth, async (req, res) => {
  const now = Math.floor(Date.now() / 1000);
  const page = Math.max(1, parseInt(req.query.page) || 1);
  const search = req.query.search || '';
  const filter = req.query.filter || 'all';
  const creator = req.query.creator || 'all';
  const limit = 20;
  const offset = (page - 1) * limit;
  const cfg = await loadConfig();

  let where = 'WHERE 1=1';
  const params = [];

  if (!req.user.isOwner) {
    where += ' AND created_by=?';
    params.push(req.user.id);
  } else {
    // Owner can filter by creator
    if (creator === 'owner') {
      where += ' AND created_by=?';
      params.push(req.user.id);
    } else if (creator === 'resellers') {
      where += ' AND created_by!=?';
      params.push(req.user.id);
    }
  }

  if (search) {
    where += ' AND (key_code LIKE ? OR device_serials LIKE ? OR notes LIKE ? OR created_by_username LIKE ?)';
    params.push(`%${search}%`, `%${search}%`, `%${search}%`, `%${search}%`);
  }
  if (filter === 'active') {
    where += ' AND is_active=1 AND expires_at>?';
    params.push(now);
  }
  if (filter === 'expired') {
    where += ' AND expires_at<=?';
    params.push(now);
  }
  if (filter === 'locked') {
    where += " AND device_serials != '[]'";
  }
  if (filter === 'inactive') {
    where += ' AND is_active=0';
  }

  const [keys, countRow, priceMatrix] = await Promise.all([
    db.all(`SELECT * FROM keys ${where} ORDER BY created_at DESC LIMIT ? OFFSET ?`, [...params, limit, offset]),
    db.get(`SELECT COUNT(*) AS c FROM keys ${where}`, params),
    getPriceMatrix()
  ]);

  res.render('keys', {
    title: 'Manage Keys',
    panel_name: cfg.panel_name,
    keys,
    total: countRow.c,
    totalPages: Math.ceil(countRow.c / limit),
    currentPage: page,
    search,
    filter,
    creator,
    now,
    fmtDate,
    parseSerials,
    gameOptions: getVisibleGames(req.user),
    priceMatrix
  });
});

router.post('/generate', auth, async (req, res) => {
  const { game, resource, duration, duration_unit, notes, bulk, max_devices } = req.body;
  const now = Math.floor(Date.now() / 1000);
  const count = Math.min(Math.max(1, parseInt(bulk) || 1), 100);
  const unit = duration_unit || 'days';
  const durationNum = Math.max(1, parseInt(duration) || 1);
  const secs = durationToSeconds(durationNum, unit);
  const maxDevices = Math.min(Math.max(1, parseInt(max_devices) || 1), MAX_DEVICES_LIMIT);
  const gamePrefix = (game || 'BS').toUpperCase();
  let priceEach = 0;
  let totalCost = 0;

  if (!GAME_OPTIONS.some((g) => g.value === gamePrefix)) {
    res.flash('error', 'Game tidak valid.');
    return res.redirect('/admin/keys');
  }

  if (!req.user.isOwner && !normalizeAllowedGames(req.user.allowedGames).includes(gamePrefix)) {
    res.flash('error', 'Game ini belum diizinkan owner untuk akun reseller kamu.');
    return res.redirect('/admin/keys');
  }

  if (!req.user.isOwner) {
    if (unit !== 'days' || durationNum < 1 || durationNum > 30) {
      res.flash('error', 'Reseller hanya bisa membuat key harian 1 sampai 30 hari.');
      return res.redirect('/admin/keys');
    }

    const price = await db.get('SELECT price_credit FROM key_prices WHERE game=? AND duration_days=?', [
      gamePrefix,
      durationNum
    ]);
    priceEach = normalizePrice(price && price.price_credit);
    totalCost = priceEach * count;

    const reseller = await db.get('SELECT credit, is_active FROM users WHERE id=?', [req.user.id]);
    const balance = Number(reseller && reseller.credit) || 0;
    if (!reseller || !reseller.is_active) {
      res.flash('error', 'Akun reseller tidak aktif.');
      return res.redirect('/admin/keys');
    }
    if (balance < totalCost) {
      res.flash('error', `Credit tidak cukup. Butuh ${totalCost} credit, saldo kamu ${balance}.`);
      return res.redirect('/admin/keys');
    }
  }

  const generatedKeys = [];
  for (let i = 0; i < count; i++) {
    let key,
      tries = 0;
    do {
      key = generateKey(gamePrefix);
      tries++;
    } while ((await db.get('SELECT id FROM keys WHERE key_code=?', [key])) && tries < 20);
    generatedKeys.push(key);
  }

  if (!req.user.isOwner && totalCost > 0) {
    await db.run('UPDATE users SET credit=credit-?, updated_at=? WHERE id=?', [totalCost, now, req.user.id]);
  }

  for (const key of generatedKeys) {
    await db.run(
      'INSERT INTO keys (key_code, resource, device_serials, max_devices, created_at, expires_at, notes, created_by, created_by_username, price_paid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
      [key, resource || 'vip', '[]', maxDevices, now, now + secs, notes || '', req.user.id, req.user.username, priceEach]
    );
  }

  const creditMsg = req.user.isOwner ? '' : ` Credit terpakai: ${totalCost}.`;
  res.flash('success', `${count} key (${gamePrefix}) berhasil digenerate.${creditMsg}`);
  res.redirect('/admin/keys');
});

router.post('/:id/edit', auth, async (req, res) => {
  const { resource, expires_at_input, is_active, notes, reset_devices, max_devices } = req.body;
  const row = await db.get('SELECT * FROM keys WHERE id=?', [req.params.id]);
  if (!row) {
    res.flash('error', 'Key tidak ditemukan.');
    return res.redirect('/admin/keys');
  }

  if (!req.user.isOwner) {
    if (req.user.role === 'reseller') {
      res.flash('error', 'Akses ditolak. Reseller hanya boleh menghapus key.');
      return res.redirect('/admin/keys');
    }
    if (row.created_by !== req.user.id) {
      res.flash('error', 'Akses ditolak.');
      return res.redirect('/admin/keys');
    }
  }

  const expiresAt = expires_at_input ? Math.floor(new Date(expires_at_input).getTime() / 1000) : Number(row.expires_at);
  const maxDevices = Math.min(Math.max(1, parseInt(max_devices) || 1), MAX_DEVICES_LIMIT);
  const serials = reset_devices === '1' ? '[]' : row.device_serials || '[]';

  await db.run(
    'UPDATE keys SET resource=?, expires_at=?, is_active=?, notes=?, max_devices=?, device_serials=? WHERE id=?',
    [resource || row.resource, expiresAt, is_active === '1' ? 1 : 0, notes ?? row.notes, maxDevices, serials, row.id]
  );

  res.flash('success', 'Key berhasil diupdate.');
  res.redirect('/admin/keys');
});

router.post('/:id/delete', auth, async (req, res) => {
  const row = await db.get('SELECT * FROM keys WHERE id=?', [req.params.id]);
  if (!row) {
    res.flash('error', 'Key tidak ditemukan.');
    return res.redirect('/admin/keys');
  }

  if (!req.user.isOwner && row.created_by !== req.user.id) {
    res.flash('error', 'Akses ditolak.');
    return res.redirect('/admin/keys');
  }

  await db.run('DELETE FROM keys WHERE id=?', [req.params.id]);
  res.flash('success', 'Key berhasil dihapus.');
  res.redirect('/admin/keys');
});

router.post('/bulk-delete', auth, async (req, res) => {
  let ids = req.body.ids;
  if (!ids) {
    res.flash('error', 'Pilih minimal 1 key.');
    return res.redirect('/admin/keys');
  }
  if (!Array.isArray(ids)) ids = [ids];
  const ph = ids.map(() => '?').join(',');
  const params = [...ids];
  let query = `DELETE FROM keys WHERE id IN (${ph})`;

  if (!req.user.isOwner) {
    query += ' AND created_by=?';
    params.push(req.user.id);
  }

  await db.run(query, params);
  res.flash('success', `Key berhasil dihapus.`);
  res.redirect('/admin/keys');
});

router.post('/bulk-deactivate', auth, async (req, res) => {
  let ids = req.body.ids;
  if (!ids) {
    res.flash('error', 'Pilih minimal 1 key.');
    return res.redirect('/admin/keys');
  }
  if (!Array.isArray(ids)) ids = [ids];
  const ph = ids.map(() => '?').join(',');
  const params = [...ids];
  let query = `UPDATE keys SET is_active=0 WHERE id IN (${ph})`;
  if (!req.user.isOwner && req.user.role === 'reseller') {
    res.flash('error', 'Akses ditolak. Reseller hanya boleh menghapus key.');
    return res.redirect('/admin/keys');
  }

  if (!req.user.isOwner) {
    query += ' AND created_by=?';
    params.push(req.user.id);
  }

  await db.run(query, params);
  res.flash('success', `Key dinonaktifkan.`);
  res.redirect('/admin/keys');
});

router.get('/export', auth, auth.requireOwner, async (req, res) => {
  const keys = await db.all('SELECT * FROM keys ORDER BY created_at DESC');
  res.setHeader('Content-Disposition', 'attachment; filename="attic-keys.json"');
  res.json(keys);
});

module.exports = router;
