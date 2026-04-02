const jwt = require('jsonwebtoken');

const SECRET = () => process.env.JWT_SECRET || 'attic-jwt-fallback-secret';

module.exports = (req, res, next) => {
  const token = req.cookies._token;
  if (!token) return res.redirect('/login');
  try {
    const payload = jwt.verify(token, SECRET());
    res.locals.admin = { username: payload.username };
    next();
  } catch {
    res.clearCookie('_token');
    res.redirect('/login');
  }
};
