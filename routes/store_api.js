const express = require('express');
const router = express.Router();
const { db } = require('../database');
const { verifyAndFulfillOrder } = require('../services/storeOrders');

// Polling endpoint: cek status pembayaran order.
router.get('/order/:id/status', async (req, res) => {
  try {
    return res.json(await verifyAndFulfillOrder(req.params.id));
  } catch (err) {
    console.error('Status check error:', err.message);
    res.json({ success: false, message: 'Error cek status' });
  }
});

// Referral check API.
router.post('/referral/check', async (req, res) => {
  try {
    const { code, productId } = req.body;
    if (!code) return res.json({ valid: false, reason: 'Kode kosong.' });

    const { rows } = await db.execute(`SELECT * FROM store_referrals WHERE code = ? AND is_active = 1`, [
      code.trim().toUpperCase()
    ]);
    if (!rows.length) return res.json({ valid: false, reason: 'Kode tidak ditemukan atau sudah tidak aktif.' });

    const ref = rows[0];
    if (ref.expired_at && new Date(ref.expired_at) < new Date()) {
      return res.json({ valid: false, reason: 'Kode referral sudah kedaluwarsa.' });
    }

    if (productId) {
      try {
        const allowed = JSON.parse(ref.allowed_products || '[]');
        if (allowed.length > 0 && !allowed.includes(Number(productId))) {
          return res.json({ valid: false, reason: 'Kode ini tidak berlaku untuk produk ini.' });
        }
      } catch (e) {}
    }

    return res.json({ valid: true, discount: ref.discount_amount });
  } catch (err) {
    console.error('Referral check error:', err);
    return res.json({ valid: false, reason: 'Kesalahan server.' });
  }
});

module.exports = router;
