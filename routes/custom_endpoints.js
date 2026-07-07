const express = require('express');
const crypto = require('crypto');
const db = require('../database');

const router = express.Router();

function encryptionSecret() {
  return process.env.CUSTOM_ENDPOINT_SECRET || process.env.JWT_SECRET || 'attic-custom-endpoint-secret';
}

function encryptChaCha(payload, aad) {
  const key = crypto.createHash('sha256').update(encryptionSecret()).digest();
  const nonce = crypto.randomBytes(12);
  const cipher = crypto.createCipheriv('chacha20-poly1305', key, nonce, { authTagLength: 16 });
  cipher.setAAD(Buffer.from(aad));
  const encrypted = Buffer.concat([cipher.update(JSON.stringify(payload), 'utf8'), cipher.final()]);
  const tag = cipher.getAuthTag();
  return {
    status: true,
    encrypted: true,
    alg: 'qyz-c20p1305-v1',
    nonce: nonce.toString('base64url'),
    tag: tag.toString('base64url'),
    data: encrypted.toString('base64url')
  };
}

function requester(req) {
  const body = req.body || {};
  const query = req.query || {};
  return String(
    req.get('x-reseller-username') ||
      req.get('x-reseller-id') ||
      body.reseller_username ||
      body.reseller_id ||
      query.reseller_username ||
      query.reseller_id ||
      ''
  ).trim();
}

async function hasAccess(endpoint, req) {
  const access = await db.all('SELECT u.id, u.username FROM custom_endpoint_access cea JOIN users u ON u.id=cea.reseller_id WHERE cea.endpoint_id=? AND cea.can_use=1', [endpoint.id]);
  if (access.length === 0) return true;
  const who = requester(req).toLowerCase();
  if (!who) return false;
  return access.some((row) => String(row.id) === who || String(row.username).toLowerCase() === who);
}

router.all('*', async (req, res, next) => {
  const path = req.originalUrl.split('?')[0].replace(/\/+$/g, '').toLowerCase();
  const endpoint = await db.get('SELECT * FROM custom_endpoints WHERE path=? AND is_active=1', [path]);
  if (!endpoint) return next();
  if (endpoint.method !== 'ALL' && endpoint.method !== req.method.toUpperCase()) {
    return res.status(405).json({ status: false, reason: 'Method not allowed' });
  }
  if (!(await hasAccess(endpoint, req))) {
    return res.status(403).json({ status: false, reason: 'Reseller belum diberi akses endpoint ini.' });
  }
  let payload;
  try {
    payload = JSON.parse(endpoint.response_body || '{}');
  } catch {
    payload = { status: true };
  }
  res.set('Cache-Control', 'no-store, max-age=0, no-cache');
  if (endpoint.response_mode === 'chacha20-poly1305') return res.json(encryptChaCha(payload, endpoint.path));
  return res.json(payload);
});

module.exports = router;
