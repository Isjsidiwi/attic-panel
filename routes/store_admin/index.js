const express = require('express');
const router = express.Router();
const auth = require('../../middleware/auth'); // Import middleware pusat

const dashboardRoutes = require('./dashboard');
const productRoutes = require('./products');
const keyRoutes = require('./keys');
const orderRoutes = require('./orders');
const referralRoutes = require('./referrals');

// Semua route di bawah ini WAJIB melewati auth pusat agar req.user terisi
router.use(auth); 

router.use('/', dashboardRoutes);
router.use('/products', productRoutes);
router.use('/products/:id/keys', keyRoutes);
router.use('/orders', orderRoutes);
router.use('/referrals', referralRoutes);

module.exports = router;
