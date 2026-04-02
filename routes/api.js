const express = require('express');
const router  = express.Router();
const crypto  = require('crypto');
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
  if (!row)             return fail('Key tidak ditemukan');
  if (!row.is_active)   return fail('Key dinonaktifkan');
  if (Number(row.expires_at) <= now) return fail('Key sudah expired');

  if (row.device_serial && row.device_serial !== serial)
    return fail('Device tidak diizinkan — key ini sudah terkunci ke device lain');

  if (!row.device_serial && serial) {
    await db.run(
      'UPDATE keys SET device_serial=?, login_count=login_count+1, last_login=? WHERE id=?',
      [serial, now, row.id]
    );
  } else {
    await db.run(
      'UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?',
      [now, row.id]
    );
  }

  const cfg = await loadConfig();
  const raw = `MLBB-${userKey}-${serial}-${resource}-${cfg.salt}`;
  const token = crypto.createHash('md5').update(raw).digest('hex');

  const expiredStr = new Date(Number(row.expires_at) * 1000)
    .toISOString().replace('T', ' ').slice(0, 19);

  res.json({
    status: true,
    reason: 'Login Success',
    data: { token, rng: Number(row.expires_at), tittle: 'ATTIC MOD - 100% HACKED', expired: expiredStr }
  });
});

module.exports = router;
