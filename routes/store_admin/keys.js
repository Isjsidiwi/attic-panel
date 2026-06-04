const express = require('express');
const router = express.Router();
const { db } = require('../../database');
const { requireStoreAdmin } = require('./middleware');

router.get('/:id', requireStoreAdmin, async (req, res) => {
  try {
    const { rows: product } = await db.execute(`SELECT * FROM store_products WHERE id = ?`, [req.params.id]);
    if (!product.length) return res.redirect('/admin/store/products');

    const { rows: variants } = await db.execute(`SELECT * FROM store_product_variants WHERE product_id = ?`, [
      req.params.id
    ]);

    const { rows: keys } = await db.execute(
      `SELECT k.*, v.name as variant_name FROM store_keys k 
       LEFT JOIN store_product_variants v ON v.id = k.variant_id
       WHERE k.product_id = ? ORDER BY k.created_at DESC`,
      [req.params.id]
    );
    res.render('store/admin/keys', { title: 'Manage Store', product: product[0], variants, keys, success: req.query.success });
  } catch (err) {
    console.error('Get keys error:', err.message);
    res.redirect('/admin/store/products');
  }
});

router.post('/:id/add', requireStoreAdmin, async (req, res) => {
  try {
    const { keys_text, variant_id } = req.body;
    const lines = keys_text
      .split('\n')
      .map((l) => l.trim())
      .filter((l) => l.length > 0);
    for (const line of lines) {
      await db.execute(`INSERT INTO store_keys (product_id, variant_id, key_value) VALUES (?, ?, ?)`, [
        req.params.id,
        variant_id || null,
        line
      ]);
    }
    res.redirect(`/admin/store/products/${req.params.id}/keys?success=${lines.length}+key+ditambahkan`);
  } catch (err) {
    console.error('Add keys error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/keys?error=Gagal+menambah+key`);
  }
});

router.post('/:id/bulk-delete', requireStoreAdmin, async (req, res) => {
  try {
    let { key_ids } = req.body;
    if (!key_ids) return res.redirect(`/admin/store/products/${req.params.id}/keys`);
    if (!Array.isArray(key_ids)) key_ids = [key_ids];

    const placeholders = key_ids.map(() => '?').join(',');
    await db.execute(`UPDATE store_orders SET key_id = NULL WHERE key_id IN (${placeholders})`, key_ids);
    await db.execute(`DELETE FROM store_keys WHERE id IN (${placeholders})`, key_ids);

    res.redirect(`/admin/store/products/${req.params.id}/keys?success=${key_ids.length}+key+berhasil+dihapus`);
  } catch (err) {
    console.error('Bulk delete keys error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/keys?error=Gagal+menghapus+banyak+key`);
  }
});

router.post('/:id/:kid/delete', requireStoreAdmin, async (req, res) => {
  try {
    await db.execute(`UPDATE store_orders SET key_id = NULL WHERE key_id = ?`, [req.params.kid]);
    await db.execute(`DELETE FROM store_keys WHERE id = ?`, [req.params.kid]);
    res.redirect(`/admin/store/products/${req.params.id}/keys?success=Key+berhasil+dihapus`);
  } catch (err) {
    console.error('Delete key error:', err.message);
    res.redirect(`/admin/store/products/${req.params.id}/keys?error=Gagal+menghapus+key`);
  }
});

module.exports = router;
