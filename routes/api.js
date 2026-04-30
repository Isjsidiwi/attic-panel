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

  // RESPONSE YANG DIBUTUHKAN OLEH APLIKASI CHEAT
  res.json({
    success: true,
    seller: "lord",
    version: "1.0"
  });
});

module.exports = router;
