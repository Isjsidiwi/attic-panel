const express = require('express');
const cookieParser = require('cookie-parser');
const session = require('cookie-session');
const methodOverride = require('method-override');
const path = require('path');
const jwt = require('jsonwebtoken');
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
  res.setHeader('Content-Security-Policy', "default-src 'self' https://cdn.tailwindcss.com; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://fonts.googleapis.com; img-src 'self' data: https: http:; font-src 'self' https://fonts.gstatic.com; connect-src 'self'");
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
app.use('/api', storeApiRoutes);
const crypto = require('crypto');

app.post('/mod/LoginData.php', (req, res) => {
    // 1. Ambil payload 'c' dari request APK
    const payloadC_Base64 = req.body.c; 
    
    // Ubah ke buffer biner
    const encryptedRequestBuffer = Buffer.from(payloadC_Base64, 'base64');
    
    // KUNCI RAHASIANYA: Ambil 16 byte terakhir dari ciphertext request!
    // Inilah IV yang ditunggu oleh aplikasi.
    const mutatedIv = encryptedRequestBuffer.slice(-16); 
    
    // Key tetap 0000 (sesuai patch kamu)
    const key = Buffer.alloc(16, 0);

    const responseJson = JSON.stringify({
        "ConnectSt_hk": "Failed",
        "mensagem": "tes failed",
        "timestamp": Math.floor(Date.now() / 1000),
        "nonce": "ef84a889dec86aafa295bf5c6fd92382" // Bebas
    });

    try {
        // Enkripsi menggunakan IV yang sudah bermutasi
        const cipher = crypto.createCipheriv('aes-128-cbc', key, mutatedIv);
        
        let encryptedBase64 = cipher.update(responseJson, 'utf8', 'base64');
        encryptedBase64 += cipher.final('base64');

        res.status(200).json({
            "data": encryptedBase64,
            "signature": "111111"
        });

        console.log("[+] Berhasil mengirim response dengan Mutated IV!");

    } catch (e) {
        console.error("[-] Error:", e);
    }
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
initDB().catch(err => { console.error('DB init failed:', err); process.exit(1); });

const PORT = process.env.PORT || 3000;
if (require.main === module) {
  app.listen(PORT, () => {
    console.log(`\n ATTIC PANEL → http://localhost:${PORT}\n`);
  });
}

module.exports = app;
