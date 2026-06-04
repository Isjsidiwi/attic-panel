const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const fs = require('fs');
const path = require('path');
const { validateAndRegisterKey } = require('../../services/gameAuth');

// Muat Private Key BR Mods dari folder certs/
let privateKeyPEM = '';
try {
  const keyPath = path.join(__dirname, '../../certs/private_brmods.pem');
  if (fs.existsSync(keyPath)) {
    privateKeyPEM = fs.readFileSync(keyPath, 'utf8');
    if (privateKeyPEM.trim().length > 0) {
      console.log('[+] BR Mods: Private Key loaded successfully.');
    } else {
      console.error('[-] BR Mods: private_brmods.pem is empty.');
    }
  } else {
    console.error('[-] BR Mods: private_brmods.pem not found.');
  }
} catch (err) {
  console.error('[-] BR Mods: Failed to load private_brmods.pem:', err.message);
}

// Muat Payload Loader (3.8MB+) ke Memory
let loaderPayload = '';
try {
  const loaderPath = path.join(__dirname, '../../certs/loader.txt');
  if (fs.existsSync(loaderPath)) {
    loaderPayload = fs.readFileSync(loaderPath, 'utf8').trim();
    console.log(`[+] BR Mods: Loader payload loaded successfully (${(loaderPayload.length / 1024 / 1024).toFixed(2)} MB).`);
  } else {
    console.error('[-] BR Mods: loader.txt not found.');
  }
} catch (err) {
  console.error('[-] BR Mods: Failed to load loader.txt:', err.message);
}

// --- FUNGSI HELPER: ENKRIPSI RESPONSE (XOR + RSA Sign) ---
function createEncryptedResponse(plaintext) {
  // A. Buat Hash SHA-256 dari plaintext
  const hashHex = crypto.createHash('sha256').update(plaintext, 'utf8').digest('hex').toUpperCase();

  // B. Lakukan XOR Cipher (menggunakan Hash sebagai kunci)
  const ptBuffer = Buffer.from(plaintext, 'utf8');
  let encryptedBuffer = Buffer.alloc(ptBuffer.length);

  for (let i = 0; i < ptBuffer.length; i++) {
    let keyByte = hashHex.charCodeAt(i % hashHex.length);
    encryptedBuffer[i] = ptBuffer[i] ^ keyByte;
  }

  // C. Ubah hasil XOR ke Base64 (Variabel "Data")
  const encryptedBase64 = encryptedBuffer.toString('base64');

  // D. Buat Digital Signature dari "Data" menggunakan Private Key
  const sign = crypto.createSign('SHA256');
  sign.update(plaintext, 'utf8');
  const signatureBase64 = sign.sign(privateKeyPEM, 'base64');

  // E. Kembalikan string base64 dari JSON hasil (sebagai plaintext tunggal yang diminta)
  const finalJson = JSON.stringify({
    Data: encryptedBase64,
    Sign: signatureBase64,
    Hash: hashHex
  });
  return Buffer.from(finalJson).toString('base64');
}

// --- ENDPOINT UTAMA BR MODS ---
router.post('/b.php', async (req, res) => {
  try {
    if (!privateKeyPEM || privateKeyPEM.trim() === '') {
      console.error('[-] Server Error: Private key BR Mods not loaded / is empty');
      return res.status(500).send('Internal Error: Private key missing');
    }

    // 1. Ambil parameter 'token' dari aplikasi
    const tokenBase64 = req.body.token;
    if (!tokenBase64) return res.status(400).send('Token tidak ditemukan!');

    // 2. Buka "Kardus Luar" (Decode Base64 ke JSON)
    const outerJsonString = Buffer.from(tokenBase64, 'base64').toString('utf8');
    const outerJson = JSON.parse(outerJsonString);

    // 3. Ambil isi "Data" (Data Terenkripsi RSA)
    const encryptedDataBuffer = Buffer.from(outerJson.Data, 'base64');

    // 4. Buka Gembok RSA menggunakan Private Key Server (Mendekripsi Data)
    const decryptedBuffer = crypto.privateDecrypt(
      {
        key: privateKeyPEM,
        padding: crypto.constants.RSA_PKCS1_PADDING
      },
      encryptedDataBuffer
    );

    // 5. Parse Plaintext JSON hasil dekripsi
    const clientData = JSON.parse(decryptedBuffer.toString('utf8'));
    
    // Asumsi user memasukkan key di app_Us atau app_Pa
    const userKey = (clientData.app_Us || clientData.app_Pa || '').trim();
    const hwid = (clientData.app_ID || 'Unknown_HWID').trim();

    if (!userKey) {
      const payloadError = JSON.stringify({
        Status: 'Failed',
        MessageString: 'Username/Password (Key) kosong!',
        SubscriptionLeft: 0
      });
      return res.json(createEncryptedResponse(payloadError));
    }

    // 6. LOGIKA MANAJEMEN USER DINAMIS DARI DATABASE KITA
    const auth = await validateAndRegisterKey(userKey, hwid);

    let finalResponseObject;

    if (auth.success) {
      const { key } = auth;
      const sisaDetik = Number(key.expires_at) - Math.floor(Date.now() / 1000);
      const sisaHari = Math.max(0, Math.ceil(sisaDetik / 86400));

      const payloadSukses = JSON.stringify({
        Status: 'Success',
        Loader: loaderPayload, // Diambil dari certs/loader.txt
        MessageString: {
          Cliente: userKey,
          Dias: sisaHari.toString()
        },
        CurrUser: userKey,
        CurrPass: userKey,
        CurrVersion: '2.0',
        SubscriptionLeft: Math.max(0, sisaDetik)
      });
      
      finalResponseObject = createEncryptedResponse(payloadSukses);
    } else {
      const payloadError = JSON.stringify({
        Status: 'Failed',
        MessageString: auth.reason || 'Key tidak valid / expired!',
        SubscriptionLeft: 0
      });
      
      finalResponseObject = createEncryptedResponse(payloadError);
    }

    // 7. Kirim balasan ke Aplikasi Mod sebagai plaintext Base64
    res.send(finalResponseObject);

  } catch (error) {
    console.error('[-] BR Mods Error:', error.message);
    res.status(500).send('Internal Server Error / Invalid Payload');
  }
});

module.exports = router;