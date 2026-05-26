const express = require('express');
const router = express.Router();
const { db } = require('../database');
const { createQRIS, generateUniqueSuffix } = require('../services/payment');
const { verifyAndFulfillOrder } = require('../services/storeOrders');
const { randomUUID: uuidv4 } = require('crypto');

// ── Beranda
router.get('/', async (req, res) => {
  try {
    const selectedCategory = (req.query.cat || '').trim();
    const q = (req.query.q || '').trim();
    const where = ['p.is_active = 1'];
    const params = [];

    if (selectedCategory && selectedCategory !== 'all') {
      where.push('p.category = ?');
      params.push(selectedCategory);
    }
    if (q) {
      where.push('(p.name LIKE ? OR p.description LIKE ? OR p.category LIKE ?)');
      params.push(`%${q}%`, `%${q}%`, `%${q}%`);
    }

    const productQuery = `
      SELECT p.*, 
        (SELECT COUNT(*) FROM store_keys k WHERE k.product_id = p.id AND k.is_used = 0) as stock,
        (SELECT MIN(price) FROM store_product_variants pv WHERE pv.product_id = p.id) as min_price,
        (SELECT MAX(price) FROM store_product_variants pv WHERE pv.product_id = p.id) as max_price,
        (SELECT COUNT(*) FROM store_product_variants pv WHERE pv.product_id = p.id) as variant_count
      FROM store_products p
      WHERE ${where.join(' AND ')}
      ORDER BY p.created_at DESC
      `;
    const { rows: products } = await db.execute(productQuery, params);

    const { rows: categories } = await db.execute(
      `SELECT DISTINCT category FROM store_products WHERE is_active = 1`
    );

    res.render('store/index', { products, categories, selectedCategory, q });
  } catch (err) {
    console.error(err);
    res.render('store/index', { products: [], categories: [], selectedCategory: '', q: '' });
  }
});

// ── Detail Produk
router.get('/produk/:slug', async (req, res) => {
  try {
    const { rows } = await db.execute(
      `SELECT p.* FROM store_products p WHERE p.slug = ? AND p.is_active = 1`,
      [req.params.slug]
    );
    if (!rows.length) return res.redirect('/store/');
    const product = rows[0];

    const { rows: variants } = await db.execute(
      `SELECT v.*, (SELECT COUNT(*) FROM store_keys k WHERE k.variant_id = v.id AND k.is_used = 0) as stock
       FROM store_product_variants v WHERE v.product_id = ?`,
      [product.id]
    );

    // Hitung total stok dari semua varian
    product.stock = variants.reduce((sum, v) => sum + v.stock, 0);

    res.render('store/product', { product, variants });
  } catch (err) {
    console.error(err);
    res.redirect('/store/');
  }
});

// ── Checkout: Form
router.get('/checkout/:slug/:variantId', async (req, res) => {
  try {
    const { rows: pRows } = await db.execute(
      `SELECT p.* FROM store_products p WHERE p.slug = ? AND p.is_active = 1`,
      [req.params.slug]
    );
    if (!pRows.length) return res.redirect('/store/');
    const product = pRows[0];

    const { rows: vRows } = await db.execute(
      `SELECT v.*, (SELECT COUNT(*) FROM store_keys k WHERE k.variant_id = v.id AND k.is_used = 0) as stock
       FROM store_product_variants v WHERE v.id = ? AND v.product_id = ?`,
      [req.params.variantId, product.id]
    );
    if (!vRows.length) return res.redirect('/store/produk/' + req.params.slug);
    const variant = vRows[0];

    if (variant.stock < 1) return res.redirect('/store/produk/' + req.params.slug);
    res.render('store/checkout', { product, variant, error: null });
  } catch (err) {
    console.error(err);
    res.redirect('/store/');
  }
});

