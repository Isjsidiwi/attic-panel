const express    = require('express');
const router     = express.Router();
const bcrypt     = require('bcryptjs');
const jwt        = require('jsonwebtoken');
const rateLimit  = require('express-rate-limit');
const { loadConfig } = require('../config');
const db         = require('../database');

const SECRET = () => process.env.JWT_SECRET || 'attic-jwt-fallback-secret';

const loginLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 10,
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
    show_reseller: req.query.show_reseller === '1'
  });
});

router.post('/login', loginLimiter, async (req, res) => {
  const username = (req.body.username || '').trim();
  const password = req.body.password || '';
  const cfg = await loadConfig();
  const user = await db.get('SELECT * FROM users WHERE username=?', [username]);

  const now = Math.floor(Date.now() / 1000);

  if (user && user.is_active && bcrypt.compareSync(password, user.password_hash)) {
    if (user.role === 'reseller' && cfg.maintenance_mode === '1') {
      return res.redirect('/login?error=Website+sedang+maintenance.+Hanya+admin+yang+bisa+login.');
    }
    
    if (user.expires_at && user.expires_at < now) {
      return res.redirect('/login?error=Akun+kamu+sudah+expired.');
    }

    const token = jwt.sign(
      { id: user.id, username: user.username, role: user.role },
      SECRET(),
      { expiresIn: '24h' }
    );
    res.cookie('_token', token, {
      httpOnly: true, maxAge: 24 * 60 * 60 * 1000, sameSite: 'lax', secure: process.env.NODE_ENV === 'production'
    });
    return res.redirect(homeFor(user.role));
  }

  if (username === cfg.admin_username && bcrypt.compareSync(password, cfg.admin_password)) {
    const owner = await db.get("SELECT * FROM users WHERE role='owner' LIMIT 1");
    const token = jwt.sign(
      { id: owner && owner.id, username: cfg.admin_username, role: 'owner' },
      SECRET(),
      { expiresIn: '24h' }
    );
    res.cookie('_token', token, {
      httpOnly: true, maxAge: 24 * 60 * 60 * 1000, sameSite: 'lax', secure: process.env.NODE_ENV === 'production'
    });
    return res.redirect('/admin/dashboard');
  }

  res.redirect('/login?error=Username+atau+password+salah.&show_reseller=1');
});

router.post('/logout', (req, res) => {
  res.clearCookie('_token');
  res.redirect('/login');
});

module.exports = router;
