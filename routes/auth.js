const express = require('express');
const router = express.Router();
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const rateLimit = require('express-rate-limit');
// In-memory tracker for failed login attempts per username (simple lockout)
const failedLogins = new Map();
const { loadConfig } = require('../config');
const db = require('../database');

const SECRET = () => process.env.JWT_SECRET || 'attic-jwt-fallback-secret';

const loginLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 5,
  skipSuccessfulRequests: true,
  handler: (req, res) => res.redirect('/login?error=Terlalu+banyak+percobaan.+Coba+lagi+15+menit+lagi.')
});

function homeFor(role) {
  return role === 'reseller' ? '/admin/keys' : '/admin/dashboard';
}

router.get('/', (req, res) => {
  const token = req.cookies._token;
  if (token) {
    try {
      const payload = jwt.verify(token, SECRET());
      return res.redirect(homeFor(payload.role));
    } catch {}
  }
  res.redirect('/login');
});

router.get('/login', async (req, res) => {
  const token = req.cookies._token;
  if (token) {
    try {
      const payload = jwt.verify(token, SECRET());
      return res.redirect(homeFor(payload.role));
    } catch {}
  }
  const cfg = await loadConfig();
  res.render('login', {
    title: 'Login',
    panel_name: cfg.panel_name || 'ATTIC PANEL',
    success_msg: [],
    error_msg: req.query.error ? [req.query.error] : [],
    show_reseller: req.query.show_reseller === '1',
    show_api_info: process.env.SHOW_API_INFO === '1'
  });
});

router.post('/login', loginLimiter, async (req, res) => {
  const username = (req.body.username || '').trim();
  const password = req.body.password || '';
  const cfg = await loadConfig();
  const user = await db.get('SELECT * FROM users WHERE username=?', [username]);

  const now = Math.floor(Date.now() / 1000);

  // Check simple username lockout
  if (username) {
    const info = failedLogins.get(username);
    if (info && info.lockedUntil && info.lockedUntil > Date.now()) {
      const mins = Math.ceil((info.lockedUntil - Date.now()) / 60000);
      return res.redirect('/login?error=' + encodeURIComponent(`Akun terkunci. Coba lagi setelah ${mins} menit.`));
    }
  }

  if (user && user.is_active && bcrypt.compareSync(password, user.password_hash)) {
    if (user.role === 'reseller' && cfg.maintenance_mode === '1') {
      return res.redirect('/login?error=Website+sedang+maintenance.+Hanya+admin+yang+bisa+login.');
    }

    if (user.expires_at && user.expires_at < now) {
      return res.redirect('/login?error=Akun+kamu+sudah+expired.');
    }

    const token = jwt.sign({ id: user.id, username: user.username, role: user.role }, SECRET(), { expiresIn: '24h' });
    res.cookie('_token', token, {
      httpOnly: true,
      maxAge: 24 * 60 * 60 * 1000,
      sameSite: 'lax',
      secure: process.env.NODE_ENV === 'production'
    });
    // clear failed attempts on successful login
    if (username) failedLogins.delete(username);
    return res.redirect(homeFor(user.role));
  }

  if (username === cfg.admin_username && bcrypt.compareSync(password, cfg.admin_password)) {
    const owner = await db.get("SELECT * FROM users WHERE role='owner' LIMIT 1");
    const token = jwt.sign({ id: owner && owner.id, username: cfg.admin_username, role: 'owner' }, SECRET(), {
      expiresIn: '24h'
    });
    res.cookie('_token', token, {
      httpOnly: true,
      maxAge: 24 * 60 * 60 * 1000,
      sameSite: 'lax',
      secure: process.env.NODE_ENV === 'production'
    });
    if (username) failedLogins.delete(username);
    return res.redirect('/admin/dashboard');
  }

  // increment failed attempt counter for username (simple lockout after 5 tries)
  if (username) {
    const info = failedLogins.get(username) || { count: 0, lockedUntil: null };
    info.count = (info.count || 0) + 1;
    if (info.count >= 5) {
      info.lockedUntil = Date.now() + 15 * 60 * 1000; // lock 15 minutes
      info.count = 0;
    }
    failedLogins.set(username, info);
  }

  res.redirect('/login?error=Username+atau+password+salah.&show_reseller=1');
});

router.post('/logout', (req, res) => {
  res.clearCookie('_token');
  res.redirect('/login');
});

module.exports = router;
