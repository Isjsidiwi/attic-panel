const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../../database').db;
const { loadConfig } = require('../../config');

function generateName() {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-';
  let name = '-';
  const bytes = crypto.randomBytes(19);
  for (let i = 0; i < 19; i++) {
    name += chars[bytes[i] % chars.length];
  }
  return name;
}

function toDeviceIdsMap(rows) {
  const map = {};
  rows.forEach(r => { map[r.name] = r.device_id; });
  return map;
}

router.get('/game/valorant', async (req, res) => {
  try {
    const cfg = await loadConfig();
    const devices = await db.execute('SELECT name, device_id, created_at FROM valorant_device_ids ORDER BY created_at DESC');
    const rows = devices.rows || [];
    const used = rows.length;

    const payload = {
      date: parseInt(cfg.valorant_date || '20251231'),
      deviceIds: used === 0 ? '' : toDeviceIdsMap(rows),
      isShareable: (cfg.valorant_is_shareable || 'true') === 'true',
      maxDevice: parseInt(cfg.valorant_max_device || '500'),
      time: cfg.valorant_time || '23:59',
      used,
      userKey: cfg.valorant_user_key || 'KONTOL'
    };

    res.json(payload);
  } catch (err) {
    console.error('[-] Valorant GET Error:', err.message);
    res.status(500).json({ status: false, reason: 'Internal Server Error' });
  }
});

router.post('/game/valorant', async (req, res) => {
  try {
    const deviceId = (req.body && typeof req.body === 'object' && req.body.deviceId)
      ? String(req.body.deviceId).trim()
      : (typeof req.body === 'string' ? req.body.trim() : '');

    if (!deviceId) {
      return res.status(400).json({ error: 'deviceId required' });
    }

    const name = generateName();
    const now = Math.floor(Date.now() / 1000);

    await db.execute(
      'INSERT INTO valorant_device_ids (name, device_id, created_at) VALUES (?, ?, ?)',
      [name, deviceId, now]
    );

    res.status(201).json({ name });
  } catch (err) {
    if (err.message && err.message.includes('UNIQUE')) {
      return res.status(409).json({ error: 'Device already registered' });
    }
    console.error('[-] Valorant POST Error:', err.message);
    res.status(500).json({ status: false, reason: 'Internal Server Error' });
  }
});

router.patch('/game/valorant', async (req, res) => {
  res.json({ used: 0 });
});

module.exports = router;