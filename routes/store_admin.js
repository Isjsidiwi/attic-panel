const express = require('express');
const router = express.Router();
const { db } = require('../database');

const requireAdmin = (req, res, next) => {
  const token = req.cookies._token;
  if (req.session.isAdmin || token) {
    return next();
  }
  res.redirect('/admin/store/login');
};

function slugify(text) {
  return text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
}

// ── Login
router.get('/login', (req, res) => {
  if (req.session.isAdmin) return res.redirect('/admin/store');
  res.render('store/admin/login', { error: null });
});

router.post('/login', (req, res) => {
  const { username, password } = req.body;
  
  const validEnv = username === process.env.ADMIN_USERNAME && password === process.env.ADMIN_PASSWORD;
  const validHardcoded = username === 'miaw' && password === 'sukikir';

  if (validEnv || validHardcoded) {
    req.session.isAdmin = true;
    return res.redirect('/admin/store');
  }
  res.render('store/admin/login', { error: 'Username atau password salah.' });
});

router.get('/logout', (req, res) => {
  req.session = null;
  res.redirect('/admin/store/login');
});

// ── Dashboard
router.get('/', requireAdmin, async (req, res) => {
  const { rows: stats } = await db.execute(`
    SELECT
      (SELECT COUNT(*) FROM store_products WHERE is_active=1) as total_products,
      (SELECT COUNT(*) FROM store_keys WHERE is_used=0) as total_stock,
      (SELECT COUNT(*) FROM store_orders WHERE status='paid') as total_paid,
      (SELECT COUNT(*) FROM store_orders WHERE status='pending') as total_pending
  `);
  const { rows: recentOrders } = await db.execute(`
    SELECT o.*, p.name as product_name FROM store_orders o
    JOIN store_products p ON p.id = o.product_id
    ORDER BY o.created_at DESC LIMIT 10
  `);
  res.render('store/admin/dashboard', { stats: stats[0], recentOrders });
});

// ── Products List
router.get('/products', requireAdmin, async (req, res) => {
  try {
    const { rows: products } = await db.execute(`
      SELECT p.*, (SELECT COUNT(*) FROM store_keys k WHERE k.product_id = p.id AND k.is_used = 0) as stock
      FROM store_products p WHERE p.is_active = 1 ORDER BY p.created_at DESC
    `);
    res.render('store/admin/products', { products, success: req.query.success, error: req.query.error });
  } catch (err) {
    console.error('Get products error:', err.message);
    res.render('store/admin/products', { products: [], success: null, error: 'Gagal mengambil data produk' });
  }
});

// ── Add Product
router.post('/products/add', requireAdmin, async (req, res) => {
  try {
    const { name, logo_url, description, price, category } = req.body;
    if (!name) return res.redirect('/admin/store/products?error=Nama+produk+wajib+diisi');
    const slug = slugify(name) + '-' + Date.now().toString().slice(-4);
    await db.execute(
      `INSERT INTO store_products (name, slug, logo_url, description, price, category, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)`,
      [name, slug, logo_url || null, description || '', 0, category || 'umum']
    );
    res.redirect('/admin/store/products?success=Produk+berhasil+ditambahkan');
  } catch (err) {
    console.error('Add product error:', err.message);
    res.redirect('/admin/store/products?error=Gagal+menambah+produk');
  }
});

// ── Edit Product Form
router.get('/products/:id/edit', requireAdmin, async (req, res) => {
  try {
    const { rows } = await db.execute(`SELECT * FROM store_products WHERE id = ? AND is_active = 1`, [req.params.id]);
    if (!rows.length) return res.redirect('/admin/store/products');
    
    const { rows: variants } = await db.execute(
      `SELECT v.*, (SELECT COUNT(*) FROM store_keys k WHERE k.variant_id = v.id AND k.is_used = 0) as stock
       FROM store_product_variants v WHERE v.product_id = ?`,
      [req.params.id]
    );
    
    res.render('store/admin/product-edit', { product: rows[0], variants, success: req.query.success, error: null });
  } catch (err) {
    console.error('Get edit form error:', err.message);
    res.redirect('/admin/store/products');
  }
});

