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
  const { panel_name, admin_username, new_password, confirm_password, salt, maintenance_mode } = req.body;
  const updates = {};
  const userUpdates = {};

  if (panel_name) updates.panel_name = panel_name;
  if (maintenance_mode !== undefined) updates.maintenance_mode = maintenance_mode;
  if (admin_username) {
    updates.admin_username = admin_username.trim();
    userUpdates.username = admin_username.trim();
  }
  if (salt) updates.salt = salt;

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

router.post('/xsrc', auth, requireOwner, async (req, res) => {
  const { telegram_bot_token, telegram_chat_id, mod_status, feat_esp, feat_aimbot, feat_silent_aim, feat_memory } =
    req.body;
  const updates = {};

  if (telegram_bot_token !== undefined) updates.telegram_bot_token = telegram_bot_token.trim();
  if (telegram_chat_id !== undefined) updates.telegram_chat_id = telegram_chat_id.trim();
  if (mod_status !== undefined) updates.mod_status = mod_status;

  const modFeatures = {
    esp: feat_esp === '1',
    aimbot: feat_aimbot === '1',
    silent_aim: feat_silent_aim === '1',
    memory: feat_memory === '1'
  };
  updates.mod_features = JSON.stringify(modFeatures);

  await saveConfig(updates);
  res.flash('success', 'Masteredge Settings berhasil disimpan.');
  res.redirect('/admin/settings');
});

module.exports = router;
