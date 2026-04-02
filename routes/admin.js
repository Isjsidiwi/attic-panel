const express = require('express');
const router  = express.Router();
const bcrypt  = require('bcryptjs');
const auth    = require('../middleware/auth');
const db      = require('../database');
const { loadConfig, saveConfig } = require('../config');

/* ─── Helpers ────────────────────────────────── */
const CHARS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

function generateKey() {
  const seg = () => Array.from({ length: 4 }, () => CHARS[Math.floor(Math.random() * CHARS.length)]).join('');
  return `ATTIC-${seg()}-${seg()}-${seg()}`;
}

function fmtDate(unix) {
  if (!unix) return '—';
  return new Date(Number(unix) * 1000).toLocaleString('id-ID', {
    timeZone: 'Asia/Jakarta',
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
}

function durationToSeconds(val, unit) {
  const n = parseInt(val) || 1;
  if (unit === 'hours')  return n * 3600;
  if (unit === 'months') return n * 30 * 86400;
  return n * 86400;
}

/* ─── Dashboard ──────────────────────────────── */
router.get('/dashboard', auth, async (req, res) => {
  const now  = Math.floor(Date.now() / 1000);
  const cfg  = await loadConfig();

  const [total, active, expired, locked, recent] = await Promise.all([
    db.get('SELECT COUNT(*) AS c FROM keys'),
    db.get('SELECT COUNT(*) AS c FROM keys WHERE is_active=1 AND expires_at>?', [now]),
    db.get('SELECT COUNT(*) AS c FROM keys WHERE expires_at<=?', [now]),
    db.get('SELECT COUNT(*) AS c FROM keys WHERE device_serial IS NOT NULL'),
    db.all('SELECT * FROM keys ORDER BY created_at DESC LIMIT 8')
  ]);

  res.render('dashboard', {
    title: 'Dashboard',
    panel_name: cfg.panel_name,
    stats: { total: total.c, active: active.c, expired: expired.c, locked: locked.c },
    recent, now, fmtDate
  });
});

/* ─── Keys list ──────────────────────────────── */
router.get('/keys', auth, async (req, res) => {
  const now    = Math.floor(Date.now() / 1000);
  const page   = Math.max(1, parseInt(req.query.page) || 1);
  const search = req.query.search || '';
  const filter = req.query.filter || 'all';
  const limit  = 20;
  const offset = (page - 1) * limit;
  const cfg    = await loadConfig();

  let where = 'WHERE 1=1';
  const params = [];

  if (search) {
    where += ' AND (key_code LIKE ? OR device_serial LIKE ? OR notes LIKE ?)';
    params.push(`%${search}%`, `%${search}%`, `%${search}%`);
  }
  if (filter === 'active')   { where += ' AND is_active=1 AND expires_at>?'; params.push(now); }
  if (filter === 'expired')  { where += ' AND expires_at<=?';                params.push(now); }
  if (filter === 'locked')   { where += ' AND device_serial IS NOT NULL'; }
  if (filter === 'inactive') { where += ' AND is_active=0'; }

  const [keys, countRow] = await Promise.all([
    db.all(`SELECT * FROM keys ${where} ORDER BY created_at DESC LIMIT ? OFFSET ?`, [...params, limit, offset]),
    db.get(`SELECT COUNT(*) AS c FROM keys ${where}`, params)
  ]);

  const total      = countRow.c;
  const totalPages = Math.ceil(total / limit);

  res.render('keys', {
    title: 'Manage Keys',
    panel_name: cfg.panel_name,
    keys, total, totalPages, currentPage: page,
    search, filter, now, fmtDate
  });
});

/* ─── Generate ───────────────────────────────── */
router.post('/keys/generate', auth, async (req, res) => {
  const { resource, duration, duration_unit, notes, bulk } = req.body;
  const now   = Math.floor(Date.now() / 1000);
  const count = Math.min(Math.max(1, parseInt(bulk) || 1), 100);
  const secs  = durationToSeconds(duration, duration_unit);

  for (let i = 0; i < count; i++) {
    let key, tries = 0;
    do {
      key = generateKey();
      tries++;
    } while ((await db.get('SELECT id FROM keys WHERE key_code=?', [key])) && tries < 20);

    await db.run(
      'INSERT INTO keys (key_code, resource, created_at, expires_at, notes) VALUES (?, ?, ?, ?, ?)',
      [key, resource || 'vip', now, now + secs, notes || '']
    );
  }

  res.flash('success', `${count} key berhasil digenerate.`);
  res.redirect('/admin/keys');
});

/* ─── Edit ───────────────────────────────────── */
router.post('/keys/:id/edit', auth, async (req, res) => {
  const { resource, expires_at_input, is_active, notes, reset_device } = req.body;
  const row = await db.get('SELECT * FROM keys WHERE id=?', [req.params.id]);
  if (!row) { res.flash('error', 'Key tidak ditemukan.'); return res.redirect('/admin/keys'); }

  const expiresAt = expires_at_input
    ? Math.floor(new Date(expires_at_input).getTime() / 1000)
    : Number(row.expires_at);

  await db.run(
    'UPDATE keys SET resource=?, expires_at=?, is_active=?, notes=?, device_serial=? WHERE id=?',
    [
      resource || row.resource,
      expiresAt,
      is_active === '1' ? 1 : 0,
      notes ?? row.notes,
      reset_device === '1' ? null : row.device_serial,
      row.id
    ]
  );

  res.flash('success', 'Key berhasil diupdate.');
  res.redirect('/admin/keys');
});

/* ─── Delete ─────────────────────────────────── */
router.post('/keys/:id/delete', auth, async (req, res) => {
  await db.run('DELETE FROM keys WHERE id=?', [req.params.id]);
  res.flash('success', 'Key berhasil dihapus.');
  res.redirect('/admin/keys');
});

/* ─── Bulk delete ────────────────────────────── */
router.post('/keys/bulk-delete', auth, async (req, res) => {
  let ids = req.body.ids;
  if (!ids) { res.flash('error', 'Pilih minimal 1 key.'); return res.redirect('/admin/keys'); }
  if (!Array.isArray(ids)) ids = [ids];
  const ph = ids.map(() => '?').join(',');
  await db.run(`DELETE FROM keys WHERE id IN (${ph})`, ids);
  res.flash('success', `${ids.length} key berhasil dihapus.`);
  res.redirect('/admin/keys');
});

/* ─── Bulk deactivate ────────────────────────── */
router.post('/keys/bulk-deactivate', auth, async (req, res) => {
  let ids = req.body.ids;
  if (!ids) { res.flash('error', 'Pilih minimal 1 key.'); return res.redirect('/admin/keys'); }
  if (!Array.isArray(ids)) ids = [ids];
  const ph = ids.map(() => '?').join(',');
  await db.run(`UPDATE keys SET is_active=0 WHERE id IN (${ph})`, ids);
  res.flash('success', `${ids.length} key dinonaktifkan.`);
  res.redirect('/admin/keys');
});

/* ─── Export ─────────────────────────────────── */
router.get('/keys/export', auth, async (req, res) => {
  const keys = await db.all('SELECT * FROM keys ORDER BY created_at DESC');
  res.setHeader('Content-Disposition', 'attachment; filename="attic-keys.json"');
  res.json(keys);
});

/* ─── Settings ───────────────────────────────── */
router.get('/settings', auth, async (req, res) => {
  const cfg = await loadConfig();
  res.render('settings', {
    title: 'Settings',
    panel_name: cfg.panel_name,
    cfg: { ...cfg, admin_password: '' }
  });
});

router.post('/settings', auth, async (req, res) => {
  const { panel_name, admin_username, new_password, confirm_password, salt } = req.body;
  const updates = {};

  if (panel_name)     updates.panel_name     = panel_name;
  if (admin_username) updates.admin_username = admin_username;
  if (salt)           updates.salt           = salt;

  if (new_password) {
    if (new_password !== confirm_password) {
      res.flash('error', 'Konfirmasi password tidak cocok.');
      return res.redirect('/admin/settings');
    }
    if (new_password.length < 6) {
      res.flash('error', 'Password minimal 6 karakter.');
      return res.redirect('/admin/settings');
    }
    updates.admin_password = bcrypt.hashSync(new_password, 10);
  }

  await saveConfig(updates);
  res.flash('success', 'Settings berhasil disimpan.');
  res.redirect('/admin/settings');
});

module.exports = router;
