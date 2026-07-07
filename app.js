const express = require('express');
const cookieParser = require('cookie-parser');
const session = require('cookie-session');
const methodOverride = require('method-override');
const path = require('path');
const jwt = require('jsonwebtoken');
const { initDB } = require('./database');
const authRoutes = require('./routes/auth');
const adminRoutes = require('./routes/admin/index');
const apiRoutes = require('./routes/api');
const hgModsRoutes = require('./routes/game_khusus/game_hgmods');
const brModsRoutes = require('./routes/game_khusus/game_brmods');
const valorantRoutes = require('./routes/game_khusus/game_valorant');
const storeIndexRoutes = require('./routes/store_index');
const storeAdminRoutes = require('./routes/store_admin/index');
const storeApiRoutes = require('./routes/store_api');
const customEndpointRoutes = require('./routes/custom_endpoints');

const app = express();

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.use(express.urlencoded({ extended: true, limit: '500mb' }));
app.use(express.json({ limit: '500mb', strict: false }));
app.use(cookieParser());
app.use(
  session({
    name: 'suki_session',
    keys: [process.env.SESSION_SECRET || 'rajasuki-secret-key-123'],
    maxAge: 24 * 60 * 60 * 1000 // 24 jam
  })
);
app.use(methodOverride('_method'));
// Security headers to harden responses
app.use((req, res, next) => {
  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('X-Frame-Options', 'DENY');
  res.setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
  res.setHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
  res.setHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
  // Basic CSP - adjust if you add external script/font providers
  res.setHeader(
    'Content-Security-Policy',
    "default-src 'self' https://cdn.tailwindcss.com; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://fonts.googleapis.com; img-src 'self' data: https: http:; font-src 'self' https://fonts.gstatic.com; connect-src 'self'"
  );
  if (process.env.NODE_ENV === 'production' || req.secure || req.headers['x-forwarded-proto'] === 'https') {
    res.setHeader('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
  }
  next();
});
app.use(express.static(path.join(__dirname, 'public')));

// Store specific locals
app.use((req, res, next) => {
  res.locals.storeName = process.env.STORE_NAME || 'XSRC';
  res.locals.storeTagline = process.env.STORE_TAGLINE || 'xsrc cheat store';
  res.locals.currentUrl = req.originalUrl;
  res.locals.isAdmin = false;
  if (req.cookies._token) {
    try {
      const payload = jwt.verify(req.cookies._token, process.env.JWT_SECRET || 'attic-jwt-fallback-secret');
      res.locals.isAdmin = payload.role === 'owner';
    } catch {}
  }
  next();
});

app.use((req, res, next) => {
  const raw = req.cookies._flash;
  if (raw) {
    try {
      const { type, msg } = JSON.parse(raw);
      res.locals.success_msg = type === 'success' ? [msg] : [];
      res.locals.error_msg = type === 'error' ? [msg] : [];
    } catch {
      res.locals.success_msg = [];
      res.locals.error_msg = [];
    }
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
      maxAge: 10000,
      httpOnly: false,
      path: '/',
      sameSite: 'lax'
    });
  };
  next();
});

function safeJson(data) {
  return JSON.stringify(data).replace(/</g, '\\u003c').replace(/>/g, '\\u003e').replace(/&/g, '\\u0026');
}

function stripPrivateData(value) {
  if (Array.isArray(value)) return value.map(stripPrivateData);
  if (!value || typeof value !== 'object') return value;
  const out = {};
  for (const [key, item] of Object.entries(value)) {
    if (/password|password_hash|token|secret/i.test(key)) continue;
    out[key] = stripPrivateData(item);
  }
  return out;
}

app.use((req, res, next) => {
  const originalRender = res.render.bind(res);
  res.render = (view, locals = {}, callback) => {
    if (view === 'react-shell') return originalRender(view, locals, callback);
    const data = {
      view,
      currentUrl: req.originalUrl,
      user: req.user || res.locals.admin || null,
      isStoreAdmin: Boolean(res.locals.isStoreAdmin),
      success_msg: res.locals.success_msg || [],
      error_msg: res.locals.error_msg || [],
      ...res.locals,
      ...locals
    };
    return originalRender(
      'react-shell',
      {
        title: data.title || 'ATTIC PANEL',
        panel_name: data.panel_name || data.storeName || 'ATTIC PANEL',
        bodyClass: view === 'login' ? 'login-view' : view.startsWith('store/') && !view.startsWith('store/admin/') ? 'store-view' : '',
        appData: safeJson(stripPrivateData(data))
      },
      callback
    );
  };
  next();
});

app.use('/', authRoutes);
app.use('/admin', adminRoutes);
app.use('/api', customEndpointRoutes);
app.use('/api', apiRoutes);
app.use('/mod', hgModsRoutes);
app.use('/mod', brModsRoutes);
app.use('/', valorantRoutes);
// Store routes
app.use('/store', storeIndexRoutes);
app.use('/admin/store', storeAdminRoutes);
app.use('/api/store', storeApiRoutes);
app.use('/api', storeApiRoutes);

const authApiRoutes = require('./routes/auth_api');
app.use('/auth', authApiRoutes);

const crypto = require('crypto');
const { validateAndRegisterKey } = require('./services/gameAuth');
const { formatDateTime } = require('./routes/api');

app.post('/connect', async (req, res) => {
  const userKey = (req.body.user_key || req.body.member_key || '').trim();
  const serial = (req.body.serial || '').trim();
  const resource = (req.body.resource || '').trim();

  const auth = await validateAndRegisterKey(userKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason });

  const { key } = auth;
  const real = `DFM-${userKey}-${serial}${resource ? '-' + resource : ''}-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`;
  const token = crypto.createHash('md5').update(real).digest('hex');
  const ts = formatDateTime(key.expires_at);

  res.json({
    status: true,
    data: {
      real,
      token,
      rng: Number(key.expires_at),
      ts: ts
    }
  });
});

app.post('/project/login', (req, res) => {
  res.status(200).json({ id: '68ef13a95b460ed3e8845e16', platform: ['PUBG', 'DFM', 'ARB'] });
});

// 404 handler (placed after all routes so they can be matched)
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
initDB().catch((err) => {
  console.error('DB init failed:', err);
  process.exit(1);
});

const PORT = process.env.PORT || 3000;
if (require.main === module) {
  app.listen(PORT, () => {
    console.log(`\n ATTIC PANEL → http://localhost:${PORT}\n`);
  });
}

module.exports = app;
