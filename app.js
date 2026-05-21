const express = require('express');
const cookieParser = require('cookie-parser');
const session = require('cookie-session');
const methodOverride = require('method-override');
const path = require('path');
const { initDB } = require('./database');
const authRoutes = require('./routes/auth');
const adminRoutes = require('./routes/admin');
const apiRoutes = require('./routes/api');
const storeIndexRoutes = require('./routes/store_index');
const storeAdminRoutes = require('./routes/store_admin');
const storeApiRoutes = require('./routes/store_api');

const app = express();

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.use(express.urlencoded({ extended: true, limit: '500mb' }));
app.use(express.json({ limit: '500mb' }));
app.use(cookieParser());
app.use(session({
  name: 'suki_session',
  keys: [process.env.SESSION_SECRET || 'rajasuki-secret-key-123'],
  maxAge: 24 * 60 * 60 * 1000 // 24 jam
}));
app.use(methodOverride('_method'));
// Security headers to harden responses
app.use((req, res, next) => {
  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('X-Frame-Options', 'DENY');
  res.setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
  // Basic CSP - adjust if you add external script/font providers
  res.setHeader('Content-Security-Policy', "default-src 'self' https://cdn.tailwindcss.com; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; img-src 'self' data: https: http:; font-src 'self'; connect-src 'self'");
  if (process.env.NODE_ENV === 'production' || req.secure || req.headers['x-forwarded-proto'] === 'https') {
    res.setHeader('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
  }
  next();
});
app.use(express.static(path.join(__dirname, 'public')));

// Store specific locals
app.use((req, res, next) => {
  res.locals.storeName = process.env.STORE_NAME || 'XSRC-CLIENT';
  res.locals.storeTagline = process.env.STORE_TAGLINE || 'imgui';
  res.locals.isAdmin = (req.cookies._token) ? true : false; // Naive admin check for store frontend views
  next();
});

app.use((req, res, next) => {
  const raw = req.cookies._flash;
  if (raw) {
    try {
      const { type, msg } = JSON.parse(raw);
      res.locals.success_msg = type === 'success' ? [msg] : [];
      res.locals.error_msg = type === 'error' ? [msg] : [];
    } catch { res.locals.success_msg = []; res.locals.error_msg = []; }
    res.clearCookie('_flash');
  } else {
    res.locals.success_msg = [];
    res.locals.error_msg = [];
  }
  next();
});

app.use((req, res, next) => {
  res.flash = (type, msg) => {
    res.cookie('_flash', JSON.stringify({ type, msg }), {
      maxAge: 10000, httpOnly: false, path: '/', sameSite: 'lax'
    });
  };
  next();
});

app.use('/', authRoutes);
app.use('/admin', adminRoutes);
app.use('/api', apiRoutes);

// Store routes
app.use('/store', storeIndexRoutes);
app.use('/admin/store', storeAdminRoutes);
app.use('/api/store', storeApiRoutes);

// 404 handler
app.use((req, res) => {
  res.status(404).send(`
    <html><body style="background:#070b10;color:#00e5ff;font-family:monospace;display:flex;align-items:center;justify-content:center;height:100vh;flex-direction:column;gap:1rem;">
      <div style="font-size:4rem;font-weight:900;">404</div>
      <div>ENDPOINT NOT FOUND</div>
      <a href="/" style="color:#00ff88;text-decoration:none;">← BACK</a>
    </body></html>
  `);
});

// Init DB then start
initDB().catch(err => { console.error('DB init failed:', err); process.exit(1); });

const PORT = process.env.PORT || 3000;
if (require.main === module) {
  app.listen(PORT, () => {
    console.log(`\n ATTIC PANEL → http://localhost:${PORT}\n`);
  });
}

module.exports = app;
