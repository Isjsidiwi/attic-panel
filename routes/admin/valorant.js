const express = require('express');
const router = express.Router();
const auth = require('../../middleware/auth');
const { loadConfig, saveConfig } = require('../../config');

const requireOwner = auth.requireOwner;

// GET /admin/valorant — Tampilkan halaman konfigurasi Valorant
router.get('/', auth, requireOwner, async (req, res) => {
  const cfg = await loadConfig();
  res.render('valorant', {
    title: 'Valorant Config',
    panel_name: cfg.panel_name || 'ATTIC PANEL',
    cfg,
    success_msg: res.locals.success_msg || [],
    error_msg:   res.locals.error_msg   || []
  });
});

// POST /admin/valorant — Simpan konfigurasi Valorant
router.post('/', auth, requireOwner, async (req, res) => {
  const {
    valorant_date,
    valorant_time,
    valorant_max_device,
    valorant_is_shareable,
    valorant_user_key,
    valorant_used,
    valorant_device_ids
  } = req.body;

  await saveConfig({
    valorant_date:        (valorant_date        || '20261231').trim(),
    valorant_time:        (valorant_time        || '23:59').trim(),
    valorant_max_device:  (valorant_max_device  || '500').trim(),
    valorant_is_shareable: valorant_is_shareable ? 'true' : 'false',
    valorant_user_key:    (valorant_user_key    || 'tes').trim(),
    valorant_used:        (valorant_used        || '1').trim(),
    valorant_device_ids:  (valorant_device_ids  || '-OwX5RfHhAn6T7NN58f8:9ef0466f154a5926').trim()
  });

  res.flash('success', 'Konfigurasi Valorant berhasil disimpan.');
  res.redirect('/admin/valorant');
});

module.exports = router;