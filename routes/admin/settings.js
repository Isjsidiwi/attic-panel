const express = require('express');
const router = express.Router();
const bcrypt = require('bcryptjs');
const auth = require('../../middleware/auth');
const db = require('../../database');
const { loadConfig, saveConfig } = require('../../config');
const { fmtDate, getPriceMatrix, parseAllowedGames, PRICE_DAYS, GAME_OPTIONS } = require('../../utils/adminUtils');

const requireOwner = auth.requireOwner;

router.get('/', auth, requireOwner, async (req, res) => {
  const cfg = await loadConfig();
  const [resellers, priceMatrix] = await Promise.all([
    db.all(
      "SELECT id, username, credit, is_active, created_at, expires_at, allowed_games FROM users WHERE role='reseller' ORDER BY created_at DESC"
    ),
    getPriceMatrix()
  ]);

  res.render('settings', {
    title: 'Settings',
    panel_name: cfg.panel_name,
    cfg: { ...cfg, admin_password: '' },
    resellers: resellers.map((r) => ({ ...r, allowedGames: parseAllowedGames(r.allowed_games) })),
    priceMatrix,
    pricingDays: Array.from({ length: 30 }, (_, i) => i + 1),
    pricingGames: GAME_OPTIONS,
    fmtDate
  });
});

router.post('/', auth, requireOwner, async (req, res) => {
  const { panel_name, admin_username, new_password, confirm_password, maintenance_mode } = req.body;
  const updates = {};
  const userUpdates = {};

  if (panel_name) updates.panel_name = panel_name;
  if (maintenance_mode !== undefined) updates.maintenance_mode = maintenance_mode;
  if (admin_username) {
    updates.admin_username = admin_username.trim();
    userUpdates.username = admin_username.trim();
  }

  if (new_password) {
    if (new_password !== confirm_password) {
      res.flash('error', 'Konfirmasi password tidak cocok.');
      return res.redirect('/admin/settings');
    }
    if (new_password.length < 6) {
      res.flash('error', 'Password minimal 6 karakter.');
      return res.redirect('/admin/settings');
    }
    updates.admin_password = bcrypt.hashSync(new_password, 10);
    userUpdates.password_hash = updates.admin_password;
  }

  if (Object.keys(userUpdates).length > 0) {
    const sets = Object.keys(userUpdates)
      .map((k) => `${k}=?`)
      .join(', ');
    try {
      await db.run(`UPDATE users SET ${sets}, updated_at=? WHERE id=? AND role='owner'`, [
        ...Object.values(userUpdates),
        Math.floor(Date.now() / 1000),
        req.user.id
      ]);
    } catch (err) {
      res.flash('error', 'Username owner sudah dipakai akun lain.');
      return res.redirect('/admin/settings');
    }
  }

  await saveConfig(updates);
  res.flash('success', 'Settings berhasil disimpan.');
  res.redirect('/admin/settings');
});

module.exports = router;
