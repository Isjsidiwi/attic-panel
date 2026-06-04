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

router.post('/LoginData.php', async (req, res) => {
  try {
    if (!privateKey) {
      console.error('[-] Server Error: Private key not loaded');
      return res.status(500).send('Internal Error: Private key missing');
    }

    const payload_a = req.body.a;
    const payload_b = req.body.b;
    const payload_c = req.body.c;

    if (!payload_a || !payload_b || !payload_c) {
      console.error('[-] Error: Missing payload_a, payload_b, or payload_c');
      return res.status(400).send('Invalid Payload');
    }

    const bufferA = Buffer.from(payload_a, 'base64');
    const bufferB = Buffer.from(payload_b, 'base64');

    let aesKey = null;
    let iv = null;

    try {
        aesKey = crypto.privateDecrypt({
            key: privateKey,
            padding: crypto.constants.RSA_PKCS1_OAEP_PADDING,
            oaepHash: 'sha256'
        }, bufferA);
        
        iv = crypto.privateDecrypt({
            key: privateKey,
            padding: crypto.constants.RSA_PKCS1_OAEP_PADDING,
            oaepHash: 'sha256'
        }, bufferB);
    } catch (e) {
    }

    if (!aesKey) {
        try {
            aesKey = crypto.privateDecrypt({
                key: privateKey,
                padding: crypto.constants.RSA_PKCS1_OAEP_PADDING,
                oaepHash: 'sha1'
            }, bufferA);
            
            iv = crypto.privateDecrypt({
                key: privateKey,
                padding: crypto.constants.RSA_PKCS1_OAEP_PADDING,
                oaepHash: 'sha1'
            }, bufferB);
        } catch (e) {
        }
    }

    if (!aesKey || !iv) {
        return res.status(400).send('Decryption failed');
    }

    const decipher = crypto.createDecipheriv('aes-128-cbc', aesKey, iv);
    let decryptedC = decipher.update(payload_c, 'base64', 'utf8');
    decryptedC += decipher.final('utf8');
    
    let parsedC;
    try {
        parsedC = JSON.parse(decryptedC);
    } catch(err) {
        return res.status(400).send('Invalid JSON in payload C');
    }

    const userKey = parsedC["hg-69"];
    const serial = parsedC["hg-70"];
    const nonce = parsedC["nonce"] || "03db1dddc8b6252003b57ceb61addb78";

    if (!userKey || !serial) {
        return res.status(400).send('Missing key or serial');
    }

    const auth = await validateAndRegisterKey(userKey, serial);

    let responseJson;
    let signatureBase64;
    
    if (auth.success) {
        responseJson = JSON.stringify({
          ConnectSt_hk: "HasBeenSucceeded",
          CurrentMatch: 52841232,
          IsVisible: 48065956,
          timestamp: Math.floor(Date.now() / 1000),
          seller: "Licencedashboard",
          panelName: "XSRC MIAW",
          Vendedor: "Licencedashboard",
          Logo: "iVBORw0KGgoAAAANSUhEUgAAAfQAAAH0CAYAAADL1t+KAAAQAElEQVR4",
          OpenUrl: 0,
          UrlOpen: "https://www.google.com",
          nonce: nonce
        });
        
        const cipher = crypto.createCipheriv('aes-128-cbc', aesKey, iv);
        let encryptedBase64 = cipher.update(responseJson, 'utf8', 'base64');
        encryptedBase64 += cipher.final('base64');

        const sign = crypto.createSign('SHA256');
        sign.update(encryptedBase64);
        sign.end();
        signatureBase64 = sign.sign(privateKey, 'base64');
        
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