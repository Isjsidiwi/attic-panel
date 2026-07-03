const express = require('express');
const router = express.Router();
const { loadConfig } = require('../../config');

/**
 * GET /api/game/valorant
 * Valorant custom API endpoint dengan respon yang dapat dikonfigurasi via admin panel.
 * Query params: license, path
 */
router.get('/game/valorant', async (req, res) => {
  try {
    const { license, path } = req.query;

    const cfg = await loadConfig();

    const date = cfg.valorant_date || '20261231';
    const time = cfg.valorant_time || '23:59';
    const maxDevice = parseInt(cfg.valorant_max_device) || 500;
    const isShareable = cfg.valorant_is_shareable === 'true';
    const userKey = cfg.valorant_user_key || 'tes';
    const used = parseInt(cfg.valorant_used) || 1;
    const deviceIdsStr = cfg.valorant_device_ids || '-OwX5RfHhAn6T7NN58f8:9ef0466f154a5926';

    const deviceIds = {};
    deviceIdsStr.split(',').forEach(pair => {
      const [key, value] = pair.split(':');
      if (key && value) deviceIds[key.trim()] = value.trim();
    });

    res.json({
      date: parseInt(date),
      deviceIds,
      isShareable,
      maxDevice,
      time,
      used,
      userKey
    });
  } catch (err) {
    console.error('[-] Valorant API Error:', err.message);
    res.status(500).json({ status: false, reason: 'Internal Server Error', data: null });
  }
});

module.exports = router;