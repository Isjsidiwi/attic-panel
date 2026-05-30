const express = require('express');
const router = express.Router();
const bcrypt = require('bcryptjs');
const auth = require('../../middleware/auth');
const db = require('../../database');
const { loadConfig } = require('../../config');
const { fmtDate } = require('../../utils/adminUtils');

router.get('/', auth, async (req, res) => {
  const cfg = await loadConfig();
  const user = await db.get('SELECT * FROM users WHERE id=?', [req.user.id]);

  res.render('profile', {
    title: 'My Profile',
    panel_name: cfg.panel_name,
    user,
    fmtDate
  });
});

router.post('/', auth, async (req, res) => {
  const new_username = (req.body.new_username || '').trim();
  const old_password = req.body.old_password || '';
  const new_password = req.body.new_password || '';

  const user = await db.get('SELECT * FROM users WHERE id=?', [req.user.id]);
  const updates = [];
  const args = [];

  if (new_username && new_username !== user.username) {
    updates.push('username=?');
    args.push(new_username);
  }

  if (new_password) {
    if (!old_password) {
      res.flash('error', 'Masukkan password lama untuk mengubah password.');
      return res.redirect('/admin/profile');
    }
    if (!bcrypt.compareSync(old_password, user.password_hash)) {
      res.flash('error', 'Password lama salah.');
      return res.redirect('/admin/profile');
    }
    if (new_password.length < 6) {
      res.flash('error', 'Password baru minimal 6 karakter.');
      return res.redirect('/admin/profile');
    }
    updates.push('password_hash=?');
    args.push(bcrypt.hashSync(new_password, 10));
  }

  if (updates.length > 0) {
    args.push(req.user.id);
    try {
      await db.run(`UPDATE users SET ${updates.join(', ')} WHERE id=?`, args);
      res.flash('success', 'Profil berhasil diupdate.');
    } catch (err) {
      res.flash('error', 'Username sudah dipakai orang lain.');
    }
  }

  res.redirect('/admin/profile');
});

module.exports = router;
