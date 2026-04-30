const express = require('express');
const router = express.Router();
const db = require('../database');
const { loadConfig } = require('../config');

router.post('/game/MLBB', async (req, res) => {
  const userKey = (req.body.user_key || '').trim();
  const now = Math.floor(Date.now() / 1000);

  const fail = (reason) => res.json({ success: false, reason });

  if (!userKey) return fail('user_key diperlukan');

  const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);

  if (!row) return fail('Key tidak ditemukan');
  if (!row.is_active) return fail('Key dinonaktifkan');
  if (Number(row.expires_at) <= now) return fail('Key sudah expired');

  // RESPONSE PALING AMAN & BENAR UNTUK APLIKASI
  res.json({
    "success": true,
    "seller": "lord",
    "version": "1.0"
  });
});

module.exports = router;
