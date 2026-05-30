const express = require('express');
const router = express.Router();
const { db } = require('../../database');
const { requireStoreAdmin } = require('./middleware');

router.get('/', requireStoreAdmin, async (req, res) => {
  try {
    const { rows: referrals } = await db.execute(`SELECT * FROM store_referrals ORDER BY created_at DESC`);
    const { rows: products } = await db.execute(`SELECT id, name FROM store_products ORDER BY name ASC`);
    res.render('store/admin/referrals', { referrals, products, success: req.query.success, error: req.query.error });
  } catch (err) {
    console.error('Get referrals error:', err.message);
    res.redirect('/admin/store');
  }
});

router.post('/add', requireStoreAdmin, async (req, res) => {
  try {
    const { code, discount_amount, expired_at, allowed_products } = req.body;
    if (!code || !discount_amount) return res.redirect('/admin/store/referrals?error=Kode+dan+Diskon+wajib+diisi');

    let expiry = null;
    if (expired_at && expired_at.trim() !== '') {
      const d = new Date(expired_at);
      if (!isNaN(d.getTime())) {
        expiry = d.toISOString();
      }
    }

    let allowedProductsJson = '[]';
    if (allowed_products) {
      const arr = Array.isArray(allowed_products) ? allowed_products : [allowed_products];
      allowedProductsJson = JSON.stringify(arr.map(Number));
    }

    await db.execute(
      `INSERT INTO store_referrals (code, discount_amount, expired_at, allowed_products, is_active) VALUES (?, ?, ?, ?, 1)`,
      [code.trim().toUpperCase(), parseInt(discount_amount), expiry, allowedProductsJson]
    );
    res.redirect('/admin/store/referrals?success=Kode+referral+berhasil+ditambahkan');
  } catch (err) {
    console.error('Add referral error:', err.message);
    res.redirect('/admin/store/referrals?error=Gagal+menambah+referral+(mungkin+kode+sudah+ada)');
  }
});

router.post('/:id/delete', requireStoreAdmin, async (req, res) => {
  try {
    await db.execute(`DELETE FROM store_referrals WHERE id = ?`, [req.params.id]);
    res.redirect('/admin/store/referrals?success=Referral+berhasil+dihapus');
  } catch (err) {
    console.error('Delete referral error:', err.message);
    res.redirect('/admin/store/referrals?error=Gagal+menghapus+referral');
  }
});

module.exports = router;
