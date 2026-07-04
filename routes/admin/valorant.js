const express = require('express');
const router = express.Router();
const auth = require('../../middleware/auth');
const { loadConfig, saveConfig } = require('../../config');
const { all, run } = require('../../database');

const requireOwner = auth.requireOwner;

router.get('/', auth, requireOwner, async (req, res) => {
  const cfg = await loadConfig();
  const devices = await all('SELECT name, device_id, created_at FROM valorant_device_ids ORDER BY created_at DESC');
  res.render('valorant', {
    title: 'Valorant Config',
    panel_name: cfg.panel_name || 'ATTIC PANEL',
    cfg,
    devices,
    success_msg: res.locals.success_msg || [],
    error_msg:   res.locals.error_msg   || []
  });
});

router.post('/', auth, requireOwner, async (req, res) => {
  const {
    valorant_date,
    valorant_time,
    valorant_max_device,
    valorant_is_shareable,
    valorant_user_key
  } = req.body;

  await saveConfig({
    valorant_date:        (valorant_date        || '20251231').trim(),
    valorant_time:        (valorant_time        || '23:59').trim(),
    valorant_max_device:  (valorant_max_device  || '500').trim(),
    valorant_is_shareable: valorant_is_shareable ? 'true' : 'false',
    valorant_user_key:    (valorant_user_key    || 'KONTOL').trim()
  });

  res.flash('success', 'Konfigurasi Valorant berhasil disimpan.');
  res.redirect('/admin/valorant');
});

router.post('/:id/delete-device', auth, requireOwner, async (req, res) => {
  await run('DELETE FROM valorant_device_ids WHERE id = ?', [req.params.id]);
  res.flash('success', 'Device berhasil dihapus.');
  res.redirect('/admin/valorant');
});

module.exports = router;