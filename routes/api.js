const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../database');
const { loadConfig } = require('../config');
const { validateAndRegisterKey } = require('../services/gameAuth');

const monthNamesId = [
  'Januari',
  'Februari',
  'Maret',
  'April',
  'Mei',
  'Juni',
  'Juli',
  'Agustus',
  'September',
  'Oktober',
  'November',
  'Desember'
];

function formatDateId(date) {
  const d = date.getDate().toString().padStart(2, '0');
  const m = monthNamesId[date.getMonth()];
  const y = date.getFullYear();
  const hh = date.getHours().toString().padStart(2, '0');
  const mm = date.getMinutes().toString().padStart(2, '0');
  return `${d} - ${m} - ${y} ${hh}:${mm}`;
}

function formatIsoMicros(unix) {
  return new Date(Number(unix) * 1000).toISOString().replace(/\.(\d{3})Z$/, (_, ms) => `.${ms}000Z`);
}

function formatDateTime(unix) {
  const parts = new Intl.DateTimeFormat('en-CA', {
    timeZone: 'Asia/Jakarta',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hourCycle: 'h23'
  })
    .formatToParts(new Date(Number(unix) * 1000))
    .reduce((acc, part) => {
      if (part.type !== 'literal') acc[part.type] = part.value;
      return acc;
    }, {});

  return `${parts.year}-${parts.month}-${parts.day} ${parts.hour}:${parts.minute}:${parts.second}`;
}





// --- Game Endpoints ---

router.post('/game/MLBB', async (req, res) => {
  const userKey = (req.body.user_key || '').trim();
  const serial = (req.body.serial || '').trim();
  const resource = (req.body.resource || '').trim();

  const auth = await validateAndRegisterKey(userKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason, data: null });

  const { key } = auth;
  const raw = `MLBB-${userKey}-${serial}-${resource}-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`;
  const token = crypto.createHash('md5').update(raw).digest('hex');
  const expiredStr = new Date(Number(key.expires_at) * 1000).toISOString().replace('T', ' ').slice(0, 19);

  res.json({
    status: true,
    reason: 'Login Success',
    data: { token, rng: Number(key.expires_at), tittle: 'Provided by Xsrc & Shannz', expired: expiredStr }
  });
});

router.post('/game/8bp', async (req, res) => {
  const userKey = (req.body.user_key || '').trim();
  const serial = (req.body.serial || '').trim();
  const game = (req.body.game || '').trim();

  if (!serial || !game) return res.json({ status: false, reason: 'Serial and game are required', data: null });

  const auth = await validateAndRegisterKey(userKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason, data: null });

  const { key } = auth;
  const raw = `8BP-${userKey}-${serial}-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`;
  const token = crypto.createHash('md5').update(raw).digest('hex');
  const formattedDate = formatDateId(new Date());
  const formattedExpired = formatDateId(new Date(Number(key.expires_at) * 1000));

  res.json({
    status: true,
    data: {
      Datte: formattedDate,
      token,
      rng: Number(key.expires_at) || Math.floor(Math.random() * 9000000000) + 1000000000,
      tittle: ' | Easyvictors',
      instance: 'Instance',
      expired: formattedExpired
    }
  });
});

router.post('/game/pubgm', async (req, res) => {
  const memberKey = (req.body.member_key || req.body.user_key || '').trim();
  const serial = (req.body.serial || '').trim();

  const auth = await validateAndRegisterKey(memberKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason, data: null });

  const { key } = auth;
  res.json({
    status: true,
    data: {
      token: 'e57e308605a4dc7f5da27a8a63dc0645',
      rng: Number(key.expires_at),
      expired: formatIsoMicros(key.expires_at),
      EXPR: formatIsoMicros(key.expires_at),
      registrator: 'Edge'
    }
  });
});

const mobileLegendsHandler = async (req, res) => {
  const userKey = (req.body.user_key || req.body.member_key || '').trim();
  const serial = (req.body.serial || '').trim();
  const appVer = (req.body.appVer || '').trim();

  const auth = await validateAndRegisterKey(userKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason, data: null });

  const { key } = auth;
  const raw = `${userKey}${serial}ANTARXY`;
  const token = crypto.createHash('md5').update(raw).digest('hex');
  const expiredStr = new Date(Number(key.expires_at) * 1000).toISOString().replace('T', ' ').slice(0, 19);

  res.set({
    'Content-Type': 'application/json',
    'Cache-Control': 'no-store, no-cache, must-revalidate',
    Pragma: 'no-cache'
  });

  res.json({
    status: true,
    data: {
      token,
      rng: Math.floor(Date.now() / 1000),
      expired: expiredStr,
      custom_title: '-Xive',
      version: appVer || '1.0.0',
      registrator: 5,
      btData: 'BattleData'
    }
  });
};

