const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const fs = require('fs');
const path = require('path');
const { validateAndRegisterKey } = require('../services/gameAuth');

// --- FUNGSI HELPER: ENKRIPSI AES UNTUK LIBRARY (.so) ---
// Menggunakan AES/ECB/PKCS5Padding dengan kunci hardcoded aplikasi
function encryptLib(buffer) {
  // Gunakan aes-128-ecb (panjang kunci 16 byte)
  const cipher = crypto.createCipheriv('aes-128-ecb', Buffer.from('22P9ULFDKPJ70G46', 'utf8'), null);
  let encrypted = cipher.update(buffer);
  encrypted = Buffer.concat([encrypted, cipher.final()]);
  return encrypted.toString('base64'); // Return langsung sebagai Base64
}

// Muat Private Key Auth API dari folder certs/
let privateKeyPEM = '';
try {
  const keyPath = path.join(__dirname, '../certs/private_auth.pem');
  if (fs.existsSync(keyPath)) {
    privateKeyPEM = fs.readFileSync(keyPath, 'utf8');
    if (privateKeyPEM.trim().length > 0) {
      console.log('[+] Auth API: Private Key loaded successfully.');
    } else {
      console.error('[-] Auth API: private_auth.pem is empty.');
    }
  } else {
    console.error('[-] Auth API: private_auth.pem not found.');
  }
} catch (err) {
  console.error('[-] Auth API: Failed to load private_auth.pem:', err.message);
}

// Muat File Library (.so) ke Memory lalu Enkripsi
let base64LibCheat = '';
let base64LibOri = '';
try {
  // Pastikan Anda sudah meletakkan file libcheat.so dan libori.so di folder certs
  const cheatPath = path.join(__dirname, '../certs/libcheat.so');
  const oriPath = path.join(__dirname, '../certs/libori.so');

  if (fs.existsSync(cheatPath) && fs.existsSync(oriPath)) {
    // 1. Baca sebagai Buffer binary (bukan utf8)
    const cheatBuffer = fs.readFileSync(cheatPath);
    const oriBuffer = fs.readFileSync(oriPath);

    // 2. Enkripsi Buffer dengan AES lalu jadikan Base64
    base64LibCheat = encryptLib(cheatBuffer);
    base64LibOri = encryptLib(oriBuffer);

    console.log(`[+] Auth API: Libraries loaded & AES encrypted successfully.`);
  } else {
    console.error('[-] Auth API: File libcheat.so atau libori.so tidak ditemukan di folder certs!');
  }
} catch (err) {
  console.error('[-] Auth API: Failed to load and encrypt libraries:', err.message);
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
  sign.update(plaintext, 'utf8'); // Sesuai logika C++ yang men-verifikasi sign ke plaintext(decdata)
  const signatureBase64 = sign.sign(privateKeyPEM, 'base64');

  // E. Kembalikan string base64 dari JSON hasil (sebagai plaintext tunggal yang diminta)
  const finalJson = JSON.stringify({
    Data: encryptedBase64,
    Sign: signatureBase64,
    Hash: hashHex
  });
  return Buffer.from(finalJson).toString('base64');
}

// --- ENDPOINT UTAMA AUTH API ---
router.post('/login', async (req, res) => {
  try {
    if (!privateKeyPEM || privateKeyPEM.trim() === '') {
      console.error('[-] Server Error: Private key Auth API not loaded / is empty');
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
        MessageString: 'Username/Password (Key) kosong!'
      });
      return res.send(createEncryptedResponse(payloadError));
    }

    // 6. LOGIKA MANAJEMEN USER DINAMIS DARI DATABASE KITA
    const auth = await validateAndRegisterKey(userKey, hwid);

    let finalResponseObject;

    if (auth.success) {
      const { key } = auth;
      const sisaDetik = Number(key.expires_at) - Math.floor(Date.now() / 1000);
      const sisaHari = Math.max(0, Math.ceil(sisaDetik / 86400));

      // [PERUBAHAN] Disesuaikan dengan kebutuhan C++ (Login.h / Auth.h)
      const payloadSukses = JSON.stringify({
        Status: 'Success',
        lib_cheat: base64LibCheat,
        lib_original: base64LibOri,
        MessageString: {
          Cliente: userKey,
          Dias: sisaHari.toString()
        }
      });

      finalResponseObject = createEncryptedResponse(payloadSukses);
    } else {
      const payloadError = JSON.stringify({
        Status: 'Failed',
        MessageString: auth.reason || 'Key tidak valid / expired!' // Harus berupa string
      });

      finalResponseObject = createEncryptedResponse(payloadError);
    }

    // 7. Kirim balasan ke Aplikasi Mod sebagai plaintext Base64
    res.send(finalResponseObject);
  } catch (error) {
    console.error('[-] Auth API Error:', error.message);
    res.status(500).send('Internal Server Error / Invalid Payload');
  }
});

module.exports = router;
