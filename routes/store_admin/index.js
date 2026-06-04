const express = require('express');
const router = express.Router();
const auth = require('../../middleware/auth'); // Import middleware pusat
const { loadConfig } = require('../../config');

const dashboardRoutes = require('./dashboard');
const productRoutes = require('./products');
const keyRoutes = require('./keys');
const orderRoutes = require('./orders');
const referralRoutes = require('./referrals');

// Semua route di bawah ini WAJIB melewati auth pusat agar req.user terisi
router.use(auth); 

// Populate panel_name for the sidebar
router.use(async (req, res, next) => {
  const cfg = await loadConfig();
  res.locals.panel_name = cfg.panel_name || 'ATTIC PANEL';
  next();
});

router.use('/', dashboardRoutes);
router.use('/products', productRoutes);
router.use('/products/:id/keys', keyRoutes);
router.use('/orders', orderRoutes);
router.use('/referrals', referralRoutes);

module.exports = router;
