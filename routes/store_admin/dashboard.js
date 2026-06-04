const express = require('express');
const router = express.Router();
const { db } = require('../../database');
const { requireStoreAdmin } = require('./middleware');

router.get('/', requireStoreAdmin, async (req, res) => {
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
  res.render('store/admin/dashboard', { title: 'Manage Store', stats: stats[0], recentOrders });
});

module.exports = router;
