const express = require('express');
const router = express.Router();
const bcrypt = require('bcryptjs');
const crypto = require('crypto');
const auth = require('../../middleware/auth');
const db = require('../../database');
const {
  normalizeCredit,
  normalizeAllowedGames,
  normalizePrice,
  GAME_OPTIONS,
  PRICE_DAYS
} = require('../../utils/adminUtils');

const requireOwner = auth.requireOwner;

async function ensureResellerColumns() {
  for (const sql of [
    'ALTER TABLE users ADD COLUMN expires_at INTEGER DEFAULT 0',
    "ALTER TABLE users ADD COLUMN allowed_games TEXT NOT NULL DEFAULT '[]'"
  ]) {
    try {
      await db.run(sql);
    } catch (err) {
      if (!String(err && err.message).toLowerCase().includes('duplicate column')) throw err;
    }
  }
}

router.post('/', auth, requireOwner, async (req, res) => {
  const username = (req.body.username || '').trim();
  const password = req.body.password || '';
  const credit = normalizeCredit(req.body.credit);
  const duration = req.body.duration;
  const allowedGames = normalizeAllowedGames(req.body.allowed_games);
  const now = Math.floor(Date.now() / 1000);

  let expiresAt = 0;
  if (duration === '1_month') expiresAt = now + 30 * 86400;
  else if (duration === '1_year') expiresAt = now + 365 * 86400;

  if (!username) {
    res.flash('error', 'Username reseller wajib diisi.');
    return res.redirect('/admin/settings');
  }
  if (password.length < 6) {
    res.flash('error', 'Password reseller minimal 6 karakter.');
    return res.redirect('/admin/settings');
  }
  if (allowedGames.length === 0) {
    res.flash('error', 'Pilih minimal 1 game yang boleh diakses reseller.');
    return res.redirect('/admin/settings');
  }

  try {
    await ensureResellerColumns();
    await db.run(
      'INSERT INTO users (username, password_hash, role, credit, is_active, created_at, expires_at, allowed_games) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
      [username, bcrypt.hashSync(password, 10), 'reseller', credit, 1, now, expiresAt, JSON.stringify(allowedGames)]
    );
    await db.run('UPDATE users SET endpoint_api_secret=? WHERE username=? AND role=\'reseller\' AND (endpoint_api_secret IS NULL OR endpoint_api_secret=\'\')', [
      crypto.randomBytes(24).toString('base64url'),
      username
    ]);
  } catch (err) {
    console.error('Create reseller failed:', err);
    const isDuplicate = String(err && err.message).toLowerCase().includes('unique');
    res.flash('error', isDuplicate ? 'Username reseller sudah dipakai.' : 'Gagal membuat reseller. Cek konfigurasi database.');
    return res.redirect('/admin/settings');
  }

  res.flash('success', `Reseller ${username} berhasil dibuat.`);
  res.redirect('/admin/settings');
});

router.post('/:id', auth, requireOwner, async (req, res) => {
  const id = Number(req.params.id);
  const username = (req.body.username || '').trim();
  const password = req.body.password || '';
  const credit = normalizeCredit(req.body.credit);
  const isActive = req.body.is_active === '1' ? 1 : 0;
  const extendDuration = req.body.extend_duration;
  const allowedGames = normalizeAllowedGames(req.body.allowed_games);
  const now = Math.floor(Date.now() / 1000);

  const reseller = await db.get("SELECT * FROM users WHERE id=? AND role='reseller'", [id]);
  if (!reseller) {
    res.flash('error', 'Reseller tidak ditemukan.');
    return res.redirect('/admin/settings');
  }
  if (!username) {
    res.flash('error', 'Username reseller wajib diisi.');
    return res.redirect('/admin/settings');
  }
  if (password && password.length < 6) {
    res.flash('error', 'Password reseller minimal 6 karakter.');
    return res.redirect('/admin/settings');
  }
  if (allowedGames.length === 0) {
    res.flash('error', 'Pilih minimal 1 game yang boleh diakses reseller.');
    return res.redirect('/admin/settings');
  }

  const fields = ['username=?', 'credit=?', 'is_active=?', 'allowed_games=?', 'updated_at=?'];
  const args = [username, credit, isActive, JSON.stringify(allowedGames), now];

  if (extendDuration && extendDuration !== 'none') {
    let newExpiresAt = reseller.expires_at || now;
    if (newExpiresAt < now) newExpiresAt = now;

    if (extendDuration === '1_month') newExpiresAt += 30 * 86400;
    else if (extendDuration === '1_year') newExpiresAt += 365 * 86400;
    else if (extendDuration === 'lifetime') newExpiresAt = 0;

    fields.push('expires_at=?');
    args.push(newExpiresAt);
  }

  if (password) {
    fields.push('password_hash=?');
    args.push(bcrypt.hashSync(password, 10));
  }
  args.push(id);

  try {
    await ensureResellerColumns();
    await db.run(`UPDATE users SET ${fields.join(', ')} WHERE id=? AND role='reseller'`, args);
  } catch (err) {
    res.flash('error', 'Username reseller sudah dipakai.');
    return res.redirect('/admin/settings');
  }

  res.flash('success', `Reseller ${username} berhasil diupdate.`);
  res.redirect('/admin/settings');
});

router.post('/:id/delete', auth, requireOwner, async (req, res) => {
  const id = Number(req.params.id);
  const reseller = await db.get("SELECT * FROM users WHERE id=? AND role='reseller'", [id]);

  if (!reseller) {
    res.flash('error', 'Reseller tidak ditemukan.');
    return res.redirect('/admin/settings');
  }

  await db.run('DELETE FROM users WHERE id=?', [id]);
  res.flash('success', `Reseller ${reseller.username} berhasil dihapus.`);
  res.redirect('/admin/settings');
});

module.exports = router;
