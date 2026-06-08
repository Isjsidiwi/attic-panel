const express = require('express');
const router = express.Router();
const auth = require('../../middleware/auth');
const db = require('../../database');
const { loadConfig } = require('../../config');
const { fmtDate, parseSerials } = require('../../utils/adminUtils');

const requireOwner = auth.requireOwner;

router.get('/', auth, requireOwner, async (req, res) => {
  const now = Math.floor(Date.now() / 1000);
  const cfg = await loadConfig();

  const [total, active, expired, recent] = await Promise.all([
    db.get('SELECT COUNT(*) AS c FROM keys'),
    db.get('SELECT COUNT(*) AS c FROM keys WHERE is_active=1 AND (expires_at>? OR expires_at=0)', [now]),
    db.get('SELECT COUNT(*) AS c FROM keys WHERE expires_at<=? AND expires_at!=0', [now]),
    db.all('SELECT * FROM keys ORDER BY created_at DESC LIMIT 8')
  ]);

  const locked = await db.get("SELECT COUNT(*) AS c FROM keys WHERE device_serials != '[]'");

  res.render('dashboard', {
    title: 'Dashboard',
    panel_name: cfg.panel_name,
    stats: { total: total.c, active: active.c, expired: expired.c, locked: locked.c },
    recent,
    now,
    fmtDate,
    parseSerials,
    show_api_info: process.env.SHOW_API_INFO === '1'
  });
});

module.exports = router;
