const express      = require('express');
const cookieParser = require('cookie-parser');
const methodOverride = require('method-override');
const path         = require('path');
const { initDB }   = require('./database');

const authRoutes   = require('./routes/auth');
const adminRoutes  = require('./routes/admin');
const apiRoutes    = require('./routes/api');

const app = express();

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(cookieParser());
app.use(methodOverride('_method'));
app.use(express.static(path.join(__dirname, 'public')));

// Flash cookie middleware (type + msg set before redirect, read here)
app.use((req, res, next) => {
  const raw = req.cookies._flash;
  if (raw) {
    try {
      const { type, msg } = JSON.parse(raw);
      res.locals.success_msg = type === 'success' ? [msg] : [];
      res.locals.error_msg   = type === 'error'   ? [msg] : [];
    } catch { res.locals.success_msg = []; res.locals.error_msg = []; }
    res.clearCookie('_flash');
  } else {
    res.locals.success_msg = [];
    res.locals.error_msg   = [];
  }
  next();
});

// Flash helper: call before res.redirect()
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

app.use((req, res) => {
  res.status(404).send(`
    <html><body style="background:#070b10;color:#00e5ff;font-family:monospace;display:flex;align-items:center;justify-content:center;height:100vh;flex-direction:column;gap:1rem;">
      <div style="font-size:4rem;font-weight:900;">404</div>
      <div>ENDPOINT NOT FOUND</div>
      <a href="/" style="color:#00ff88;text-decoration:none;">← BACK</a>
    </body></html>
  `);
});

// Init DB then start (local) / export (Vercel)
initDB().catch(err => { console.error('DB init failed:', err); process.exit(1); });

const PORT = process.env.PORT || 3000;
if (require.main === module) {
  app.listen(PORT, () => {
    console.log(`\n  ATTIC PANEL → http://localhost:${PORT}\n`);
  });
}

module.exports = app;