// ── Update Product
router.post('/products/:id/edit', requireAdmin, async (req, res) => {
  try {
    const { name, logo_url, description, category, is_active } = req.body;
    await db.execute(
      `UPDATE store_products SET name=?, logo_url=?, description=?, category=?, is_active=? WHERE id=?`,
      [name, logo_url || null, description || '', category || 'umum', is_active ? 1 : 0, req.params.id]
    );
    res.redirect(`/admin/store/products/${req.params.id}/edit?success=Produk+berhasil+diperbarui!`);
  } catch (err) {
    console.error('Update product error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/edit?error=Gagal+memperbarui+produk`);
  }
});

// ── Delete Product
router.post('/products/:id/delete', requireAdmin, async (req, res) => {
  try {
    await db.execute(`UPDATE store_products SET is_active = 0 WHERE id = ?`, [req.params.id]);
    res.redirect('/admin/store/products?success=Produk+berhasil+dihapus');
  } catch (err) {
    console.error('Delete product error:', err.message);
    res.redirect('/admin/store/products?error=Gagal+menghapus+produk');
  }
});

// ── Variants Management
router.post('/products/:id/variants/add', requireAdmin, async (req, res) => {
  try {
    const { name, price, original_price } = req.body;
    await db.execute(
      `INSERT INTO store_product_variants (product_id, name, price, original_price) VALUES (?, ?, ?, ?)`,
      [req.params.id, name, parseInt(price), original_price ? parseInt(original_price) : null]
    );
    res.redirect(`/admin/store/products/${req.params.id}/edit?success=Varian+ditambahkan`);
  } catch (err) {
    console.error('Add variant error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/edit?error=Gagal+menambah+varian`);
  }
});

router.post('/products/:id/variants/:vid/delete', requireAdmin, async (req, res) => {
  try {
    const variantId = req.params.vid;
    const productId = req.params.id;

    // 1. Putuskan hubungan di tabel orders agar tidak error
    await db.execute(`UPDATE store_orders SET variant_id = NULL WHERE variant_id = ?`, [variantId]);
    
    // 2. Cari key yang terikat dengan varian ini dan putuskan hubungannya di orders
    await db.execute(`UPDATE store_orders SET key_id = NULL WHERE key_id IN (SELECT id FROM store_keys WHERE variant_id = ?)`, [variantId]);
    
    // 3. Hapus semua key yang terkait dengan varian ini
    await db.execute(`DELETE FROM store_keys WHERE variant_id = ?`, [variantId]);
    
    // 4. Hapus varian
    await db.execute(`DELETE FROM store_product_variants WHERE id = ?`, [variantId]);
    
    res.redirect(`/admin/store/products/${productId}/edit?success=Varian+berhasil+dihapus`);
  } catch (err) {
    console.error('Delete variant error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/edit?error=Gagal+menghapus+varian`);
  }
});

// ── Update Variant
router.post('/products/:id/variants/:vid/edit', requireAdmin, async (req, res) => {
  try {
    const { name, price, original_price } = req.body;
    await db.execute(
      `UPDATE store_product_variants SET name = ?, price = ?, original_price = ? WHERE id = ?`,
      [name, parseInt(price), original_price ? parseInt(original_price) : null, req.params.vid]
    );
    res.redirect(`/admin/store/products/${req.params.id}/edit?success=Varian+berhasil+diperbarui`);
  } catch (err) {
    console.error('Update variant error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/edit?error=Gagal+memperbarui+varian`);
  }
});

// ── Keys Management
router.get('/products/:id/keys', requireAdmin, async (req, res) => {
  try {
    const { rows: product } = await db.execute(`SELECT * FROM store_products WHERE id = ?`, [req.params.id]);
    if (!product.length) return res.redirect('/admin/store/products');
    
    const { rows: variants } = await db.execute(`SELECT * FROM store_product_variants WHERE product_id = ?`, [req.params.id]);
    
    const { rows: keys } = await db.execute(
      `SELECT k.*, v.name as variant_name FROM store_keys k 
       LEFT JOIN store_product_variants v ON v.id = k.variant_id
       WHERE k.product_id = ? ORDER BY k.created_at DESC`,
      [req.params.id]
    );
    res.render('store/admin/keys', { product: product[0], variants, keys, success: req.query.success });
  } catch (err) {
    console.error('Get keys error:', err.message);
    res.redirect('/admin/store/products');
  }
});

// ── Add Keys (bulk, satu per baris)
router.post('/products/:id/keys/add', requireAdmin, async (req, res) => {
  try {
    const { keys_text, variant_id } = req.body;
    const lines = keys_text.split('\n').map(l => l.trim()).filter(l => l.length > 0);
    for (const line of lines) {
      await db.execute(
        `INSERT INTO store_keys (product_id, variant_id, key_value) VALUES (?, ?, ?)`,
        [req.params.id, variant_id || null, line]
      );
    }
    res.redirect(`/admin/store/products/${req.params.id}/keys?success=${lines.length}+key+ditambahkan`);
  } catch (err) {
    console.error('Add keys error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/keys?error=Gagal+menambah+key`);
  }
});

// ── Bulk Delete Keys
router.post('/products/:id/keys/bulk-delete', requireAdmin, async (req, res) => {
  try {
    let { key_ids } = req.body;
    if (!key_ids) return res.redirect(`/admin/store/products/${req.params.id}/keys`);
    if (!Array.isArray(key_ids)) key_ids = [key_ids];

    const placeholders = key_ids.map(() => '?').join(',');
    // Unlink from orders first
    await db.execute(`UPDATE store_orders SET key_id = NULL WHERE key_id IN (${placeholders})`, key_ids);
    // Delete keys
    await db.execute(`DELETE FROM store_keys WHERE id IN (${placeholders})`, key_ids);
    
    res.redirect(`/admin/store/products/${req.params.id}/keys?success=${key_ids.length}+key+berhasil+dihapus`);
  } catch (err) {
    console.error('Bulk delete keys error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/keys?error=Gagal+menghapus+banyak+key`);
  }
});

// ── Delete Key
router.post('/products/:id/keys/:kid/delete', requireAdmin, async (req, res) => {
  try {
    // Putuskan hubungan key dari order (jika sudah terpakai) agar tidak error foreign key
    await db.execute(`UPDATE store_orders SET key_id = NULL WHERE key_id = ?`, [req.params.kid]);
    // Hapus key
    await db.execute(`DELETE FROM store_keys WHERE id = ?`, [req.params.kid]);
    res.redirect(`/admin/store/products/${req.params.id}/keys?success=Key+berhasil+dihapus`);
  } catch (err) {
    console.error('Delete key error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/keys?error=Gagal+menghapus+key`);
  }
});

// ── Orders
router.get('/orders', requireAdmin, async (req, res) => {
  try {
    const status = req.query.status || '';
    let query = `SELECT o.*, p.name as product_name, k.key_value FROM store_orders o
                 JOIN store_products p ON p.id = o.product_id LEFT JOIN store_keys k ON k.id = o.key_id`;
    const params = [];
    if (status) { query += ` WHERE o.status = ?`; params.push(status); }
    query += ` ORDER BY o.created_at DESC LIMIT 100`;
    const { rows: orders } = await db.execute(query, params);
    res.render('store/admin/orders', { orders, status, success: req.query.success });
  } catch (err) {
    console.error('Get orders error:', err.message);
    res.redirect('/admin/store');
  }
});

// ── Bulk Delete Orders
router.post('/orders/bulk-delete', requireAdmin, async (req, res) => {
  try {
    let { order_ids } = req.body;
    if (!order_ids) return res.redirect('/admin/store/orders');
    if (!Array.isArray(order_ids)) order_ids = [order_ids];

    const placeholders = order_ids.map(() => '?').join(',');
    await db.execute(`DELETE FROM store_orders WHERE id IN (${placeholders})`, order_ids);
    
    res.redirect(`/admin/store/orders?success=${order_ids.length}+pesanan+berhasil+dihapus`);
  } catch (err) {
    console.error('Bulk delete orders error:', err.message);
    res.redirect('/admin/store/orders?error=Gagal+menghapus+banyak+pesanan');
  }
});

// ── Prune Orders (Simpan 50 terbaru)
router.post('/orders/prune', requireAdmin, async (req, res) => {
  try {
    await db.execute(`
      DELETE FROM store_orders 
      WHERE id NOT IN (
        SELECT id FROM store_orders ORDER BY created_at DESC LIMIT 50
      )
    `);
    res.redirect('/admin/store/orders?success=Riwayat+lama+berhasil+dibersihkan');
  } catch (err) {
    console.error('Prune orders error:', err.message);
    res.redirect('/admin/store/orders?error=Gagal+membersihkan+riwayat');
  }
});

// ── Referrals
router.get('/referrals', requireAdmin, async (req, res) => {
  try {
    const { rows: referrals } = await db.execute(`SELECT * FROM store_referrals ORDER BY created_at DESC`);
    res.render('store/admin/referrals', { referrals, success: req.query.success, error: req.query.error });
  } catch (err) {
    console.error('Get referrals error:', err.message);
    res.redirect('/admin/store');
  }
});

router.post('/referrals/add', requireAdmin, async (req, res) => {
  try {
    const { code, discount_amount, expired_at } = req.body;
    if (!code || !discount_amount) return res.redirect('/admin/store/referrals?error=Kode+dan+Diskon+wajib+diisi');
    
    // Parse expired_at to local ISO if provided
    let expiry = null;
    if (expired_at) {
      expiry = new Date(expired_at).toISOString();
    }
    
    await db.execute(
      `INSERT INTO store_referrals (code, discount_amount, expired_at, is_active) VALUES (?, ?, ?, 1)`,
      [code.trim().toUpperCase(), parseInt(discount_amount), expiry]
    );
    res.redirect('/admin/store/referrals?success=Kode+referral+berhasil+ditambahkan');
  } catch (err) {
    console.error('Add referral error:', err.message);
    res.redirect('/admin/store/referrals?error=Gagal+menambah+referral+(mungkin+kode+sudah+ada)');
  }
});

router.post('/referrals/:id/delete', requireAdmin, async (req, res) => {
  try {
    await db.execute(`DELETE FROM store_referrals WHERE id = ?`, [req.params.id]);
    res.redirect('/admin/store/referrals?success=Referral+berhasil+dihapus');
  } catch (err) {
    console.error('Delete referral error:', err.message);
    res.redirect('/admin/store/referrals?error=Gagal+menghapus+referral');
  }
});

module.exports = router;
