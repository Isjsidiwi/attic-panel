  const express = require('express');
  const router  = express.Router();
  const crypto  = require('crypto');
  const fs      = require('fs');
  const path    = require('path');
  const db      = require('../database');
  const { loadConfig } = require('../config');

  router.post('/game/MLBB', async (req, res) => {
    const userKey  = (req.body.user_key  || '').trim();
    const serial   = (req.body.serial    || '').trim();
    const resource = (req.body.resource  || '').trim();
    const now      = Math.floor(Date.now() / 1000);

    const fail = (reason) => res.json({ status: false, reason, data: null });

    if (!userKey) return fail('user_key diperlukan');

    const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);
    if (!row)           return fail('Key tidak ditemukan');
    if (!row.is_active) return fail('Key dinonaktifkan');
    if (Number(row.expires_at) <= now) return fail('Key sudah expired');

    let serials = [];
    try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
    const maxDevices = Number(row.max_devices) || 1;

    if (serial) {
      if (serials.includes(serial)) {
      } else if (serials.length >= maxDevices) {
        return fail(`Batas device tercapai (${maxDevices}/${maxDevices}). Key ini sudah terkunci ke ${maxDevices} device.`);
      } else {
        serials.push(serial);
        await db.run(
          'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
          [JSON.stringify(serials), now, row.id]
        );
      }
    } else {
      await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
    }

    const cfg = await loadConfig();
    const raw = `MLBB-${userKey}-${serial}-${resource}-${cfg.salt}`;
    const token = crypto.createHash('md5').update(raw).digest('hex');

    const expiredStr = new Date(Number(row.expires_at) * 1000)
      .toISOString().replace('T', ' ').slice(0, 19);

    res.json({
      status: true,
      reason: 'Login Success',
      data: { token, rng: Number(row.expires_at), tittle: 'Provided by Xsrc & Shannz', expired: expiredStr }
  });
  });


  router.post('/connect', (req, res) => {
      const { game, user_key, serial } = req.body;

      const response = {
          "status": true,
          "data": {
              "real": `BS-HEMORAX-VIP-${user_key ? user_key.substring(0, 8) : "CUSTOM"}-f4c61ab5-f04d-3300-b3e8-c1720ae56b64-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`,
              "token": "61a1c302db02026dc48c57f8eff693b3",
              "modname": "VIP MOD",
              "mod_status": "Safe",
              "credit": "110% SAFE",
              "ESP": "on",
              "Item": "on",
              "AIM": "on",
              "SilentAim": "on",
              "BulletTrack": "on",
              "Floating": "on",
              "Memory": "on",
              "Setting": "on",
              "expired_date": "2027-12-31 23:59:59",
              "EXP": "2027-12-31 23:59:59",
              "exdate": "2027-12-31 23:59:59",
              "device": "150",
              "rng": Math.floor(Math.random() * 9999999999)
          }
      };

      res.json(response);
  });

  module.exports = router;