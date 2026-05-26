const express = require('express');
const router  = express.Router();
const bcrypt  = require('bcryptjs');
const auth    = require('../middleware/auth');
const db      = require('../database');
const { loadConfig, saveConfig } = require('../config');

const CHARS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
const HEX_CHARS = '0123456789ABCDEF';
const MAX_DEVICES_LIMIT = 500;
const PRICE_DAYS = Array.from({ length: 30 }, (_, i) => i + 1);
const GAME_OPTIONS = [
  { value: 'BS', label: 'Blood Strike (BS)' },
  { value: 'MLBB', label: 'Mobile Legends (MLBB)' },
  { value: 'ANTARXY', label: 'Mobile Legends (Antarxy)' },
  { value: 'PUBGM', label: 'PUBG Mobile (PUBGM)' },
  { value: 'CODM', label: 'Call Of Duty (CODM)' },
  { value: '8BP', label: '8 Ball Pool (8BP)' },
  { value: 'FF', label: 'Free Fire (FF)' },
  { value: 'CFL', label: 'Crossfire (CFL)' }
];
const requireOwner = auth.requireOwner;

function generateKey(game = 'BS') {
  if (game === 'PUBGM') {
    return `${game}-${Array.from({ length: 10 }, () => HEX_CHARS[Math.floor(Math.random() * HEX_CHARS.length)]).join('')}`;
  }

  const seg = () => Array.from({ length: 4 }, () => CHARS[Math.floor(Math.random() * CHARS.length)]).join('');
  return `${game}-${seg()}-${seg()}-${seg()}`;
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

function parseSerials(raw) {
  try { return JSON.parse(raw || '[]'); } catch { return []; }
}

async function getPriceMatrix() {
  const rows = await db.all('SELECT game, duration_days, price_credit FROM key_prices ORDER BY game, duration_days');
  const matrix = {};
  for (const game of GAME_OPTIONS) matrix[game.value] = {};
  rows.forEach(row => {
    if (!matrix[row.game]) matrix[row.game] = {};
    matrix[row.game][Number(row.duration_days)] = Number(row.price_credit) || 0;
  });
  return matrix;
}

function normalizeCredit(value) {
  return Math.max(0, Math.floor(Number(value) || 0));
}

function normalizePrice(value) {
  return Math.max(1, Math.floor(Number(value) || 1));
}

function normalizeAllowedGames(value) {
  const raw = Array.isArray(value) ? value : (value ? [value] : []);
  const valid = new Set(GAME_OPTIONS.map(g => g.value));
  return [...new Set(raw.map(v => String(v).toUpperCase()).filter(v => valid.has(v)))];
}

function parseAllowedGames(raw) {
  try {
    return normalizeAllowedGames(JSON.parse(raw || '[]'));
  } catch {
    return [];
  }
}

function getVisibleGames(user) {
  if (user.isOwner) return GAME_OPTIONS;
  const allowed = new Set(normalizeAllowedGames(user.allowedGames || []));
  return GAME_OPTIONS.filter(game => allowed.has(game.value));
}

router.get('/dashboard', auth, requireOwner, async (req, res) => {
  const now = Math.floor(Date.now() / 1000);
  const cfg = await loadConfig();

  const [total, active, expired, recent] = await Promise.all([
    db.get('SELECT COUNT(*) AS c FROM keys'),
    db.get('SELECT COUNT(*) AS c FROM keys WHERE is_active=1 AND expires_at>?', [now]),
    db.get('SELECT COUNT(*) AS c FROM keys WHERE expires_at<=?', [now]),
    db.all('SELECT * FROM keys ORDER BY created_at DESC LIMIT 8')
  ]);

  const locked = await db.get("SELECT COUNT(*) AS c FROM keys WHERE device_serials != '[]'");

  res.render('dashboard', {
    title: 'Dashboard',
    panel_name: cfg.panel_name,
    stats: { total: total.c, active: active.c, expired: expired.c, locked: locked.c },
    recent, now, fmtDate, parseSerials,
    show_api_info: process.env.SHOW_API_INFO === '1'
  });
});

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

  if (!req.user.isOwner) {
    where += ' AND created_by=?';
    params.push(req.user.id);
  }

  if (search) {
    where += ' AND (key_code LIKE ? OR device_serials LIKE ? OR notes LIKE ? OR created_by_username LIKE ?)';
    params.push(`%${search}%`, `%${search}%`, `%${search}%`, `%${search}%`);
  }
  if (filter === 'active')   { where += ' AND is_active=1 AND expires_at>?'; params.push(now); }
  if (filter === 'expired')  { where += ' AND expires_at<=?';                params.push(now); }
  if (filter === 'locked')   { where += " AND device_serials != '[]'"; }
  if (filter === 'inactive') { where += ' AND is_active=0'; }

  const [keys, countRow, priceMatrix] = await Promise.all([
    db.all(`SELECT * FROM keys ${where} ORDER BY created_at DESC LIMIT ? OFFSET ?`, [...params, limit, offset]),
    db.get(`SELECT COUNT(*) AS c FROM keys ${where}`, params),
    getPriceMatrix()
  ]);

  res.render('keys', {
    title: 'Manage Keys',
    panel_name: cfg.panel_name,
    keys, total: countRow.c,
    totalPages: Math.ceil(countRow.c / limit),
    currentPage: page, search, filter, now, fmtDate, parseSerials,
    gameOptions: getVisibleGames(req.user), priceMatrix
  });
});

