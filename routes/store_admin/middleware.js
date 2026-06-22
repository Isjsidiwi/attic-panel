const auth = require('../../middleware/auth');

const requireStoreAdmin = (req, res, next) => {
  // Use the standard auth middleware first (it redirects to /login)
  // Store admin now uses the unified login page at /login.

  if (req.user && req.user.role === 'owner') {
    return next();
  }
  // Jika bukan owner (misal reseller atau belum login), arahkan ke login utama dengan pesan error
  res.redirect('/login?error=Akses+ditolak.+Hanya+owner+yang+bisa+masuk+ke+Store+Admin.');
};

module.exports = { requireStoreAdmin };
