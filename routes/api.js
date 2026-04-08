const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const fs = require('fs');
const path = require('path');
const db = require('../database');
const { loadConfig } = require('../config');

let privateKey = '';
try {
  privateKey = fs.readFileSync(path.join(__dirname, '../private_key.pem'), 'utf8');
} catch (err) {
  console.warn('⚠️ WARNING: private_key.pem tidak ditemukan. API BYOND tidak akan berfungsi.');
}

router.post('/game/MLBB', async (req, res) => {
  const userKey = (req.body.user_key || '').trim();
  const serial = (req.body.serial || '').trim();
  const resource = (req.body.resource || '').trim();
  const now = Math.floor(Date.now() / 1000);

  const fail = (reason) => res.json({ status: false, reason, data: null });

  if (!userKey) return fail('user_key diperlukan');

  const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);
  if (!row) return fail('Key tidak ditemukan');
  if (!row.is_active) return fail('Key dinonaktifkan');
  if (Number(row.expires_at) <= now) return fail('Key sudah expired');

  let serials = [];
  try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
  const maxDevices = Number(row.max_devices) || 1;

  if (serial) {
    if (serials.includes(serial)) {
    } else if (serials.length >= maxDevices) {
      return fail(`Batas device tercapai (${maxDevices}/${maxDevices}). Key ini sudah terkunci ke ${maxDevices} device.`);
    } else {
      serials.push(serial);
      await db.run(
        'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
        [JSON.stringify(serials), now, row.id]
      );
    }
  } else {
    await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
  }

  const cfg = await loadConfig();
  const raw = `MLBB-${userKey}-${serial}-${resource}-${cfg.salt}`;
  const token = crypto.createHash('md5').update(raw).digest('hex');

  const expiredStr = new Date(Number(row.expires_at) * 1000)
    .toISOString().replace('T', ' ').slice(0, 19);

  res.json({
    status: true,
    reason: 'Login Success',
    data: { token, rng: Number(row.expires_at), tittle: 'Provided by Xsrc & Shannz', expired: expiredStr }
  });
});

router.post('/game/BEYOND-LOGINN', async (req, res) => {
  try {
    if (!privateKey) {
      return res.status(500).send("Server Error: Private Key not configured.");
    }

    const rawToken = req.body.token;
    if (!rawToken) return res.status(400).send("No token provided");

    let decryptedJsonStr = '';
    try {
      const encryptedBuffer = Buffer.from(rawToken, 'base64');
      const decryptedBuffer = crypto.privateDecrypt({
        key: privateKey,
        padding: crypto.constants.RSA_PKCS1_PADDING
      }, encryptedBuffer);
      decryptedJsonStr = decryptedBuffer.toString('utf8');
    } catch (err) {
      console.error("❌ Gagal dekripsi RSA:", err.message);
      return res.status(400).send("Invalid Token");
    }

    const reqData = JSON.parse(decryptedJsonStr);
    const userKey = reqData.uname;
    const serial  = reqData.cs;
    
    if (!userKey) return res.status(400).send("Invalid Request Data");

    const sendByondResponse = (status, msg, user, expiryDate, daysLeft) => {
      const responseObj = {
        "Status": status,
        "MessageString": msg,
        "Usuario": user,
        "Username": user,
        "SubsbriptionLeft": expiryDate || "2000-01-01 00:00:00",
        "Validade": expiryDate || "2000-01-01 00:00:00",
        "Vendedor": "Admin-ATTIC",
        "RegisterDate": "2024-01-01 00:00:00",
        "Dias": daysLeft || "0 dias restantes"
      };

      const jsonBuffer = Buffer.from(JSON.stringify(responseObj), 'utf8');
      const hashString = crypto.createHash('sha256').update(jsonBuffer).digest('hex').toUpperCase();
      const hashBuffer = Buffer.from(hashString, 'utf8');

      let xorBuffer = Buffer.alloc(jsonBuffer.length);
      for (let i = 0; i < jsonBuffer.length; i++) {
        xorBuffer[i] = jsonBuffer[i] ^ hashBuffer[i % hashBuffer.length];
      }

      const dataBase64 = xorBuffer.toString('base64');
      const signBase64 = crypto.sign("sha256", Buffer.from(dataBase64), privateKey).toString('base64');

      const finalRes = {
        "Data": dataBase64,
        "Sign": signBase64,
        "Hash": hashString
      };

      const sendPayload = Buffer.from(JSON.stringify(finalRes)).toString('base64');
      res.setHeader('Content-Type', 'text/plain');
      return res.send(sendPayload);
    };

    const now = Math.floor(Date.now() / 1000);
    const row = await db.get('SELECT * FROM keys WHERE key_code = ?', [userKey]);

    if (!row) return sendByondResponse("Failed", "Key tidak ditemukan bosku!", userKey);
    if (!row.is_active) return sendByondResponse("Failed", "Key dinonaktifkan!", userKey);
    if (Number(row.expires_at) <= now) return sendByondResponse("Failed", "Key sudah expired!", userKey);

    let serials = [];
    try { serials = JSON.parse(row.device_serials || '[]'); } catch { serials = []; }
    const maxDevices = Number(row.max_devices) || 1;

    if (serial) {
      if (!serials.includes(serial)) {
        if (serials.length >= maxDevices) {
          return sendByondResponse("Failed", `Batas device tercapai (${maxDevices}/${maxDevices})`, userKey);
        } else {
          serials.push(serial);
          await db.run(
            'UPDATE keys SET device_serials=?, login_count=login_count+1, last_login=? WHERE id=?',
            [JSON.stringify(serials), now, row.id]
          );
        }
      } else {
        await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
      }
    } else {
      await db.run('UPDATE keys SET login_count=login_count+1, last_login=? WHERE id=?', [now, row.id]);
    }

    const d = new Date(Number(row.expires_at) * 1000);
    const pad = (n) => String(n).padStart(2, '0');
    const expiryStr = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    const diffTime = Math.abs(d - new Date());
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const daysLeftStr = `${diffDays} hari tersisa`;

    console.log(`✅ [BYOND] Login sukses: ${userKey} | Device: ${serial}`);
    return sendByondResponse("Success", "Login VIP Berhasil Bosku!", userKey, expiryStr, daysLeftStr);

  } catch (e) {
    console.error("🚨 Error BYOND:", e.message);
    res.status(500).send("Error");
  }
});

module.exports = router;