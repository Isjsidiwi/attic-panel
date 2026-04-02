const { createClient } = require('@libsql/client');

let client;

function getClient() {
  if (!client) {
    client = createClient({
      url:       process.env.TURSO_DATABASE_URL,
      authToken: process.env.TURSO_AUTH_TOKEN
    });
  }
  return client;
}

async function initDB() {
  const db = getClient();

  await db.executeMultiple(`
    CREATE TABLE IF NOT EXISTS keys (
      id            INTEGER PRIMARY KEY AUTOINCREMENT,
      key_code      TEXT UNIQUE NOT NULL,
      resource      TEXT NOT NULL DEFAULT 'vip',
      device_serial TEXT DEFAULT NULL,
      created_at    INTEGER NOT NULL,
      expires_at    INTEGER NOT NULL,
      is_active     INTEGER NOT NULL DEFAULT 1,
      notes         TEXT DEFAULT '',
      login_count   INTEGER DEFAULT 0,
      last_login    INTEGER DEFAULT NULL
    );

    CREATE TABLE IF NOT EXISTS config (
      key   TEXT PRIMARY KEY,
      value TEXT NOT NULL
    );
  `);

  // Seed default config if empty
  const bcrypt = require('bcryptjs');
  const defaults = {
    panel_name:     process.env.PANEL_NAME     || 'ATTIC PANEL',
    admin_username: process.env.ADMIN_USERNAME || 'admin',
    admin_password: process.env.ADMIN_PASSWORD_HASH
                    || bcrypt.hashSync(process.env.ADMIN_PASSWORD || 'admin123', 10),
    salt:           process.env.SALT           || 'Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E'
  };

  for (const [k, v] of Object.entries(defaults)) {
    await db.execute({
      sql:  'INSERT OR IGNORE INTO config (key, value) VALUES (?, ?)',
      args: [k, v]
    });
  }

  console.log('✓ Turso DB ready');
}

// ── Query helpers ─────────────────────────────────────

async function all(sql, args = []) {
  const db = getClient();
  const res = await db.execute({ sql, args });
  return res.rows;          // array of row objects
}

async function get(sql, args = []) {
  const rows = await all(sql, args);
  return rows[0] || null;
}

async function run(sql, args = []) {
  const db = getClient();
  return db.execute({ sql, args });
}

module.exports = { initDB, all, get, run };
