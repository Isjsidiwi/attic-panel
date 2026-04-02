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
      is_active       INTEGER NOT NULL DEFAULT 1,
      notes           TEXT DEFAULT '',
      login_count     INTEGER DEFAULT 0,
      last_login      INTEGER DEFAULT NULL
    );

    CREATE TABLE IF NOT EXISTS config (
      key   TEXT PRIMARY KEY,
      value TEXT NOT NULL
    );
  `);

  // Migration: tabel lama mungkin punya device_serial (tanpa s)
  try {
    const cols = await db.execute('PRAGMA table_info(keys)');
    const colNames = cols.rows.map(r => r.name);

    if (colNames.includes('device_serial') && !colNames.includes('device_serials')) {
      await db.execute("ALTER TABLE keys ADD COLUMN device_serials TEXT NOT NULL DEFAULT '[]'");
      await db.execute("ALTER TABLE keys ADD COLUMN max_devices INTEGER NOT NULL DEFAULT 1");
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
        await db.execute("ALTER TABLE keys ADD COLUMN max_devices INTEGER NOT NULL DEFAULT 1");
    }
  } catch (_) { /* tabel fresh, kolom sudah ada */ }

  // Seed default config
  const bcrypt = require('bcryptjs');
  const defaults = {
    panel_name:     process.env.PANEL_NAME     || 'ATTIC PANEL',
    admin_username: process.env.ADMIN_USERNAME || 'admin',
    admin_password: process.env.ADMIN_PASSWORD_HASH
                    || bcrypt.hashSync(process.env.ADMIN_PASSWORD || 'admin123', 10),
    salt:           process.env.SALT           || 'Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E'
  };

  for (const [k, v] of Object.entries(defaults)) {
    await db.execute({ sql: 'INSERT OR IGNORE INTO config (key, value) VALUES (?, ?)', args: [k, v] });
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

module.exports = { initDB, all, get, run };
