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

  function formatDateTime(unix) {
    const parts = new Intl.DateTimeFormat('en-CA', {
      timeZone: 'Asia/Jakarta',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hourCycle: 'h23'
    }).formatToParts(new Date(Number(unix) * 1000))
      .reduce((acc, part) => {
        if (part.type !== 'literal') acc[part.type] = part.value;
        return acc;
      }, {});

    return `${parts.year}-${parts.month}-${parts.day} ${parts.hour}:${parts.minute}:${parts.second}`;
  }

  // Masteredge Mod Features endpoint
  router.get('/mod_config', async (req, res) => {
    try {
      const cfg = await loadConfig();
      let modFeatures = {};
      try { modFeatures = JSON.parse(cfg.mod_features || '{}'); } catch(e) {}
      
      res.json({
        success: true,
        data: {
          panel_name: cfg.panel_name,
          mod_status: cfg.mod_status || 'online',
          maintenance_mode: cfg.maintenance_mode === '1',
          features: modFeatures
        }
      });
    } catch (err) {
      res.json({ success: false, error: 'Failed to load config' });
    }
  });

  // Telegram Webhook for Bot Commands
  router.post('/telegram/webhook', async (req, res) => {
    res.sendStatus(200); // Acknowledge immediately

    try {
      const cfg = await loadConfig();
      if (!cfg.telegram_bot_token || !cfg.telegram_chat_id) return;
      
      const update = req.body;
      if (!update || !update.message || !update.message.text) return;
      
      const chatId = update.message.chat.id.toString();
      // Only allow commands from the configured admin chat ID
      if (chatId !== cfg.telegram_chat_id && '@' + update.message.chat.username !== cfg.telegram_chat_id) return;

      const text = update.message.text.trim();
      const args = text.split(' ');
      const command = args[0].toLowerCase();
      
      const axios = require('axios');
      const sendMessage = async (msg) => {
        try {
          await axios.post(`https://api.telegram.org/bot${cfg.telegram_bot_token}/sendMessage`, {
            chat_id: chatId,
            text: msg,
            parse_mode: 'Markdown'
          });
        } catch(e) { console.error('Error sending msg:', e.message); }
      };

      if (command === '/reset') {
        const keyCode = args[1];
        if (!keyCode) return sendMessage('❌ *Format Salah*\nGunakan: `/reset [key_code]`');
        
        const key = await db.get('SELECT * FROM keys WHERE key_code = ?', [keyCode]);
        if (!key) return sendMessage('❌ Key tidak ditemukan.');
        
        await db.run('UPDATE keys SET device_serials = ? WHERE id = ?', ['[]', key.id]);
        sendMessage(`✅ *Device Reset Berhasil*\nKey: \`${keyCode}\` sekarang bisa digunakan di device baru.`);
      }
      
      if (command === '/gen') {
        const days = parseInt(args[1]) || 1;
        const game = (args[2] || 'BS').toUpperCase();
        const now = Math.floor(Date.now() / 1000);
        const expiresAt = now + (days * 86400);
        
        const CHARS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        const seg = () => Array.from({ length: 4 }, () => CHARS[Math.floor(Math.random() * CHARS.length)]).join('');
        const keyCode = `${game}-${seg()}-${seg()}-${seg()}`;
        
        const owner = await db.get("SELECT id, username FROM users WHERE role = 'owner' LIMIT 1");
        
        await db.run(
          'INSERT INTO keys (key_code, resource, device_serials, max_devices, created_at, expires_at, notes, created_by, created_by_username, price_paid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
          [keyCode, 'vip', '[]', 1, now, expiresAt, 'Generated from Telegram Bot', owner.id, owner.username, 0]
        );
        
        sendMessage(`✅ *Key Berhasil Digenerate*\n\nGame: ${game}\nKey: \`${keyCode}\`\nDurasi: ${days} Hari`);
      }
    } catch (e) {
      console.error('Telegram webhook error:', e);
    }
  });

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

  router.post('/game/mobile-legends', async (req, res) => {
    const userKey = (req.body.user_key || req.body.member_key || '').trim();
    const serial  = (req.body.serial || '').trim();
    const appVer  = (req.body.appVer || '').trim();
    const now     = Math.floor(Date.now() / 1000);

    const fail = (reason) => res.json({ status: false, reason, data: null });

    if (!userKey) return fail('user_key diperlukan');
    if (!serial)  return fail('serial diperlukan');

    const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);
    if (!row)           return fail('Key tidak ditemukan');
    if (!row.is_active) return fail('Key dinonaktifkan');
    if (Number(row.expires_at) <= now) return fail('Key sudah expired');

    let serials = [];
    try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
    const maxDevices = Number(row.max_devices) || 1;

    if (!serials.includes(serial)) {
      if (serials.length >= maxDevices) {
        return fail(`Batas device tercapai (${maxDevices}/${maxDevices})`);
      }
      serials.push(serial);
      await db.run(
        'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
        [JSON.stringify(serials), now, row.id]
      );
    } else {
      await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
    }

    // Best-effort token generation (MD5 of userKey + serial + gameName)
    const raw = `${userKey}${serial}ANTARXY`;
    const token = crypto.createHash('md5').update(raw).digest('hex');
    const expiredStr = new Date(Number(row.expires_at) * 1000).toISOString().replace('T', ' ').slice(0, 19);

    res.set({
      'Content-Type': 'application/json',
      'Cache-Control': 'no-store, no-cache, must-revalidate',
      'Pragma': 'no-cache'
    });

    res.json({
      status: true,
      data: {
        token: token,
        rng: now,
        expired: expiredStr,
        custom_title: "-Xive",
        version: appVer || "1.0.0",
        registrator: 5,
        btData: "BattleData"
      }
    });
  });

  router.post('/game/MLBB/antrxyz', async (req, res) => {
    // Alias for mobile-legends endpoint as requested
    const userKey = (req.body.user_key || req.body.member_key || '').trim();
    const serial  = (req.body.serial || '').trim();
    const appVer  = (req.body.appVer || '').trim();
    const now     = Math.floor(Date.now() / 1000);

    const fail = (reason) => res.json({ status: false, reason, data: null });

    if (!userKey) return fail('user_key diperlukan');
    if (!serial)  return fail('serial diperlukan');

    const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);
    if (!row)           return fail('Key tidak ditemukan');
    if (!row.is_active) return fail('Key dinonaktifkan');
    if (Number(row.expires_at) <= now) return fail('Key sudah expired');

    let serials = [];
    try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
    const maxDevices = Number(row.max_devices) || 1;

    if (!serials.includes(serial)) {
      if (serials.length >= maxDevices) {
        return fail(`Batas device tercapai (${maxDevices}/${maxDevices})`);
      }
      serials.push(serial);
      await db.run(
        'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
        [JSON.stringify(serials), now, row.id]
      );
    } else {
      await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
    }

    const raw = `${userKey}${serial}ANTARXY`;
    const token = crypto.createHash('md5').update(raw).digest('hex');
    const expiredStr = new Date(Number(row.expires_at) * 1000).toISOString().replace('T', ' ').slice(0, 19);

    res.json({
      status: true,
      data: {
        token: token,
        rng: now,
        expired: expiredStr,
        custom_title: "-Xive",
        version: appVer || "1.0.0",
        registrator: 5,
        btData: "BattleData"
      }
    });
  });

  router.post('/game/CFL', async (req, res) => {
    const userKey = (req.body.user_key || req.body.member_key || '').trim();
    const serial  = (req.body.serial || '').trim();
    const now     = Math.floor(Date.now() / 1000);

    const fail = (reason) => res.json({ status: false, reason, data: null });

    if (!userKey) return fail('user_key diperlukan');
    if (!serial)  return fail('serial diperlukan');

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

    const expiredStr = new Date(Number(row.expires_at) * 1000).toISOString().replace('T', ' ').slice(0, 19);

    res.set({
      'Content-Type': 'application/json',
      'Cache-Control': 'no-cache, private'
    });

    res.json({
      status: true,
      data: {
        token: "f7193b42781e210ddfcb4debb62c9837",
        rng: now,
        EXPR: expiredStr,
        vvipmodsgr: "QUh/k8wd+CfJxob7qKlIyMlHfxiauTXyjkN6258nbu0="
      }
    });
  });

  router.post('/game/freefire', async (req, res) => {
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

    const raw = `${memberKey}${serial}VVIPMODS`;
    const token = crypto.createHash('md5').update(raw).digest('hex');
    const expiredStr = formatIsoMicros(row.expires_at);

    res.set({
      'Content-Type': 'application/json',
      'Cache-Control': 'no-cache, private'
    });

    res.json({
      status: true,
      data: {
        token: token,
        rng: now,
        expired: expiredStr,
        EXPR: expiredStr,
        registrator: "Edge"
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

  router.post('/codm', async (req, res) => {
    const game    = (req.body.game || 'CODM').trim().toUpperCase();
    const userKey = (req.body.user_key || req.body.member_key || '').trim();
    const serial  = (req.body.serial || '').trim();
    const now     = Math.floor(Date.now() / 1000);

    const fail = (reason) => res.json({ status: false, reason, data: null });

    if (!userKey) return fail('user_key diperlukan');
    if (!serial)  return fail('serial diperlukan');

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
    const real = `${game}-${userKey}-${serial}-${cfg.salt}`;
    const token = crypto.createHash('md5').update(real).digest('hex');
    const expired = formatDateTime(row.expires_at);
    const slot = String(maxDevices);

    res.set('Cache-Control', 'no-store, max-age=0, no-cache');
    res.json({
      status: true,
      data: {
        real,
        token,
        modname: 'CFL MOD',
        mod_status: 'Safe',
        credit: 'Test',
        ESP: false,
        Item: false,
        AIM: false,
        SilentAim: false,
        BulletTrack: false,
        Floating: false,
        Memory: false,
        Setting: false,
        EXPR: expired,
        device: slot,
        MOD_NAME: 'CFL MOD',
        MOD_STATUS: 'Safe',
        FLOTING_TEST: 'Test',
        EXP: expired,
        SLOT: slot,
        cantcrack: '5DXuN61YKEgIhKNqa6PbJgf8DXm1Sft10sEEfFs9st8=',
        rng: now
      }
    });
  });

  module.exports = router;