router.post('/game/mobile-legends', mobileLegendsHandler);
router.post('/game/MLBB/antrxyz', mobileLegendsHandler);

router.post('/game/CFL', async (req, res) => {
  const userKey = (req.body.user_key || req.body.member_key || '').trim();
  const serial = (req.body.serial || '').trim();

  const auth = await validateAndRegisterKey(userKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason, data: null });

  const { key } = auth;
  const expiredStr = new Date(Number(key.expires_at) * 1000).toISOString().replace('T', ' ').slice(0, 19);

  res.set({ 'Content-Type': 'application/json', 'Cache-Control': 'no-cache, private' });
  res.json({
    status: true,
    data: {
      token: 'f7193b42781e210ddfcb4debb62c9837',
      rng: Math.floor(Date.now() / 1000),
      EXPR: expiredStr,
      vvipmodsgr: 'QUh/k8wd+CfJxob7qKlIyMlHfxiauTXyjkN6258nbu0='
    }
  });
});

router.post('/game/freefire', async (req, res) => {
  const memberKey = (req.body.member_key || req.body.user_key || '').trim();
  const serial = (req.body.serial || '').trim();

  const auth = await validateAndRegisterKey(memberKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason, data: null });

  const { key } = auth;
  const raw = `${memberKey}${serial}VVIPMODS`;
  const token = crypto.createHash('md5').update(raw).digest('hex');
  const expiredStr = formatIsoMicros(key.expires_at);

  res.set({ 'Content-Type': 'application/json', 'Cache-Control': 'no-cache, private' });
  res.json({
    status: true,
    data: {
      token,
      rng: Math.floor(Date.now() / 1000),
      expired: expiredStr,
      EXPR: expiredStr,
      registrator: 'Edge'
    }
  });
});

router.post('/connect', async (req, res) => {
  const userKey = (req.body.user_key || '').trim();
  const serial = (req.body.serial || '').trim();

  const auth = await validateAndRegisterKey(userKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason, data: null });

  const { key } = auth;
  res.json({
    status: true,
    data: {
      real: `${userKey}-f4c61ab5-f04d-3300-b3e8-c1720ae56b64-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`,
      token: '61a1c302db02026dc48c57f8eff693b3',
      modname: 'VIP MOD',
      mod_status: 'Safe',
      credit: '110% SAFE',
      ESP: 'on',
      Item: 'on',
      AIM: 'on',
      SilentAim: 'on',
      BulletTrack: 'on',
      Floating: 'on',
      Memory: 'on',
      Setting: 'on',
      expired_date: '2027-12-31 23:59:59',
      EXP: '2027-12-31 23:59:59',
      exdate: '2027-12-31 23:59:59',
      device: String(key.max_devices),
      rng: Math.floor(Math.random() * 9999999999)
    }
  });
});

router.post('/codm', async (req, res) => {
  const game = (req.body.game || 'CODM').trim().toUpperCase();
  const userKey = (req.body.user_key || req.body.member_key || '').trim();
  const serial = (req.body.serial || '').trim();

  const auth = await validateAndRegisterKey(userKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason, data: null });

  const { key } = auth;
  const real = `${game}-${userKey}-${serial}-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`;
  const token = crypto.createHash('md5').update(real).digest('hex');
  const expired = formatDateTime(key.expires_at);

  res.set('Cache-Control', 'no-store, max-age=0, no-cache');
  res.json({
    status: true,
    data: {
      real,
      token,
      modname: 'CFL MOD',
      mod_status: 'Safe',
      credit: 'Test',
      ESP: false,
      Item: false,
      AIM: false,
      SilentAim: false,
      BulletTrack: false,
      Floating: false,
      Memory: false,
      Setting: false,
      EXPR: expired,
      device: String(key.max_devices),
      MOD_NAME: 'CFL MOD',
      MOD_STATUS: 'Safe',
      FLOTING_TEST: 'Test',
      EXP: expired,
      SLOT: String(key.max_devices),
      cantcrack: '5DXuN61YKEgIhKNqa6PbJgf8DXm1Sft10sEEfFs9st8=',
      rng: Math.floor(Date.now() / 1000)
    }
  });
});

module.exports = router;
