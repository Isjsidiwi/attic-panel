const { get, run } = require('./database');

async function loadConfig() {
  const rows = await require('./database').all('SELECT key, value FROM config');
  const cfg = {};
  rows.forEach(r => { cfg[r.key] = r.value; });
  return cfg;
}

async function saveConfig(updates) {
  for (const [k, v] of Object.entries(updates)) {
    await run(
      'INSERT INTO config (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value',
      [k, v]
    );
  }
}

module.exports = { loadConfig, saveConfig };
