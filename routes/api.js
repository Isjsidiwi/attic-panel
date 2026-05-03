const express = require('express');
const router  = express.Router();
const crypto  = require('crypto');
const fs      = require('fs');
const path    = require('path');
const db      = require('../database');
const { loadConfig } = require('../config');

/* ═══════════════════════════════════════════════════
   FUNGSI UTAMA: PENANGANAN LOGIN & GENERATE TOKEN
   ═══════════════════════════════════════════════════ */
async function handleLogin(req, res, gameCode) {
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

  // Parse serials array
  let serials = [];
  try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
  const maxDevices = Number(row.max_devices) || 1;

  if (serial) {
    if (serials.includes(serial)) {
      // Serial sudah terdaftar — OK, lanjut login
    } else if (serials.length >= maxDevices) {
      return fail(`Batas device tercapai (${maxDevices}/${maxDevices}). Key ini sudah terkunci ke ${maxDevices} device.`);
    } else {
      // Device baru, masih ada slot — tambahkan
      serials.push(serial);
      await db.run(
        'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
        [JSON.stringify(serials), now, row.id]
      );
    }
  } else {
    await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
  }

  // Generate Token MD5
  const cfg = await loadConfig();
  const raw = `${gameCode}-${userKey}-${serial}-${resource}-${cfg.salt}`;
  const token = crypto.createHash('md5').update(raw).digest('hex');

  const expiredStr = new Date(Number(row.expires_at) * 1000)
    .toISOString().replace('T', ' ').slice(0, 19);

  // ⚠️ RESPONSE JSON LENGKAP (ANTI LOOPING / FORCE CLOSE) ⚠️
  res.json({
    status: true,
    reason: 'Login Success',
    data: { 
      Datte: "28-Mar-4764 02:15", 
      token: token, 
      rng: Number(row.expires_at), 
      tittle: "V3.5",       // Pastikan versi ini cocok dengan Mod
      versi: "1.1",         // Pastikan versi ini cocok dengan Mod
      instance: "Instance", 
      expired: "Unlimited"  // Atau bisa diganti dengan expiredStr
    }
  });
}

/* ═══════════════════════════════════════════════════
   ENDPOINT 1: MLBB
   ═══════════════════════════════════════════════════ */
// Rute Asli
router.post('/game/MLBB', async (req, res) => {
  await handleLogin(req, res, 'MLBB');
});

// Rute Cadangan (Jika path '/game' terpotong saat Hex Patching)
router.post('/MLBB', async (req, res) => {
  await handleLogin(req, res, 'MLBB');
});

/* ═══════════════════════════════════════════════════
   ENDPOINT 2: LGCY
   ═══════════════════════════════════════════════════ */
// Rute Asli
router.post('/game/LGCY', async (req, res) => {
  await handleLogin(req, res, 'LGCY');
});

// Rute Cadangan (Jika path '/game' terpotong saat Hex Patching)
router.post('/LGCY', async (req, res) => {
  await handleLogin(req, res, 'LGCY');
});

module.exports = router;
