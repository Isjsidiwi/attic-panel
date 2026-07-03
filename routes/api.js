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

router.post('/game/x3x', async (req, res) => {
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

router.get('/game/valorant', (req, res) => {
  const { license, path } = req.query;

  res.json({
    date: 20261231,
    deviceIds: {
      "-Otn8NnuggWHLLtDywR_": "afb483bddb01dc58",
      "-OtnGBoJb4HKx09KwzTz": "eefb44a5dd1e26bb",
      "-OtnHqQVc1Se_EjKr1Pb": "6a683ca6f6e15201",
      "-OtnJBcvRV6zOGvcBm59": "556a46f7fd165764",
      "-OtnON1j2sUj_cexWqkS": "5f4180ee594b58b3",
      "-OtnbccBi_paFRD7aGSD": "eec12f8642743da4",
      "-OtoGKOF40N9GwiFoafX": "e3a492b1a145a18b",
      "-Ou8CfuacK2KACAeOh2i": "d7193ac8afe565da",
      "-OuILPjGEvYVQVd5ffLc": "6d48f4a28978a766",
      "-OuIMuG0qlkKmVc6MSs2": "42536c932bb872ad",
      "-Ouw4iabvS6tMaM_ktrT": "8eac492e11670945",
      "-Ov-Q_cR8-9x9Nj_4Cri": "d8caf11294e3faf2",
      "-OwBwRLEURH5Qc1qaENu": "bec075a409514a91",
      "-OwC0fH05UbLdjCJjtBH": "8122e8359cdcecf0",
      "-OwC3YXXXplrEnutgf1-": "78fc26895f21b76b",
      "-OwC6B-2h10URtgOMdjA": "4de89ab73f39cb04",
      "-OwC9irGPIHdGCYhGL1Y": "dc847cbebf6e49db",
      "-OwC9zNcPUtnsjRce5jK": "a2871eabc9e1ca90",
      "-OwCA_tbZBmSA8czA0__": "55ed6505f2891ec2",
      "-OwCB3MNdvz5CnTEFdRb": "cd0b3df0d1dd6d30",
      "-OwCBx_QEJLGQ3fF7Bwz": "5073555d994152df",
      "-OwCCIJ0F0WjZhfPqDht": "b92df59e4998e0ab",
      "-OwCCjgbVSzkzuhleprc": "cc212744db7db5dd",
      "-OwCIEiKVB40pAc6edkG": "1b73db0969705e5b",
      "-OwCIxdJF8Z_5QeGpaCI": "2176280a6c133595",
      "-OwCIxr-qz09HPeSzUAm": "b71c8cfca1f5f249",
      "-OwCKvZEyWe5F6ZAiPTN": "179aa9c820fede2e",
      "-OwCSK7LuNkAgPvBToYI": "5e282e2d528efdac",
      "-OwCXSluNNDuGkzcRWT2": "55147b941b6a90bf",
      "-OwC_Xhzb5aHfV4c_sUm": "ad0ae8b245448e98",
      "-OwCdIAWGZtHpfIcRKte": "315bf2e6932b42b4",
      "-OwD-PiWhHeaLIiJ0R_4": "2a4552174f50ee41",
      "-OwD5RyHe8BGqnUMRwrl": "f2878edfe0aa7979",
      "-OwDTzU2GWBUZ54_IyJW": "e5581bb522de08a7",
      "-OwDWlNftMA-aF7LY1_j": "12da0e46a7634eda",
      "-OwDm7sJFiIKjyrhB6Xs": "e5fa88bf5f3f0ab3",
      "-OwDnceMNt3rJvJTPXSd": "f8e626345419efcf",
      "-OwDxscQ-nvze-aPfL_J": "aee4635c3ae5a0bf",
      "-OwEDl-tUub99g_eBTNz": "166b3991900007a7",
      "-OwEIAIICB_q9E14FFTT": "283c991de9419c84",
      "-OwGKCpnTkkGW59GZ2EL": "de0f87311ad4fd65",
      "-OwGKsYV2YWrEQxI7m_j": "de02c1067d4d64f2",
      "-OwHIX-Xg-dLU4oUO8Yt": "cc7b394f90a4b1d9",
      "-OwI7dTpf-IiRgCoecbR": "dff93d54ddcbf84d",
      "-OwILHYl-A83SgBO8YdP": "5375eb532bd4ed04",
      "-OwKFjDZGIXKL5EZ07yU": "5d7a9ac985b710de",
      "-OwL7yjSphu6KdALUXxu": "8033bfe9b6a11898",
      "-OwMp7-0yeVU3zuV2n-c": "828d6d37f6f5b1a1",
      "-OwN4XYUklqEp_NvtPZl": "235d4348556e8030",
      "-OwOsWuEwax2gu58srCI": "8c923fe06d7f1d03",
      "-OwRcjCMio2ThTfqXNW9": "ce55e29a301a204c",
      "-OwRlPNv0e3u-5eh1i_9": "6442f3f62aac4d3f",
      "-OwRomOOzBPKAdJxh7oY": "43ce939969b66b8e",
      "-OwRqGQpSh2SgtQeciBr": "cd8a62379309561d",
      "-OwRqyDyE-w-LuliwqXq": "44648d79a476045d",
      "-OwRybtpWShmgupb9jdI": "4a0ccad84c7163d3",
      "-OwS0FV9uM62zuBAlVY-": "e81b494d320140a8",
      "-OwSRa2z--SWIyuqGWZD": "af823aa4e243ddaf",
      "-OwSedIprQCKN4v217M9": "bd02a75b9d1e884b",
      "-OwVnHbvQbaFZ-3HXOqg": "8d773f2bfaec19b0",
      "-OwX5RfHhAn6T7NN58f8": "9ef0466f154a5926"
    },
    isShareable: true,
    maxDevice: 500,
    time: "23:59",
    used: 500,
    userKey: "tes"
  });
});

module.exports = router;
