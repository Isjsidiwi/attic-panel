const db = require('../database');

/**
 * Validates a key and registers/updates device serial.
 * @param {string} key_code The key to validate.
 * @param {string} serial The device serial (optional for some games).
 * @returns {Promise<{success: boolean, reason?: string, key?: object}>}
 */
async function validateAndRegisterKey(key_code, serial) {
  const now = Math.floor(Date.now() / 1000);

  if (!key_code) return { success: false, reason: 'Key diperlukan.' };

  const key = await db.get('SELECT * FROM keys WHERE key_code = ?', [key_code]);
  if (!key) return { success: false, reason: 'Key tidak ditemukan.' };
  if (!key.is_active) return { success: false, reason: 'Key dinonaktifkan.' };

  // Jika key belum diaktivasi (expires_at = 0), aktivasi sekarang
  if (Number(key.expires_at) === 0) {
    const duration = Number(key.duration) || 0;
    const newExpiresAt = now + duration;
    await db.run('UPDATE keys SET expires_at = ? WHERE id = ?', [newExpiresAt, key.id]);
    key.expires_at = newExpiresAt;
  }

  if (Number(key.expires_at) <= now) return { success: false, reason: 'Key sudah expired.' };

  let serials = [];
  try {
    serials = JSON.parse(key.device_serials || '[]');
  } catch {
    serials = [];
  }
  const maxDevices = Number(key.max_devices) || 1;

  if (serial) {
    if (!serials.includes(serial)) {
      if (serials.length >= maxDevices) {
        return {
          success: false,
          reason: `Batas device tercapai (${maxDevices}/${maxDevices}). Key ini sudah terkunci ke ${maxDevices} device.`,
          key
        };
      }
      serials.push(serial);
      await db.run('UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?', [
        JSON.stringify(serials),
        now,
        key.id
      ]);
    } else {
      await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, key.id]);
    }
  } else {
    await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, key.id]);
  }

  return { success: true, key };
}

module.exports = { validateAndRegisterKey };
