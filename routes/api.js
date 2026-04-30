const express = require('express');
const router = express.Router();
const db = require('../database');
const { loadConfig } = require('../config');

/* =============================================
   ENDPOINT MLBB - VERSI BERSIH & BENAR
   ============================================= */
router.post('/game/MLBB', async (req, res) => {
  const userKey = (req.body.user_key || '').trim();
  const serial   = (req.body.serial || '').trim();
  const now = Math.floor(Date.now() / 1000);

  const fail = (reason) => res.json({ success: false, reason });

  if (!userKey) return fail('user_key diperlukan');

  const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);

  if (!row) return fail('Key tidak ditemukan');
  if (!row.is_active) return fail('Key dinonaktifkan');
  if (Number(row.expires_at) <= now) return fail('Key sudah expired');

  // Device limit (opsional)
  let serials = [];
  try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
  const maxDevices = Number(row.max_devices) || 1;

  if (serial && !serials.includes(serial)) {
    if (serials.length >= maxDevices) {
      return fail(`Batas device tercapai (${maxDevices}/${maxDevices})`);
    }
    serials.push(serial);
    await db.run(
      'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
      [JSON.stringify(serials), now, row.id]
    );
  } else if (!serial) {
    await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
  }

 // RESPONSE INI YANG PALING AMAN UNTUK APLIKASI CHEAT
res.json({
  "success": true,
  "session_token": "eaf8a86b70cc9b566a3a424a323d452b80ea02b77228a2c04e45d87c78455a2a",
  "name_key": "ATTIC-4N3E-J2EB-CG8Q",
  "expiry_date": "2026-12-31 23:59:59",
  "remaining_devices": "99/100",
  "vip": "NO",
  "file_url": null,
  "version": "1.0",
  "announcement": "Welcome to Attic Panel",
  "seller": "lord"
});

module.exports = router;
