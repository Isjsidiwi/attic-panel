const express = require('express');
const router = express.Router();
const db = require('../database');
const { loadConfig } = require('../config');

/* =============================================
   ENDPOINT MLBB - FULL RESPONSE SESUAI PARSER NATIVE
   ============================================= */
router.post('/game/MLBB', async (req, res) => {
  const userKey = (req.body.user_key || '').trim();
  const now = Math.floor(Date.now() / 1000);

  const fail = (reason) => res.json({ success: false, reason });

  if (!userKey) return fail('user_key diperlukan');

  const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);

  if (!row) return fail('Key tidak ditemukan');
  if (!row.is_active) return fail('Key dinonaktifkan');
  if (Number(row.expires_at) <= now) return fail('Key sudah expired');

  // RESPONSE INI YANG DIPAKAI PARSER NATIVE (nlohmann::json)
  res.json({
    "success": true,
    "session_token": "eaf8a86b70cc9b566a3a424a323d452b80ea02b77228a2c04e45d87c78455a2a",
    "name_key": userKey,
    "expiry_date": "2026-12-31 23:59:59",
    "remaining_devices": "99/100",
    "vip": "NO",
    "file_url": null,
    "version": "1.0",
    "announcement": "NEW UPDATE SOON ?",
    "seller": "lord"
  });
});

module.exports = router;
