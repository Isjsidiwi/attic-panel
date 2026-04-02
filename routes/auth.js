const express    = require('express');
const router     = express.Router();
const bcrypt     = require('bcryptjs');
const jwt        = require('jsonwebtoken');
const rateLimit  = require('express-rate-limit');
const { loadConfig } = require('../config');

const SECRET = () => process.env.JWT_SECRET || 'attic-jwt-fallback-secret';

const loginLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 10,
  skipSuccessfulRequests: true,
  handler: (req, res) => res.redirect('/login?error=Terlalu+banyak+percobaan.+Coba+lagi+15+menit+lagi.')
});

router.get('/', (req, res) => {
  const token = req.cookies._token;
  if (token) {
    try { jwt.verify(token, SECRET()); return res.redirect('/admin/dashboard'); } catch {}
  }
  res.redirect('/login');
});

router.get('/login', async (req, res) => {
  const token = req.cookies._token;
  if (token) {
    try { jwt.verify(token, SECRET()); return res.redirect('/admin/dashboard'); } catch {}
  }
  const cfg = await loadConfig();
  res.render('login', {
    title: 'Login',
    panel_name: cfg.panel_name || 'ATTIC PANEL',
    success_msg: [],
    error_msg: req.query.error ? [req.query.error] : []
  });
});

router.post('/login', loginLimiter, async (req, res) => {
  const { username, password } = req.body;
  const cfg = await loadConfig();

  if (username === cfg.admin_username && bcrypt.compareSync(password, cfg.admin_password)) {
    const token = jwt.sign({ username: cfg.admin_username }, SECRET(), { expiresIn: '24h' });
    res.cookie('_token', token, {
      httpOnly: true, maxAge: 24 * 60 * 60 * 1000, sameSite: 'lax', secure: process.env.NODE_ENV === 'production'
    });
    return res.redirect('/admin/dashboard');
  }

  res.redirect('/login?error=Username+atau+password+salah.');
});

router.post('/logout', (req, res) => {
  res.clearCookie('_token');
  res.redirect('/login');
});

module.exports = router;
