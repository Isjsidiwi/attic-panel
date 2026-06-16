const crypto = require('crypto');

// 1. Master Key (AES-256) yang baru saja Anda rampok dari RAM
const keyHex = "19030475121ca431710fe6ff29a8bb56cb139c41a357974866af57c4724b8132";
const key = Buffer.from(keyHex, 'hex');

// 2. Payload respons "success" Anda yang lama dari Reqable
const payloadBase64 = "Q9yUR+VEwHg29pfir7fx74zSMpiMMBgvRLdVUsaup12/ac6oEGeif5t71b9kYwcvPZ+9FXLse/N2adJVbYFPl/YuCdyUvtes2QlwKSC8UjgtqMJ8OpCmWW9wxDcOXA7LEn8BBn==";
const payloadBuffer = Buffer.from(payloadBase64, 'base64');

// 3. Membedah Struktur Memori C++ (Header 52 Byte)
const magicBytes = payloadBuffer.slice(0, 4);      // 4 Byte Awal (Q9yU)
const hash       = payloadBuffer.slice(4, 36);     // 32 Byte HMAC
const iv         = payloadBuffer.slice(36, 52);    // 16 Byte IV yang dikirim server!
const ciphertext = payloadBuffer.slice(52);        // Sisanya adalah Data Enkripsi murni

console.log("[*] Membedah Payload...");
console.log("[+] Magic Bytes :", magicBytes.toString());
console.log("[+] IV Server   :", iv.toString('hex'));

// 4. Proses Dekripsi
try {
    // OpenSSL biasanya menggunakan AES-256-CBC secara bawaan
    const decipher = crypto.createDecipheriv('aes-256-cbc', key, iv);
    
    let decrypted = decipher.update(ciphertext, undefined, 'utf8');
    decrypted += decipher.final('utf8');

    console.log("\n======================================");
    console.log("[!] BINGO! DEKRIPSI BERHASIL:");
    console.log(decrypted);
    console.log("======================================\n");
} catch (e) {
    console.log("\n[-] Dekripsi CBC gagal (Error: " + e.message + "). Mencoba mode CTR...");
    try {
        // Jika terjadi error 'bad decrypt', ada kemungkinan server memakai mode CTR
        const decipherCtr = crypto.createDecipheriv('aes-256-ctr', key, iv);
        let decryptedCtr = decipherCtr.update(ciphertext, undefined, 'utf8');
        decryptedCtr += decipherCtr.final('utf8');
        
        console.log("\n======================================");
        console.log("[!] BINGO! DEKRIPSI BERHASIL (Mode CTR):");
        console.log(decryptedCtr);
        console.log("======================================\n");
    } catch(err2) {
        console.log("[-] Dekripsi total gagal. Pastikan payload utuh.");
    }
}