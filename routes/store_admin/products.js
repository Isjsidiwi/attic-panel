const express = require('express');
const router = express.Router();
const { db } = require('../../database');
const { requireStoreAdmin } = require('./middleware');
const { slugify } = require('../../utils/stringUtils');

// --- Products List ---
router.get('/', requireStoreAdmin, async (req, res) => {
  try {
    const { rows: products } = await db.execute(`
      SELECT p.*, (SELECT COUNT(*) FROM store_keys k WHERE k.product_id = p.id AND k.is_used = 0) as stock
      FROM store_products p WHERE p.is_active = 1 ORDER BY p.created_at DESC
    `);
    res.render('store/admin/products', {
      title: 'Manage Store',
      products,
      success: req.query.success,
      error: req.query.error
    });
  } catch (err) {
    console.error('Get products error:', err.message);
    res.render('store/admin/products', {
      title: 'Manage Store',
      products: [],
      success: null,
      error: 'Gagal mengambil data produk'
    });
  }
});

// --- Add Product ---
router.post('/add', requireStoreAdmin, async (req, res) => {
  try {
    const { name, logo_url, description, category } = req.body;
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

// --- Edit Product Form ---
router.get('/:id/edit', requireStoreAdmin, async (req, res) => {
  try {
    const { rows } = await db.execute(`SELECT * FROM store_products WHERE id = ? AND is_active = 1`, [req.params.id]);
    if (!rows.length) return res.redirect('/admin/store/products');

    const { rows: variants } = await db.execute(
      `SELECT v.*, (SELECT COUNT(*) FROM store_keys k WHERE k.variant_id = v.id AND k.is_used = 0) as stock
       FROM store_product_variants v WHERE v.product_id = ?`,
      [req.params.id]
    );

    res.render('store/admin/product-edit', {
      title: 'Manage Store',
      product: rows[0],
      variants,
      success: req.query.success,
      error: null
    });
  } catch (err) {
    console.error('Get edit form error:', err.message);
    res.redirect('/admin/store/products');
  }
});

// --- Update Product ---
router.post('/:id/edit', requireStoreAdmin, async (req, res) => {
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

// --- Delete Product ---
router.post('/:id/delete', requireStoreAdmin, async (req, res) => {
  try {
    await db.execute(`UPDATE store_products SET is_active = 0 WHERE id = ?`, [req.params.id]);
    res.redirect('/admin/store/products?success=Produk+berhasil+dihapus');
  } catch (err) {
    console.error('Delete product error:', err.message);
    res.redirect('/admin/store/products?error=Gagal+menghapus+produk');
  }
});

// --- Variants Management ---
router.post('/:id/variants/add', requireStoreAdmin, async (req, res) => {
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

router.post('/:id/variants/:vid/delete', requireStoreAdmin, async (req, res) => {
  try {
    const variantId = req.params.vid;
    const productId = req.params.id;

    await db.execute(`UPDATE store_orders SET variant_id = NULL WHERE variant_id = ?`, [variantId]);
    await db.execute(
      `UPDATE store_orders SET key_id = NULL WHERE key_id IN (SELECT id FROM store_keys WHERE variant_id = ?)`,
      [variantId]
    );
    await db.execute(`DELETE FROM store_keys WHERE variant_id = ?`, [variantId]);
    await db.execute(`DELETE FROM store_product_variants WHERE id = ?`, [variantId]);

    res.redirect(`/admin/store/products/${productId}/edit?success=Varian+berhasil+dihapus`);
  } catch (err) {
    console.error('Delete variant error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/edit?error=Gagal+menghapus+varian`);
  }
});

router.post('/:id/variants/:vid/edit', requireStoreAdmin, async (req, res) => {
  try {
    const { name, price, original_price } = req.body;
    await db.execute(`UPDATE store_product_variants SET name = ?, price = ?, original_price = ? WHERE id = ?`, [
      name,
      parseInt(price),
      original_price ? parseInt(original_price) : null,
      req.params.vid
    ]);
    res.redirect(`/admin/store/products/${req.params.id}/edit?success=Varian+berhasil+diperbarui`);
  } catch (err) {
    console.error('Update variant error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/edit?error=Gagal+memperbarui+varian`);
  }
});

module.exports = router;
