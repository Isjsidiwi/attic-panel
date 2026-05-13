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

  router.post('/8bp', async (req, res) => {
    const userKey = (req.body.user_key || '').trim();
    const now = Math.floor(Date.now() / 1000);

    console.log("Body:", JSON.stringify(req.body, null, 2));
    console.log("Headers:", req.headers);

    res.setHeader('Content-Type', 'application/json; charset=UTF-8');
    res.setHeader('Cache-Control', 'no-store, max-age=0, no-cache');

    // Cek key availability
    if (!userKey) {
      return res.json({ status: false, reason: 'user_key diperlukan', data: null });
    }

    const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);
    if (!row) {
      return res.json({ status: false, reason: 'Key tidak ditemukan', data: null });
    }
    if (!row.is_active) {
      return res.json({ status: false, reason: 'Key dinonaktifkan', data: null });
    }
    if (Number(row.expires_at) <= now) {
      return res.json({ status: false, reason: 'Key sudah expired', data: null });
    }

    // Update login stats
    await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);

    // Return default response
    res.json({
      "data": "59f3a944c19775a22079378019820143eb431254b912e278842367ea6a4c5ce1a9d6f3a569c1e99aef074fd1f588e30f341516a8ca006b110e7002535374a6e136ccc00842a114579c5a7f9c8537d87b7ed84a35b653ee08e5dd3018a4cade669eadce09e3f11de1a0fc1c4270e2e05a9112b331920bda4d3d0de1915f2143f66aee2216ff7d7a9f06eeb971bc41089c09ae8920c7daa4cf",
      "sig": "9273719cfd063b446df735c8cc6913bf0bcf84f9bc037eedd168568dc66d7fdb71ce9f648ed8bf5f9cd868351cda433c44628b4744f97714f16bd81fd834eb7043fb5d60b5c115be37d44afcb697182b02e32fc27614914edbdb61ea9fdfb36d08ded09cdde905f4b34e99bff6f74833e1da3b3734a43159ce514503dd4e515dcfa93c26a379a1f2eeaaec530458fc6ea8735152e68b6528f50a9791a31df50a3cb9a6ee68be8660e3affe56b8f31d4baa9914caa25f7ef917fe4de274c0e9f088e59e879d0de42773d06e8312d2831bf43982e2b79da241ea18e24b1d22c5243684a3230d8479fc9b6608062badff9ad985f54d60af7323c5b251a1d138fc04",
      "tag": "b3724d8710147a18281e739ef22bc7a3"
    });
  });

router.post('/8bpx', (req, res) => {
    console.log("=== [BLOOD STRIKE] REQUEST DITERIMA ===");
    console.log("Body:", JSON.stringify(req.body, null, 2));
    console.log("Headers:", req.headers);

    // RESPONSE ORIGINAL YANG DIPAKAI MOD BLOOD STRIKE
    res.setHeader('Content-Type', 'application/json; charset=UTF-8');
    res.setHeader('Cache-Control', 'no-store, max-age=0, no-cache');

    res.json({
        "data": "59f3a944c19775a22079378019820143eb431254b912e278842367ea6a4c5ce1a9d6f3a569c1e99aef074fd1f588e30f341516a8ca006b110e7002535374a6e136ccc00842a114579c5a7f9c8537d87b7ed84a35b653ee08e5dd3018a4cade669eadce09e3f11de1a0fc1c4270e2e05a9112b331920bda4d3d0de1915f2143f66aee2216ff7d7a9f06eeb971bc41089c09ae8920c7daa4cf",
        "sig": "9273719cfd063b446df735c8cc6913bf0bcf84f9bc037eedd168568dc66d7fdb71ce9f648ed8bf5f9cd868351cda433c44628b4744f97714f16bd81fd834eb7043fb5d60b5c115be37d44afcb697182b02e32fc27614914edbdb61ea9fdfb36d08ded09cdde905f4b34e99bff6f74833e1da3b3734a43159ce514503dd4e515dcfa93c26a379a1f2eeaaec530458fc6ea8735152e68b6528f50a9791a31df50a3cb9a6ee68be8660e3affe56b8f31d4baa9914caa25f7ef917fe4de274c0e9f088e59e879d0de42773d06e8312d2831bf43982e2b79da241ea18e24b1d22c5243684a3230d8479fc9b6608062badff9ad985f54d60af7323c5b251a1d138fc04",
        "tag": "b3724d8710147a18281e739ef22bc7a3"
    });
});

  module.exports = router;