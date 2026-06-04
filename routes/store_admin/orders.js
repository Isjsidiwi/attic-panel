const express = require('express');
const router = express.Router();
const { db } = require('../../database');
const { requireStoreAdmin } = require('./middleware');

router.get('/', requireStoreAdmin, async (req, res) => {
  try {
    const status = req.query.status || '';
    let query = `SELECT o.*, p.name as product_name, k.key_value FROM store_orders o
                 JOIN store_products p ON p.id = o.product_id LEFT JOIN store_keys k ON k.id = o.key_id`;
    const params = [];
    if (status) {
      query += ` WHERE o.status = ?`;
      params.push(status);
    }
    query += ` ORDER BY o.created_at DESC LIMIT 100`;
    const { rows: orders } = await db.execute(query, params);
    res.render('store/admin/orders', { title: 'Manage Store', orders, status, success: req.query.success });
  } catch (err) {
    console.error('Get orders error:', err.message);
    res.redirect('/admin/store');
  }
});

router.post('/bulk-delete', requireStoreAdmin, async (req, res) => {
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

router.post('/prune', requireStoreAdmin, async (req, res) => {
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

module.exports = router;
