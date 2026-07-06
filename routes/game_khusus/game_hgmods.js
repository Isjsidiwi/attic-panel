const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const fs = require('fs');
const path = require('path');
const { validateAndRegisterKey } = require('../../services/gameAuth');

let privateKey;
try {
  privateKey = fs.readFileSync(path.join(__dirname, '../../certs/private_hgmods.pem'), 'utf8');
  console.log('[+] HG Mods: Private Key loaded successfully.');
} catch (err) {
  console.error('[-] HG Mods: Failed to load private_key.pem:', err.message);
}

function rsaDecrypt(buffer) {
  for (const hash of ['sha256', 'sha1']) {
    try {
      const dec = crypto.privateDecrypt(
        { key: privateKey, padding: crypto.constants.RSA_PKCS1_OAEP_PADDING, oaepHash: hash },
        buffer
      );
      return dec;
    } catch (_) {}
  }
  return null;
}

router.post('/LoginData.php', async (req, res) => {
  try {
    if (!privateKey) {
      return res.status(500).send('Internal Error: Private key missing');
    }

    const body = req.body;
    const payloadKey = body.key || body.a || '';
    const payloadData = body.data || body.c || '';

    if (!payloadKey || !payloadData) {
      return res.status(400).send('Invalid Payload');
    }

    const encryptedKey = Buffer.from(payloadKey, 'base64');
    const decryptedKey = rsaDecrypt(encryptedKey);
    if (!decryptedKey) {
      return res.status(400).send('Decryption failed');
    }

    let aesKey, iv, encryptedData;

    if (decryptedKey.length >= 32) {
      aesKey = decryptedKey.subarray(0, 16);
      iv = decryptedKey.subarray(16, 32);
      encryptedData = Buffer.from(payloadData, 'base64');
    } else if (decryptedKey.length >= 16) {
      aesKey = decryptedKey.subarray(0, 16);
      const rawData = Buffer.from(payloadData, 'base64');
      iv = rawData.subarray(0, 16);
      encryptedData = rawData.subarray(16);
    } else {
      return res.status(400).send('Invalid key size');
    }

    let decryptedC;
    try {
      const decipher = crypto.createDecipheriv('aes-128-cbc', aesKey, iv);
      decipher.setAutoPadding(true);
      decryptedC = decipher.update(encryptedData);
      decryptedC += decipher.final('utf8');
    } catch (e) {
      return res.status(400).send('AES decryption failed');
    }

    let parsedC;
    try {
      parsedC = JSON.parse(decryptedC);
    } catch (err) {
      return res.status(400).send('Invalid JSON');
    }

    const userKey = parsedC['hg-69'] || parsedC.app_Us || parsedC.app_Pa || '';
    const serial = parsedC['hg-70'] || parsedC.app_ID || parsedC.hwid || 'Unknown';
    const nonce = parsedC['nonce'] || '03db1dddc8b6252003b57ceb61addb78';

    if (!userKey) {
      return res.status(400).send('Missing key');
    }

    const auth = await validateAndRegisterKey(userKey, serial);

    let responseJson;
    if (auth.success) {
      responseJson = JSON.stringify({
        ConnectSt_hk: 'HasBeenSucceeded',
        CurrentMatch: 52841232,
        IsVisible: 48065956,
        timestamp: Math.floor(Date.now() / 1000),
        seller: 'Licencedashboard',
        panelName: 'HG CHEAT APK MOD',
        Vendedor: 'Licencedashboard',
        Logo: 'iVBORw0KGgoAAAANSUhEUgAAAfQAAAH0CAYAAADL1t+KAAAQAElEQVR4',
        OpenUrl: 0,
        UrlOpen: 'https://www.google.com',
        nonce: nonce
      });

      const cipher = crypto.createCipheriv('aes-128-cbc', aesKey, iv);
      let encryptedBase64 = cipher.update(responseJson, 'utf8', 'base64');
      encryptedBase64 += cipher.final('base64');

      const sign = crypto.createSign('SHA256');
      sign.update(encryptedBase64);
      sign.end();
      const signatureBase64 = sign.sign(privateKey, 'base64');

      res.status(200).json({
        data: encryptedBase64,
        signature: signatureBase64
      });
    } else {
      responseJson = JSON.stringify({
        ConnectSt_hk: 'Failed',
        mensagem: auth.reason || 'XSRC MIAW',
        timestamp: Math.floor(Date.now() / 1000),
        nonce: nonce
      });

      const cipher = crypto.createCipheriv('aes-128-cbc', aesKey, iv);
      let encryptedBase64 = cipher.update(responseJson, 'utf8', 'base64');
      encryptedBase64 += cipher.final('base64');

      res.status(200).json({
        data: encryptedBase64,
        signature: '111111'
      });
    }
  } catch (e) {
    console.error('[-] HG Mods Error:', e.message);
    res.status(500).send('Internal Error: ' + e.message);
  }
});

module.exports = router;
