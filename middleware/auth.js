const jwt = require('jsonwebtoken');
const db = require('../database');

const SECRET = () => process.env.JWT_SECRET || 'attic-jwt-fallback-secret';

async function auth(req, res, next) {
  const token = req.cookies._token;
  if (!token) return res.redirect('/login');
  try {
    const payload = jwt.verify(token, SECRET());
    const user = payload.id
      ? await db.get('SELECT id, username, role, credit, is_active FROM users WHERE id=?', [payload.id])
      : await db.get('SELECT id, username, role, credit, is_active FROM users WHERE username=?', [payload.username]);

    if (!user || !user.is_active) {
      res.clearCookie('_token');
      return res.redirect('/login');
    }

    req.user = {
      id: user.id,
      username: user.username,
      role: user.role,
      credit: Number(user.credit) || 0,
      isOwner: user.role === 'owner'
    };
    res.locals.admin = req.user;
    next();
  } catch (err) {
    res.clearCookie('_token');
    res.redirect('/login');
  }
}

auth.requireOwner = (req, res, next) => {
  if (req.user && req.user.role === 'owner') return next();
  res.flash('error', 'Akses owner diperlukan.');
  return res.redirect('/admin/keys');
};

module.exports = auth;
