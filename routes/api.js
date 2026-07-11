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

router.post('/vvip-bs', async (req, res) => {
  const userKey = (req.body.user_key || req.body.member_key || '').trim();
  const serial = (req.body.serial || '').trim();
  const resource = (req.body.resource || '').trim();

  const auth = await validateAndRegisterKey(userKey, serial);
  if (!auth.success) return res.json({ status: false, reason: auth.reason });

  const { key } = auth;
  const real = `TS-${userKey}-${serial}${resource ? '-' + resource : ''}-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`;
  const token = crypto.createHash('md5').update(real).digest('hex');
  const ts = formatDateTime(key.expires_at);

  res.json({
    status: true,
    data: {
      real,
      token,
      rng: Number(key.expires_at),
      EXPR: ts,
      xenoanticrack: 'TS'
    }
  });
});

router.post('/ev8bp', async (req, res) => {
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

function xorWithStream(input, seed) {
  const output = Buffer.alloc(input.length);
  let offset = 0;
  let round = 0;
  while (offset < input.length) {
    const block = crypto.createHash('sha256').update(seed).update(String(round++)).digest();
    for (let i = 0; i < block.length && offset < input.length; i++, offset++) output[offset] = input[offset] ^ block[i];
  }
  return output;
}

function encryptGngEnvelope(payload) {
  const key = crypto.createHash('sha256').update(process.env.GNG_CHACHA_SECRET || 'gng-chacha20-poly1305-key-2024').digest();
  const nonce = crypto.randomBytes(12);
  const mask = crypto.randomBytes(16);
  const aad = Buffer.from([0x67, 0x6e, 0x67, 0x3a, 0x32]);
  const cipher = crypto.createCipheriv('chacha20-poly1305', key, nonce, { authTagLength: 16 });
  cipher.setAAD(aad);
  const encrypted = Buffer.concat([cipher.update(JSON.stringify(payload), 'utf8'), cipher.final()]);
  const tag = cipher.getAuthTag();

  const a = mask.toString('base64url');
  const b = xorWithStream(nonce, Buffer.concat([mask, Buffer.from('n')])).toString('base64url');
  const c = xorWithStream(tag, Buffer.concat([mask, Buffer.from('t')])).toString('base64url');
  const d = xorWithStream(encrypted, Buffer.concat([mask, Buffer.from('d')])).toString('base64url');
  const e = crypto.createHmac('sha256', key).update(`${a}.${b}.${c}.${d}`).digest().subarray(0, 18).toString('base64url');

  return { a, b, c, d, e };
}

router.post('/gng', async (req, res) => {
  const key_code = (req.body.key_code || req.body.user_key || req.body.member_key || '').trim();
  const serial = (req.body.serial || req.body.device_serial || '').trim();

  const auth = await validateAndRegisterKey(key_code, serial);
  if (!auth.success) {
    const failPayload = { status: false, reason: auth.reason || 'Key tidak valid' };
    return res.json(encryptGngEnvelope(failPayload));
  }

  const { key } = auth;
  const raw = `GNG-${key_code}-${serial}-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`;
  const token = crypto.createHash('md5').update(raw).digest('hex');

  const payload = {
    status: true,
    token: token,
    rng: Number(key.expires_at),
    expires_at: new Date(Number(key.expires_at) * 1000).toISOString()
  };

  res.json(encryptGngEnvelope(payload));
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
module.exports.formatDateTime = formatDateTime;
