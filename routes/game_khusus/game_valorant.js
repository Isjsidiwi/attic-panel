const express = require('express');
const router = express.Router();
const { loadConfig } = require('../../config');
const { validateAndRegisterKey } = require('../../services/gameAuth');

/**
 * GET /api/game/valorant
 * Valorant custom API endpoint dengan validasi key database & device management.
 * Query params: license (key), path (hwid/serial)
 */
router.get('/game/valorant', async (req, res) => {
  try {
    const userKey = (req.query.license || req.query.key || '').trim();
    const serial = (req.query.path || req.query.hwid || req.query.serial || '').trim();

    if (!userKey) {
      return res.status(200).json({
        status: false,
        reason: 'License/Key diperlukan.',
        data: null
      });
    }

    // Validasi & registrasi key dari database (sama seperti game lain)
    const auth = await validateAndRegisterKey(userKey, serial);
    if (!auth.success) {
      return res.status(200).json({
        status: false,
        reason: auth.reason,
        data: null
      });
    }

    const { key } = auth;
    const cfg = await loadConfig();

    // Config dari admin panel
    const date = cfg.valorant_date || '20261231';
    const time = cfg.valorant_time || '23:59';
    const isShareable = cfg.valorant_is_shareable === 'true';
    const deviceIdsStr = cfg.valorant_device_ids || '-OwX5RfHhAn6T7NN58f8:9ef0466f154a5926';

    const deviceIds = {};
    deviceIdsStr.split(',').forEach(pair => {
      const [k, v] = pair.split(':');
      if (k && v) deviceIds[k.trim()] = v.trim();
    });

    // Data dinamis dari key database
    const expiresAt = Number(key.expires_at);
    const maxDevices = Number(key.max_devices) || 1;
    let serials = [];
    try { serials = JSON.parse(key.device_serials || '[]'); } catch { serials = []; }
    const used = serials.length;

    res.json({
      status: true,
      data: {
        date: parseInt(date),
        deviceIds,
        isShareable,
        maxDevice: maxDevices,
        time,
        used,
        userKey,
        expiresAt,
        remainingSlot: Math.max(0, maxDevices - used)
      }
    });
  } catch (err) {
    console.error('[-] Valorant API Error:', err.message);
    res.status(500).json({ status: false, reason: 'Internal Server Error', data: null });
  }
});

module.exports = router;