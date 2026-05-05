// routes/senju.js
const express = require('express');
const router = express.Router();

// exe.php → validasi key
router.get('/PointB/Senju/exe.php', (req, res) => {
    const key = req.query.key || req.query.license || '';

    // Cek key di database Turso kamu
    // Contoh sederhana dulu:
    if (key === 'KEY-ABC123' || key === 'LIFETIME-001') {
        res.send('SUCCESS');           // ← Response yang cheat harapkan
    } else {
        res.send('INVALID_KEY');
    }
});

// dll.php → kirim file DLL
router.get('/PointB/Senju/dll.php', (req, res) => {
    res.sendFile(__dirname + '/../public/cheat.dll'); // upload file DLL kamu ke folder public
});

module.exports = router;
