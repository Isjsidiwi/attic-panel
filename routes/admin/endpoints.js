const express = require('express');
const auth = require('../../middleware/auth');
const db = require('../../database');
const { loadConfig } = require('../../config');

const router = express.Router();
const requireOwner = auth.requireOwner;

const RESERVED_PATHS = new Set(['/api/game/mlbb', '/api/vvip-bs', '/api/ev8bp', '/api/codm']);

function normalizePath(value) {
  let path = String(value || '').trim().toLowerCase();
  if (!path.startsWith('/')) path = `/${path}`;
  path = path.replace(/\/+$/g, '').replace(/\/+/g, '/');
  if (!path.startsWith('/api/')) path = `/api${path}`;
  return path;
}

function validatePath(path) {
  if (!/^\/api\/[a-z0-9][a-z0-9/_-]{1,80}$/.test(path)) return 'Path endpoint tidak valid.';
  if (RESERVED_PATHS.has(path) || path.startsWith('/api/store')) return 'Endpoint bentrok dengan endpoint bawaan.';
  return null;
}

async function canCreateEndpoint(user) {
  if (user.role === 'owner') return true;
  const row = await db.get('SELECT can_create_endpoint FROM users WHERE id=?', [user.id]);
  return Boolean(row && row.can_create_endpoint);
}

router.get('/', auth, async (req, res) => {
  const cfg = await loadConfig();
  const isOwner = req.user.role === 'owner';
  const [endpoints, resellers, accessRows] = await Promise.all([
    isOwner
      ? db.all('SELECT * FROM custom_endpoints ORDER BY created_at DESC')
      : db.all(
          `SELECT DISTINCT ce.* FROM custom_endpoints ce
           LEFT JOIN custom_endpoint_access cea ON cea.endpoint_id=ce.id
           WHERE ce.created_by=? OR (cea.reseller_id=? AND cea.can_use=1)
           ORDER BY ce.created_at DESC`,
          [req.user.id, req.user.id]
        ),
    isOwner
      ? db.all("SELECT id, username, can_create_endpoint FROM users WHERE role='reseller' ORDER BY username ASC")
      : [],
    isOwner ? db.all('SELECT * FROM custom_endpoint_access WHERE endpoint_id IS NOT NULL') : []
  ]);

  res.render('endpoints', {
    title: 'Custom Endpoints',
    panel_name: cfg.panel_name,
    endpoints,
    resellers,
    accessRows,
    canCreateEndpoint: isOwner || (await canCreateEndpoint(req.user)),
    baseEndpoint: 'https://qyz.vercel.app/api/naah'
  });
});

router.post('/', auth, async (req, res) => {
  if (!(await canCreateEndpoint(req.user))) {
    res.flash('error', 'Akun kamu belum diberi akses membuat endpoint.');
    return res.redirect('/admin/endpoints');
  }

  const path = normalizePath(req.body.path);
  const method = ['GET', 'POST', 'ALL'].includes(String(req.body.method || '').toUpperCase())
    ? String(req.body.method).toUpperCase()
    : 'POST';
  const responseMode = req.body.response_mode === 'chacha20-poly1305' ? 'chacha20-poly1305' : 'json';
  const responseBody = String(req.body.response_body || '{}').trim() || '{}';
  const error = validatePath(path);
  if (error) {
    res.flash('error', error);
    return res.redirect('/admin/endpoints');
  }

  try {
    JSON.parse(responseBody);
  } catch {
    res.flash('error', 'Custom response wajib JSON valid.');
    return res.redirect('/admin/endpoints');
  }

  try {
    const now = Math.floor(Date.now() / 1000);
    const result = await db.run(
      'INSERT INTO custom_endpoints (path, method, response_body, response_mode, is_active, created_by, created_by_name, created_at) VALUES (?, ?, ?, ?, 1, ?, ?, ?)',
      [path, method, responseBody, responseMode, req.user.id, req.user.username, now]
    );
    const endpointId = Number(result.lastInsertRowid || result.lastInsertId || 0);
    if (req.user.role === 'reseller' && endpointId) {
      await db.run(
        'INSERT OR IGNORE INTO custom_endpoint_access (endpoint_id, reseller_id, can_use, can_create, created_at) VALUES (?, ?, 1, 0, ?)',
        [endpointId, req.user.id, now]
      );
    }
    res.flash('success', `Endpoint ${path} berhasil dibuat.`);
  } catch (err) {
    res.flash('error', String(err.message || '').toLowerCase().includes('unique') ? 'Endpoint sudah ada. Gunakan nama lain.' : 'Gagal membuat endpoint.');
  }
  res.redirect('/admin/endpoints');
});

router.post('/:id/delete', auth, async (req, res) => {
  const endpoint = await db.get('SELECT * FROM custom_endpoints WHERE id=?', [req.params.id]);
  if (!endpoint || (req.user.role !== 'owner' && Number(endpoint.created_by) !== Number(req.user.id))) {
    res.flash('error', 'Endpoint tidak ditemukan atau akses ditolak.');
    return res.redirect('/admin/endpoints');
  }
  await db.run('DELETE FROM custom_endpoint_access WHERE endpoint_id=?', [req.params.id]);
  await db.run('DELETE FROM custom_endpoints WHERE id=?', [req.params.id]);
  res.flash('success', 'Endpoint berhasil dihapus.');
  res.redirect('/admin/endpoints');
});

router.post('/:id/access', auth, requireOwner, async (req, res) => {
  const resellerIds = Array.isArray(req.body.reseller_ids) ? req.body.reseller_ids : req.body.reseller_ids ? [req.body.reseller_ids] : [];
  const now = Math.floor(Date.now() / 1000);
  await db.run('DELETE FROM custom_endpoint_access WHERE endpoint_id=?', [req.params.id]);
  for (const resellerId of resellerIds) {
    await db.run(
      'INSERT INTO custom_endpoint_access (endpoint_id, reseller_id, can_use, can_create, created_at) VALUES (?, ?, 1, 0, ?)',
      [req.params.id, resellerId, now]
    );
  }
  res.flash('success', 'Akses endpoint reseller berhasil disimpan.');
  res.redirect('/admin/endpoints');
});

router.post('/resellers/:id/create-access', auth, requireOwner, async (req, res) => {
  await db.run('UPDATE users SET can_create_endpoint=?, updated_at=? WHERE id=? AND role=\'reseller\'', [
    req.body.can_create_endpoint === '1' ? 1 : 0,
    Math.floor(Date.now() / 1000),
    req.params.id
  ]);
  res.flash('success', 'Akses tambah endpoint reseller berhasil diupdate.');
  res.redirect('/admin/endpoints');
});

module.exports = router;