// ── Checkout: Submit → Buat Order + QRIS
router.post('/checkout/:slug/:variantId', async (req, res) => {
  try {
    const { customer_name, customer_email, referral_code } = req.body;
    
    const { rows: pRows } = await db.execute(
      `SELECT p.* FROM store_products p WHERE p.slug = ? AND p.is_active = 1`,
      [req.params.slug]
    );
    if (!pRows.length) return res.redirect('/store/');
    const product = pRows[0];

    const { rows: vRows } = await db.execute(
      `SELECT v.*, (SELECT COUNT(*) FROM store_keys k WHERE k.variant_id = v.id AND k.is_used = 0) as stock
       FROM store_product_variants v WHERE v.id = ? AND v.product_id = ?`,
      [req.params.variantId, product.id]
    );
    if (!vRows.length) return res.redirect('/store/produk/' + req.params.slug);
    const variant = vRows[0];

    if (!customer_name || !customer_email) {
      return res.render('store/checkout', { product, variant, error: 'Nama dan email wajib diisi.' });
    }

    if (variant.stock < 1) return res.redirect('/store/produk/' + req.params.slug);

    let finalPrice = variant.price;
    let appliedDiscount = 0;
// Cek referral code jika ada
if (referral_code) {
  const { rows: refRows } = await db.execute(
    `SELECT * FROM store_referrals WHERE code = ? AND is_active = 1`,
    [referral_code.trim().toUpperCase()]
  );
  if (refRows.length > 0) {
    const ref = refRows[0];
    let isExpired = false;
    if (ref.expired_at && new Date(ref.expired_at) < new Date()) {
      isExpired = true;
    }

    let isProductAllowed = true;
    try {
      const allowed = JSON.parse(ref.allowed_products || '[]');
      if (allowed.length > 0 && !allowed.includes(product.id)) {
        isProductAllowed = false;
      }
    } catch(e) {}

    if (!isExpired && isProductAllowed) {
      appliedDiscount = ref.discount_amount;
    }
  }
}

    // Unique amount untuk verifikasi pembayaran
    const suffix = generateUniqueSuffix();
    const uniqueAmount = finalPrice + suffix;
    const orderId = uuidv4();

    // Buat QRIS
    let qrisId = null, qrisUrl = null;
    try {
      const qrisRes = await createQRIS(uniqueAmount);
      if (qrisRes?.qris_ajaib?.success) {
        qrisId = qrisRes.qris_ajaib.results.id;
        qrisUrl = qrisRes.qris_ajaib.results.qrcode_url;
      } else {
        console.error('QRIS API Error:', qrisRes);
        return res.render('store/checkout', { product, variant, error: 'Gagal membuat QRIS (API Error). Silakan coba lagi.' });
      }
    } catch (e) {
      console.error('QRIS create exception:', e.message);
      return res.render('store/checkout', { product, variant, error: 'Sistem pembayaran sedang gangguan. Coba beberapa saat lagi.' });
    }

    // Waktu bayar dibuat cukup longgar agar mutasi QRIS yang telat tetap sempat terbaca.
    const nowIso = new Date().toISOString();
    const expireMinutes = Math.max(5, Number(process.env.STORE_ORDER_EXPIRE_MINUTES || 10));
    const expiredAt = new Date(Date.now() + expireMinutes * 60 * 1000).toISOString();

    await db.execute(
      `INSERT INTO store_orders (id, product_id, variant_id, customer_name, customer_email, amount, unique_amount, unique_suffix, qris_id, qris_url, status, created_at, expired_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)`,
      [orderId, product.id, variant.id, customer_name, customer_email, finalPrice, uniqueAmount, suffix, qrisId, qrisUrl, nowIso, expiredAt]
    );

    res.redirect('/store/order/' + orderId);
  } catch (err) {
    console.error(err);
    res.redirect('/store/');
  }
});

// ── Halaman Status Order
router.get('/order/:id', async (req, res) => {
  try {
    await verifyAndFulfillOrder(req.params.id);

    const { rows } = await db.execute(
      `SELECT o.*, p.name as product_name, p.logo_url, p.slug,
              pv.name as variant_name,
              k.key_value
       FROM store_orders o
       JOIN store_products p ON p.id = o.product_id
       JOIN store_product_variants pv ON pv.id = o.variant_id
       LEFT JOIN store_keys k ON k.id = o.key_id
       WHERE o.id = ?`,
      [req.params.id]
    );
    if (!rows.length) return res.redirect('/store/');
    const order = rows[0];
    res.render('store/order', { order });
  } catch (err) {
    console.error(err);
    res.redirect('/store/');
  }
});

module.exports = router;
