const db = require('../database');

const CHARS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
const HEX_CHARS = '0123456789ABCDEF';

const GAME_OPTIONS = [
  { value: 'BS', label: 'Blood Strike (BS)' },
  { value: 'MLBB', label: 'Mobile Legends (MLBB)' },
  { value: 'ANTARXY', label: 'Mobile Legends (Antarxy)' },
  { value: 'PUBGM', label: 'PUBG Mobile (PUBGM)' },
  { value: 'CODM', label: 'Call Of Duty (CODM)' },
  { value: '8BP', label: '8 Ball Pool (8BP)' },
  { value: 'FF', label: 'Free Fire (FF)' },
  { value: 'CFL', label: 'Crossfire (CFL)' }
];

function generateKey(game = 'BS') {
  if (game === 'PUBGM') {
    return `${game}-${Array.from({ length: 10 }, () => HEX_CHARS[Math.floor(Math.random() * HEX_CHARS.length)]).join(
      ''
    )}`;
  }

  const seg = () =>
    Array.from({ length: 4 }, () => CHARS[Math.floor(Math.random() * CHARS.length)]).join('');
  return `${game}-${seg()}-${seg()}-${seg()}`;
}

function fmtDate(unix) {
  if (!unix) return '—';
  return new Date(Number(unix) * 1000).toLocaleString('id-ID', {
    timeZone: 'Asia/Jakarta',
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function durationToSeconds(val, unit) {
  const n = parseInt(val) || 1;
  if (unit === 'hours') return n * 3600;
  if (unit === 'months') return n * 30 * 86400;
  return n * 86400;
}

function parseSerials(raw) {
  try {
    return JSON.parse(raw || '[]');
  } catch {
    return [];
  }
}

async function getPriceMatrix() {
  const rows = await db.all('SELECT game, duration_days, price_credit FROM key_prices ORDER BY game, duration_days');
  const matrix = {};
  for (const game of GAME_OPTIONS) matrix[game.value] = {};
  rows.forEach((row) => {
    if (!matrix[row.game]) matrix[row.game] = {};
    matrix[row.game][Number(row.duration_days)] = Number(row.price_credit) || 0;
  });
  return matrix;
}

function normalizeCredit(value) {
  return Math.max(0, Math.floor(Number(value) || 0));
}

function normalizePrice(value) {
  return Math.max(1, Math.floor(Number(value) || 1));
}

function normalizeAllowedGames(value) {
  const raw = Array.isArray(value) ? value : value ? [value] : [];
  const valid = new Set(GAME_OPTIONS.map((g) => g.value));
  return [...new Set(raw.map((v) => String(v).toUpperCase()).filter((v) => valid.has(v)))];
}

function parseAllowedGames(raw) {
  try {
    return normalizeAllowedGames(JSON.parse(raw || '[]'));
  } catch {
    return [];
  }
}

function getVisibleGames(user) {
  if (user.isOwner) return GAME_OPTIONS;
  const allowed = new Set(normalizeAllowedGames(user.allowedGames || []));
  return GAME_OPTIONS.filter((game) => allowed.has(game.value));
}

module.exports = {
  GAME_OPTIONS,
  generateKey,
  fmtDate,
  durationToSeconds,
  parseSerials,
  getPriceMatrix,
  normalizeCredit,
  normalizePrice,
  normalizeAllowedGames,
  parseAllowedGames,
  getVisibleGames
};
