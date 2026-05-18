  const express = require('express');
  const router  = express.Router();
  const crypto  = require('crypto');
  const db      = require('../database');
  const { loadConfig } = require('../config');

  const monthNamesId = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

  function formatDateId(date) {
    const d = date.getDate().toString().padStart(2, '0');
    const m = monthNamesId[date.getMonth()];
    const y = date.getFullYear();
    const hh = date.getHours().toString().padStart(2, '0');
    const mm = date.getMinutes().toString().padStart(2, '0');
    return `${d} - ${m} - ${y} ${hh}:${mm}`;
  }

  function formatIsoMicros(unix) {
    return new Date(Number(unix) * 1000)
      .toISOString()
      .replace(/\.(\d{3})Z$/, (_, ms) => `.${ms}000Z`);
  }

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

  router.post('/game/8bp', async (req, res) => {
    const userKey = (req.body.user_key || '').trim();
    const serial  = (req.body.serial   || '').trim();
    const game    = (req.body.game     || '').trim();
    const now     = Math.floor(Date.now() / 1000);

    const fail = (reason) => res.json({ status: false, reason, data: null });

    if (!userKey) return fail('user_key diperlukan');
    if (!serial)  return fail('serial diperlukan');
    if (!game)    return fail('game diperlukan');

    const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);
    if (!row)           return fail('Key tidak ditemukan');
    if (!row.is_active) return fail('Key dinonaktifkan');
    if (Number(row.expires_at) <= now) return fail('Key sudah expired');

    let serials = [];
    try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
    const maxDevices = Number(row.max_devices) || 1;

    if (!serials.includes(serial)) {
      if (serials.length >= maxDevices) {
        return fail(`Batas device tercapai (${maxDevices}/${maxDevices}). Key ini sudah terkunci ke ${maxDevices} device.`);
      }
      serials.push(serial);
      await db.run(
        'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
        [JSON.stringify(serials), now, row.id]
      );
    } else {
      await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
    }

    const cfg = await loadConfig();
    const raw = `8BP-${userKey}-${serial}-${cfg.salt}`;
    const token = crypto.createHash('md5').update(raw).digest('hex');
    const dateNow = new Date();
    const formattedDate = formatDateId(dateNow);
    const expireDate = new Date(Number(row.expires_at) * 1000);
    const formattedExpired = formatDateId(expireDate);

    res.json({
      status: true,
      data: {
        Datte: formattedDate,
        token,
        rng: Number(row.expires_at) || Math.floor(Math.random() * 9000000000) + 1000000000,
        tittle: ' | Easyvictors',
        instance: 'Instance',
        expired: formattedExpired
      }
    });
  });

  router.post('/game/pubgm', async (req, res) => {
    const memberKey = (req.body.member_key || req.body.user_key || '').trim();
    const serial    = (req.body.serial     || '').trim();
    const now       = Math.floor(Date.now() / 1000);

    const fail = (reason) => res.json({ status: false, reason, data: null });

    if (!memberKey) return fail('member_key diperlukan');
    if (!serial)    return fail('serial diperlukan');

    const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [memberKey]);
    if (!row)           return fail('Key tidak ditemukan');
    if (!row.is_active) return fail('Key dinonaktifkan');
    if (Number(row.expires_at) <= now) return fail('Key sudah expired');

    let serials = [];
    try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
    const maxDevices = Number(row.max_devices) || 1;

    if (!serials.includes(serial)) {
      if (serials.length >= maxDevices) {
        return fail(`Batas device tercapai (${maxDevices}/${maxDevices}). Key ini sudah terkunci ke ${maxDevices} device.`);
      }
      serials.push(serial);
      await db.run(
        'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
        [JSON.stringify(serials), now, row.id]
      );
    } else {
      await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
    }

    const expired = formatIsoMicros(row.expires_at);

    res.json({
      status: true,
      data: {
        token: 'e57e308605a4dc7f5da27a8a63dc0645',
        rng: Number(row.expires_at),
        expired,
        EXPR: expired,
        registrator: 'Edge'
      }
    });
  });

  router.post('/connect', async (req, res) => {
    const userKey = (req.body.user_key || '').trim();
    const serial  = (req.body.serial   || '').trim();
    const now     = Math.floor(Date.now() / 1000);

    const fail = (reason) => res.json({ status: false, reason, data: null });

    if (!userKey) return fail('user_key diperlukan');

    // Cari key di database
    const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);
    if (!row)           return fail('Key tidak ditemukan');
    if (!row.is_active) return fail('Key dinonaktifkan');
    if (Number(row.expires_at) <= now) return fail('Key sudah expired');

    // Parse device serials
    let serials = [];
    try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
    const maxDevices = Number(row.max_devices) || 1;

    // Validasi device serial dan update jika perlu
    if (serial) {
      if (!serials.includes(serial)) {
        if (serials.length >= maxDevices) {
          return fail(`Batas device tercapai (${maxDevices}/${maxDevices}). Key ini sudah terkunci ke ${maxDevices} device.`);
        }
        serials.push(serial);
        await db.run(
          'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
          [JSON.stringify(serials), now, row.id]
        );
      } else {
        // Serial sudah terdaftar, hanya update login_count & last_login
        await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
      }
    } else {
      // Tanpa serial, update login_count & last_login
      await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
    }

    // Generate response
    const response = {
        "status": true,
        "data": {
            "real": `${userKey}-f4c61ab5-f04d-3300-b3e8-c1720ae56b64-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`,
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
            "device": String(maxDevices),
            "rng": Math.floor(Math.random() * 9999999999)
        }
    };

    res.json(response);
  });

  module.exports = router;
