const express = require('express');
const router = express.Router();

const dashboardRoutes = require('./dashboard');
const keyRoutes = require('./keys');
const settingRoutes = require('./settings');
const resellerRoutes = require('./resellers');
const priceRoutes = require('./prices');
const profileRoutes = require('./profile');
const fileRoutes = require('./files');
const endpointRoutes = require('./endpoints');
router.use('/dashboard', dashboardRoutes);
router.use('/keys', keyRoutes);
router.use('/settings', settingRoutes);
router.use('/resellers', resellerRoutes);
router.use('/prices', priceRoutes);
router.use('/profile', profileRoutes);
router.use('/files', fileRoutes);
router.use('/endpoints', endpointRoutes);

module.exports = router;
