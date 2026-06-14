const { createClient } = require('@libsql/client');
require('dotenv').config();

let client;

function getClient() {
  if (!client) {
    client = createClient({
      url: process.env.TURSO_DATABASE_URL,
      authToken: process.env.TURSO_AUTH_TOKEN
    });
  }
  return client;
}

async function ensureColumn(db, table, column, definition) {
  const cols = await db.execute(`PRAGMA table_info(${table})`);
  const colNames = cols.rows.map((r) => r.name);
  if (!colNames.includes(column)) {
    await db.execute(`ALTER TABLE ${table} ADD COLUMN ${column} ${definition}`);
  }
}

async function initDB() {
  const db = getClient();
  const now = Math.floor(Date.now() / 1000);

  // Buat tabel dengan skema terbaru
  await db.executeMultiple(`
    CREATE TABLE IF NOT EXISTS keys (
      id              INTEGER PRIMARY KEY AUTOINCREMENT,
      key_code        TEXT UNIQUE NOT NULL,
      resource        TEXT NOT NULL DEFAULT 'vip',
      device_serials  TEXT NOT NULL DEFAULT '[]',
      max_devices     INTEGER NOT NULL DEFAULT 1,
      created_at      INTEGER NOT NULL,
      expires_at      INTEGER NOT NULL,
      duration        INTEGER NOT NULL DEFAULT 0,
      is_active       INTEGER NOT NULL DEFAULT 1,
      notes           TEXT DEFAULT '',
      login_count     INTEGER DEFAULT 0,
      last_login      INTEGER DEFAULT NULL
    );

    CREATE TABLE IF NOT EXISTS config (
      key   TEXT PRIMARY KEY,
      value TEXT NOT NULL
    );

    CREATE TABLE IF NOT EXISTS users (
      id            INTEGER PRIMARY KEY AUTOINCREMENT,
      username      TEXT UNIQUE NOT NULL,
      password_hash TEXT NOT NULL,
      role          TEXT NOT NULL DEFAULT 'reseller',
      credit        INTEGER NOT NULL DEFAULT 0,
      is_active     INTEGER NOT NULL DEFAULT 1,
      created_at    INTEGER NOT NULL,
      updated_at    INTEGER DEFAULT NULL
    );

    CREATE TABLE IF NOT EXISTS key_prices (
      game          TEXT NOT NULL,
      duration_days INTEGER NOT NULL,
      price_credit INTEGER NOT NULL DEFAULT 0,
      PRIMARY KEY (game, duration_days)
    );

    CREATE TABLE IF NOT EXISTS store_products (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      slug TEXT NOT NULL UNIQUE,
      logo_url TEXT,
      description TEXT,
      price INTEGER NOT NULL,
      category TEXT DEFAULT 'umum',
      is_active INTEGER DEFAULT 1,
      created_at TEXT DEFAULT (datetime('now','localtime'))
    );

    CREATE TABLE IF NOT EXISTS store_product_variants (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      product_id INTEGER NOT NULL,
      name TEXT NOT NULL,
      price INTEGER NOT NULL,
      original_price INTEGER,
      created_at TEXT DEFAULT (datetime('now','localtime')),
      FOREIGN KEY (product_id) REFERENCES store_products(id)
    );

    CREATE TABLE IF NOT EXISTS store_keys (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      product_id INTEGER NOT NULL,
      variant_id INTEGER,
      key_value TEXT NOT NULL,
      is_used INTEGER DEFAULT 0,
      order_id TEXT,
      used_at TEXT,
      created_at TEXT DEFAULT (datetime('now','localtime')),
      FOREIGN KEY (product_id) REFERENCES store_products(id),
      FOREIGN KEY (variant_id) REFERENCES store_product_variants(id)
    );

    CREATE TABLE IF NOT EXISTS store_referrals (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      code TEXT NOT NULL UNIQUE,
      discount_amount INTEGER NOT NULL,
      expired_at TEXT,
      is_active INTEGER DEFAULT 1,
      created_at TEXT DEFAULT (datetime('now','localtime'))
    );

    CREATE TABLE IF NOT EXISTS store_orders (
      id TEXT PRIMARY KEY,
      product_id INTEGER NOT NULL,
      variant_id INTEGER,
      key_id INTEGER,
      customer_name TEXT NOT NULL,
      customer_email TEXT NOT NULL,
      amount INTEGER NOT NULL,
      unique_amount INTEGER NOT NULL,
      unique_suffix INTEGER NOT NULL,
      qris_id INTEGER,
      qris_url TEXT,
      status TEXT DEFAULT 'pending',
      created_at TEXT DEFAULT (datetime('now','localtime')),
      paid_at TEXT,
      expired_at TEXT,
      FOREIGN KEY (product_id) REFERENCES store_products(id),
      FOREIGN KEY (variant_id) REFERENCES store_product_variants(id),
      FOREIGN KEY (key_id) REFERENCES store_keys(id)
    );
  `);

  // Migration: tabel lama mungkin punya device_serial (tanpa s)
  try {
    const cols = await db.execute('PRAGMA table_info(keys)');
    const colNames = cols.rows.map((r) => r.name);

    if (colNames.includes('device_serial') && !colNames.includes('device_serials')) {
      await db.execute("ALTER TABLE keys ADD COLUMN device_serials TEXT NOT NULL DEFAULT '[]'");
      await db.execute('ALTER TABLE keys ADD COLUMN max_devices INTEGER NOT NULL DEFAULT 1');
      await db.execute(`
        UPDATE keys SET device_serials =
          CASE WHEN device_serial IS NULL THEN '[]'
               ELSE json_array(device_serial)
          END
      `);
    } else {
      if (!colNames.includes('device_serials'))
        await db.execute("ALTER TABLE keys ADD COLUMN device_serials TEXT NOT NULL DEFAULT '[]'");
      if (!colNames.includes('max_devices'))
        await db.execute('ALTER TABLE keys ADD COLUMN max_devices INTEGER NOT NULL DEFAULT 1');
    }
  } catch (_) {
    /* tabel fresh, kolom sudah ada */
  }

  await ensureColumn(db, 'keys', 'created_by', 'INTEGER DEFAULT NULL');
  await ensureColumn(db, 'keys', 'created_by_username', "TEXT DEFAULT ''");
  await ensureColumn(db, 'keys', 'price_paid', 'INTEGER NOT NULL DEFAULT 0');
  await ensureColumn(db, 'keys', 'duration', 'INTEGER NOT NULL DEFAULT 0');
  await ensureColumn(db, 'users', 'expires_at', 'INTEGER DEFAULT NULL');
  await ensureColumn(db, 'users', 'allowed_games', "TEXT NOT NULL DEFAULT '[]'");
  await ensureColumn(db, 'store_referrals', 'allowed_products', "TEXT DEFAULT '[]'");

  // Seed default config (Hanya berjalan jika tabel kosong)
  const bcrypt = require('bcryptjs');
  const defaults = {
    panel_name: 'ATTIC PANEL',
    admin_username: 'admin',
    admin_password: bcrypt.hashSync('admin123', 10),
    maintenance_mode: '0'
  };

  for (const [k, v] of Object.entries(defaults)) {
    await db.execute({ sql: 'INSERT OR IGNORE INTO config (key, value) VALUES (?, ?)', args: [k, v] });
  }

  const cfgRows = await db.execute('SELECT key, value FROM config');
  const cfg = {};
  cfgRows.rows.forEach((r) => {
    cfg[r.key] = r.value;
  });

  const owner = await db.execute("SELECT id FROM users WHERE role = 'owner' LIMIT 1");
  if (owner.rows.length === 0) {
    await db.execute({
      sql: 'INSERT INTO users (username, password_hash, role, credit, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?)',
      args: [
        cfg.admin_username || defaults.admin_username,
        cfg.admin_password || defaults.admin_password,
        'owner',
        0,
        1,
        now
      ]
    });
  }

  const games = ['BS', 'MLBB', 'PUBGM', 'CODM', '8BP', 'DFM'];
  for (const game of games) {
    for (let day = 1; day <= 30; day++) {
      await db.execute({
        sql: 'INSERT OR IGNORE INTO key_prices (game, duration_days, price_credit) VALUES (?, ?, ?)',
        args: [game, day, day]
      });
    }
  }

  console.log('Turso DB ready');
}

async function all(sql, args = []) {
  const db = getClient();
  const res = await db.execute({ sql, args });
  return res.rows;
}

async function get(sql, args = []) {
  const rows = await all(sql, args);
  return rows[0] || null;
}

async function run(sql, args = []) {
  const db = getClient();
  return db.execute({ sql, args });
}

module.exports = {
  initDB,
  all,
  get,
  run,
  db: { execute: (sql, args) => run(sql, args) }
};
