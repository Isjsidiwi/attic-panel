const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../../database');

function generateName() {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-';
  let name = '-';
  const bytes = crypto.randomBytes(19);
  for (let i = 0; i < 19; i++) {
    name += chars[bytes[i] % chars.length];
  }
  return name;
}

function toYYYYMMDD(unix) {
  const d = new Date(Number(unix) * 1000);
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return parseInt(`${y}${m}${day}`);
}

function getKeyCode(req) {
  return (req.query.key || '').trim();
}

async function resolveKey(keyCode) {
  if (!keyCode) return null;
  const key = await db.get('SELECT * FROM keys WHERE key_code = ?', [keyCode]);
  if (!key || !key.is_active) return null;
  const now = Math.floor(Date.now() / 1000);
  if (Number(key.expires_at) === 0) {
    const duration = Number(key.duration) || 0;
    const newExpiresAt = now + duration;
    await db.run('UPDATE keys SET expires_at = ? WHERE id = ?', [newExpiresAt, key.id]);
    key.expires_at = newExpiresAt;
  }
  return key;
}

async function deviceCount(keyCode) {
  const rows = await db.all(
    'SELECT name, device_id FROM valorant_device_ids WHERE key_code = ? ORDER BY created_at DESC',
    [keyCode]
  );
  return rows;
}

router.get('/license/login-auth', async (req, res) => {
  try {
    const key = await resolveKey(getKeyCode(req));
    if (!key) return res.json({ status: false, reason: 'key is invalid' });

    const devices = await deviceCount(key.key_code);
    const used = devices.length;

    res.json({
      date: toYYYYMMDD(key.expires_at),
      deviceIds: used === 0 ? '' : Object.fromEntries(devices.map(d => [d.name, d.device_id])),
      isShareable: Number(key.max_devices) > 1,
      maxDevice: Number(key.max_devices),
      time: '23:59',
      used,
      userKey: key.key_code
    });
  } catch (err) {
    console.error('[-] Valorant GET Error:', err.message);
    res.json({ status: false, reason: 'key is invalid' });
  }
});

router.post('/license/login-auth', async (req, res) => {
  try {
    const key = await resolveKey(getKeyCode(req));
    if (!key) return res.json({ status: false, reason: 'key is invalid' });

    const deviceId = (req.body && typeof req.body === 'object' && req.body.deviceId)
      ? String(req.body.deviceId).trim()
      : (typeof req.body === 'string' ? req.body.trim() : '');

    if (!deviceId) return res.json({ status: false, reason: 'key is invalid' });

    const devices = await deviceCount(key.key_code);
    if (devices.length >= Number(key.max_devices)) {
      return res.json({ status: false, reason: 'key is invalid' });
    }

    const name = generateName();
    const now = Math.floor(Date.now() / 1000);

    await db.run(
      'INSERT INTO valorant_device_ids (key_code, name, device_id, created_at) VALUES (?, ?, ?, ?)',
      [key.key_code, name, deviceId, now]
    );

    res.status(201).json({ name });
  } catch (err) {
    if (err.message && err.message.includes('UNIQUE')) {
      return res.json({ status: false, reason: 'key is invalid' });
    }
    console.error('[-] Valorant POST Error:', err.message);
    res.json({ status: false, reason: 'key is invalid' });
  }
});

router.patch('/license/login-auth', async (req, res) => {
  try {
    const key = await resolveKey(getKeyCode(req));
    if (!key) return res.json({ status: false, reason: 'key is invalid' });

    const devices = await deviceCount(key.key_code);

    res.json({ used: devices.length });
  } catch (err) {
    console.error('[-] Valorant PATCH Error:', err.message);
    res.json({ status: false, reason: 'key is invalid' });
  }
});

module.exports = router;