router.post('/keys/generate', auth, async (req, res) => {
  const { game, resource, duration, duration_unit, notes, bulk, max_devices } = req.body;
  const now        = Math.floor(Date.now() / 1000);
  const count      = Math.min(Math.max(1, parseInt(bulk) || 1), 100);
  const unit       = duration_unit || 'days';
  const durationNum = Math.max(1, parseInt(duration) || 1);
  const secs       = durationToSeconds(durationNum, unit);
  const maxDevices = Math.min(Math.max(1, parseInt(max_devices) || 1), MAX_DEVICES_LIMIT);
  const gamePrefix = (game || 'BS').toUpperCase();
  let priceEach = 0;
  let totalCost = 0;

  if (!GAME_OPTIONS.some(g => g.value === gamePrefix)) {
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

    const price = await db.get(
      'SELECT price_credit FROM key_prices WHERE game=? AND duration_days=?',
      [gamePrefix, durationNum]
    );
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
    let key, tries = 0;
    do { key = generateKey(gamePrefix); tries++; }
    while ((await db.get('SELECT id FROM keys WHERE key_code=?', [key])) && tries < 20);
    generatedKeys.push(key);
  }

  if (!req.user.isOwner && totalCost > 0) {
    await db.run(
      'UPDATE users SET credit=credit-?, updated_at=? WHERE id=?',
      [totalCost, now, req.user.id]
    );
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

router.post('/keys/:id/edit', auth, async (req, res) => {
  const { resource, expires_at_input, is_active, notes, reset_devices, max_devices } = req.body;
  const row = await db.get('SELECT * FROM keys WHERE id=?', [req.params.id]);
  if (!row) { res.flash('error', 'Key tidak ditemukan.'); return res.redirect('/admin/keys'); }

  // Non-owner users are not allowed to edit keys. Specifically, resellers
  // must not be able to modify key properties (they may only delete keys).
  if (!req.user.isOwner) {
    if (req.user.role === 'reseller') {
      res.flash('error', 'Akses ditolak. Reseller hanya boleh menghapus key.');
      return res.redirect('/admin/keys');
    }
    if (row.created_by !== req.user.id) {
      res.flash('error', 'Akses ditolak.'); return res.redirect('/admin/keys');
    }
  }

  const expiresAt  = expires_at_input
    ? Math.floor(new Date(expires_at_input).getTime() / 1000)
    : Number(row.expires_at);
  const maxDevices = Math.min(Math.max(1, parseInt(max_devices) || 1), MAX_DEVICES_LIMIT);
  const serials    = reset_devices === '1' ? '[]' : (row.device_serials || '[]');

  await db.run(
    'UPDATE keys SET resource=?, expires_at=?, is_active=?, notes=?, max_devices=?, device_serials=? WHERE id=?',
    [resource || row.resource, expiresAt, is_active === '1' ? 1 : 0, notes ?? row.notes, maxDevices, serials, row.id]
  );

  res.flash('success', 'Key berhasil diupdate.');
  res.redirect('/admin/keys');
});

router.post('/keys/:id/delete', auth, async (req, res) => {
  const row = await db.get('SELECT * FROM keys WHERE id=?', [req.params.id]);
  if (!row) { res.flash('error', 'Key tidak ditemukan.'); return res.redirect('/admin/keys'); }

  if (!req.user.isOwner && row.created_by !== req.user.id) {
    res.flash('error', 'Akses ditolak.'); return res.redirect('/admin/keys');
  }

  await db.run('DELETE FROM keys WHERE id=?', [req.params.id]);
  res.flash('success', 'Key berhasil dihapus.');
  res.redirect('/admin/keys');
});

router.post('/keys/bulk-delete', auth, async (req, res) => {
  let ids = req.body.ids;
  if (!ids) { res.flash('error', 'Pilih minimal 1 key.'); return res.redirect('/admin/keys'); }
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

router.post('/keys/bulk-deactivate', auth, async (req, res) => {
  let ids = req.body.ids;
  if (!ids) { res.flash('error', 'Pilih minimal 1 key.'); return res.redirect('/admin/keys'); }
  if (!Array.isArray(ids)) ids = [ids];
  const ph = ids.map(() => '?').join(',');
  const params = [...ids];
  let query = `UPDATE keys SET is_active=0 WHERE id IN (${ph})`;
  // Prevent resellers from performing bulk deactivation (editing).
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

router.get('/keys/export', auth, requireOwner, async (req, res) => {
  const keys = await db.all('SELECT * FROM keys ORDER BY created_at DESC');
  res.setHeader('Content-Disposition', 'attachment; filename="attic-keys.json"');
  res.json(keys);
});

router.get('/settings', auth, requireOwner, async (req, res) => {
  const cfg = await loadConfig();
  const [resellers, priceMatrix] = await Promise.all([
    db.all("SELECT id, username, credit, is_active, created_at, expires_at, allowed_games FROM users WHERE role='reseller' ORDER BY created_at DESC"),
    getPriceMatrix()
  ]);

  res.render('settings', {
    title: 'Settings',
    panel_name: cfg.panel_name,
    cfg: { ...cfg, admin_password: '' },
    resellers: resellers.map(r => ({ ...r, allowedGames: parseAllowedGames(r.allowed_games) })),
    priceMatrix,
    pricingDays: PRICE_DAYS,
    pricingGames: GAME_OPTIONS,
    fmtDate
  });
});

router.post('/settings', auth, requireOwner, async (req, res) => {
  const { panel_name, admin_username, new_password, confirm_password, salt, maintenance_mode } = req.body;
  const updates = {};
  const userUpdates = {};

  if (panel_name)     updates.panel_name     = panel_name;
  if (maintenance_mode !== undefined) updates.maintenance_mode = maintenance_mode;
  if (admin_username) {
    updates.admin_username = admin_username.trim();
    userUpdates.username = admin_username.trim();
  }
  if (salt)           updates.salt           = salt;

  if (new_password) {
    if (new_password !== confirm_password) { res.flash('error', 'Konfirmasi password tidak cocok.'); return res.redirect('/admin/settings'); }
    if (new_password.length < 6)           { res.flash('error', 'Password minimal 6 karakter.');    return res.redirect('/admin/settings'); }
    updates.admin_password = bcrypt.hashSync(new_password, 10);
    userUpdates.password_hash = updates.admin_password;
  }

  if (Object.keys(userUpdates).length > 0) {
    const sets = Object.keys(userUpdates).map(k => `${k}=?`).join(', ');
    try {
      await db.run(
        `UPDATE users SET ${sets}, updated_at=? WHERE id=? AND role='owner'`,
        [...Object.values(userUpdates), Math.floor(Date.now() / 1000), req.user.id]
      );
    } catch (err) {
      res.flash('error', 'Username owner sudah dipakai akun lain.');
      return res.redirect('/admin/settings');
    }
  }

  await saveConfig(updates);
  res.flash('success', 'Settings berhasil disimpan.');
  res.redirect('/admin/settings');
});

router.post('/masteredge', auth, requireOwner, async (req, res) => {
  const { telegram_bot_token, telegram_chat_id, mod_status, feat_esp, feat_aimbot, feat_silent_aim, feat_memory } = req.body;
  const updates = {};
  
  if (telegram_bot_token !== undefined) updates.telegram_bot_token = telegram_bot_token.trim();
  if (telegram_chat_id !== undefined) updates.telegram_chat_id = telegram_chat_id.trim();
  if (mod_status !== undefined) updates.mod_status = mod_status;
  
  const modFeatures = {
    esp: feat_esp === '1',
    aimbot: feat_aimbot === '1',
    silent_aim: feat_silent_aim === '1',
    memory: feat_memory === '1'
  };
  updates.mod_features = JSON.stringify(modFeatures);
  
  await saveConfig(updates);
  res.flash('success', 'Masteredge Settings berhasil disimpan.');
  res.redirect('/admin/settings');
});

router.post('/resellers', auth, requireOwner, async (req, res) => {
  const username = (req.body.username || '').trim();
  const password = req.body.password || '';
  const credit = normalizeCredit(req.body.credit);
  const duration = req.body.duration;
  const allowedGames = normalizeAllowedGames(req.body.allowed_games);
  const now = Math.floor(Date.now() / 1000);

  let expiresAt = null;
  if (duration === '1_month') expiresAt = now + (30 * 86400);
  else if (duration === '1_year') expiresAt = now + (365 * 86400);

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
    await db.run(
      'INSERT INTO users (username, password_hash, role, credit, is_active, created_at, expires_at, allowed_games) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
      [username, bcrypt.hashSync(password, 10), 'reseller', credit, 1, now, expiresAt, JSON.stringify(allowedGames)]
    );
  } catch (err) {
    res.flash('error', 'Username reseller sudah dipakai.');
    return res.redirect('/admin/settings');
  }

  res.flash('success', `Reseller ${username} berhasil dibuat.`);
  res.redirect('/admin/settings');
});

router.post('/resellers/:id', auth, requireOwner, async (req, res) => {
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
    if (newExpiresAt < now) newExpiresAt = now; // If already expired, start from now
    
    if (extendDuration === '1_month') newExpiresAt += (30 * 86400);
    else if (extendDuration === '1_year') newExpiresAt += (365 * 86400);
    else if (extendDuration === 'lifetime') newExpiresAt = null;
    
    fields.push('expires_at=?');
    args.push(newExpiresAt);
  }

  if (password) {
    fields.push('password_hash=?');
    args.push(bcrypt.hashSync(password, 10));
  }
  args.push(id);

  try {
    await db.run(`UPDATE users SET ${fields.join(', ')} WHERE id=? AND role='reseller'`, args);
  } catch (err) {
    res.flash('error', 'Username reseller sudah dipakai.');
    return res.redirect('/admin/settings');
  }

  res.flash('success', `Reseller ${username} berhasil diupdate.`);
  res.redirect('/admin/settings');
});

router.post('/resellers/:id/delete', auth, requireOwner, async (req, res) => {
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

router.post('/prices', auth, requireOwner, async (req, res) => {
  const prices = req.body.prices || {};
  for (const game of GAME_OPTIONS) {
    const gamePrices = prices[game.value] || {};
    for (const day of PRICE_DAYS) {
      await db.run(
        'INSERT INTO key_prices (game, duration_days, price_credit) VALUES (?, ?, ?) ON CONFLICT(game, duration_days) DO UPDATE SET price_credit=excluded.price_credit',
        [game.value, day, normalizePrice(gamePrices[day])]
      );
    }
  }

  res.flash('success', 'Harga key berhasil disimpan.');
  res.redirect('/admin/settings');
});

// Reseller Profile
router.get('/profile', auth, async (req, res) => {
  const cfg = await loadConfig();
  const user = await db.get('SELECT * FROM users WHERE id=?', [req.user.id]);
  
  res.render('profile', {
    title: 'My Profile',
    panel_name: cfg.panel_name,
    user,
    fmtDate
  });
});

router.post('/profile', auth, async (req, res) => {
  const new_username = (req.body.new_username || '').trim();
  const old_password = req.body.old_password || '';
  const new_password = req.body.new_password || '';
  
  const user = await db.get('SELECT * FROM users WHERE id=?', [req.user.id]);
  const updates = [];
  const args = [];

  if (new_username && new_username !== user.username) {
    updates.push('username=?');
    args.push(new_username);
  }

  if (new_password) {
    if (!old_password) {
      res.flash('error', 'Masukkan password lama untuk mengubah password.');
      return res.redirect('/admin/profile');
    }
    if (!bcrypt.compareSync(old_password, user.password_hash)) {
      res.flash('error', 'Password lama salah.');
      return res.redirect('/admin/profile');
    }
    if (new_password.length < 6) {
      res.flash('error', 'Password baru minimal 6 karakter.');
      return res.redirect('/admin/profile');
    }
    updates.push('password_hash=?');
    args.push(bcrypt.hashSync(new_password, 10));
  }

  if (updates.length > 0) {
    args.push(req.user.id);
    try {
      await db.run(`UPDATE users SET ${updates.join(', ')} WHERE id=?`, args);
      res.flash('success', 'Profil berhasil diupdate.');
    } catch (err) {
      res.flash('error', 'Username sudah dipakai orang lain.');
    }
  }

  res.redirect('/admin/profile');
});

// File Management
const multer = require('multer');
const fs = require('fs');
const path = require('path');
const githubFiles = require('../services/githubFiles');

const upload = multer({
  storage: multer.memoryStorage(),
  limits: { fileSize: Number(process.env.MAX_UPLOAD_BYTES || 100 * 1024 * 1024) }
});

function uploadDir() {
  return path.resolve(__dirname, '../public/uploads');
}

function resolveLocalUpload(filename) {
  const dir = uploadDir();
  const target = path.resolve(dir, githubFiles.sanitizeFileName(filename));
  if (!target.startsWith(dir + path.sep) && target !== dir) {
    throw new Error('Nama file tidak valid.');
  }
  return target;
}

function listLocalFiles() {
  const dir = uploadDir();
  if (!fs.existsSync(dir)) return [];

  return fs.readdirSync(dir).map(name => {
    const stats = fs.statSync(path.join(dir, name));
    return {
      name,
      path: name,
      sha: '',
      sizeBytes: stats.size,
      size: `${(stats.size / 1024).toFixed(2)} KB`,
      date: stats.mtime,
      url: `/uploads/${encodeURIComponent(name)}`,
      storage: 'local'
    };
  }).sort((a, b) => b.date - a.date);
}

router.get('/files', auth, requireOwner, async (req, res) => {
  const cfg = await loadConfig();
  let files = [];
  const githubConfigured = githubFiles.isConfigured();

  try {
    files = githubConfigured ? await githubFiles.listFiles() : listLocalFiles();
  } catch (err) {
    console.error('List files error:', err.message);
    res.flash('error', 'Gagal mengambil daftar file GitHub. Cek token, owner, repo, dan branch.');
    files = [];
  }

  res.render('files', {
    title: 'Manage Files',
    panel_name: cfg.panel_name,
    files,
    storageMode: githubConfigured ? 'GitHub repository' : 'Local fallback',
    githubConfig: githubFiles.getConfig()
  });
});

router.post('/files/upload', auth, requireOwner, upload.single('file'), async (req, res) => {
  try {
    if (!req.file) {
      res.flash('error', 'Tidak ada file yang diupload.');
      return res.redirect('/admin/files');
    }

    if (githubFiles.isConfigured()) {
      const uploaded = await githubFiles.uploadFile(req.file);
      res.flash('success', `File ${uploaded.name} berhasil diupload ke GitHub.`);
      return res.redirect('/admin/files');
    }

    if (process.env.VERCEL || process.env.AWS_REGION) {
      res.flash('error', 'Sistem read-only (Vercel) terdeteksi. Anda wajib mengatur GITHUB_TOKEN di Environment Variables untuk mengupload file.');
      return res.redirect('/admin/files');
    }

    const dir = uploadDir();
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    const fileName = githubFiles.sanitizeFileName(req.file.originalname);
    fs.writeFileSync(resolveLocalUpload(fileName), req.file.buffer);
    res.flash('success', `File ${fileName} berhasil diupload lokal.`);
  } catch (err) {
    console.error('Upload file error:', err.message);
    res.flash('error', `Gagal upload file: ${err.message}`);
  }
  res.redirect('/admin/files');
});

async function deleteManagedFile(req, res) {
  try {
    const targetPath = req.body.path || req.params.filename;
    const sha = req.body.sha || '';
    if (!targetPath) {
      res.flash('error', 'File tidak ditemukan.');
      return res.redirect('/admin/files');
    }

    if (githubFiles.isConfigured()) {
      await githubFiles.deleteFile(targetPath, sha);
      res.flash('success', 'File berhasil dihapus dari GitHub.');
      return res.redirect('/admin/files');
    }

    const filepath = resolveLocalUpload(targetPath);
    if (fs.existsSync(filepath)) {
      fs.unlinkSync(filepath);
      res.flash('success', `File ${githubFiles.sanitizeFileName(targetPath)} berhasil dihapus.`);
    } else {
      res.flash('error', 'File tidak ditemukan.');
    }
  } catch (err) {
    console.error('Delete file error:', err.message);
    res.flash('error', `Gagal hapus file: ${err.message}`);
  }
  res.redirect('/admin/files');
}

router.post('/files/delete', auth, requireOwner, deleteManagedFile);
router.post('/files/delete/:filename', auth, requireOwner, deleteManagedFile);

router.post('/files/rename', auth, requireOwner, async (req, res) => {
  try {
    const oldPath = req.body.path || req.body.old_name;
    const newName = githubFiles.sanitizeFileName(req.body.new_name);
    if (!oldPath || !newName) {
      res.flash('error', 'Nama file rename tidak valid.');
      return res.redirect('/admin/files');
    }

    if (githubFiles.isConfigured()) {
      await githubFiles.renameFile(oldPath, newName);
      res.flash('success', `File berhasil direname menjadi ${newName} di GitHub.`);
      return res.redirect('/admin/files');
    }

    const oldFile = resolveLocalUpload(oldPath);
    const newFile = resolveLocalUpload(newName);
    if (!fs.existsSync(oldFile)) {
      res.flash('error', 'File lama tidak ditemukan.');
      return res.redirect('/admin/files');
    }
    fs.renameSync(oldFile, newFile);
    res.flash('success', `File berhasil direname menjadi ${newName}.`);
  } catch (err) {
    console.error('Rename file error:', err.message);
    res.flash('error', `Gagal rename file: ${err.message}`);
  }
  res.redirect('/admin/files');
});

module.exports = router;
