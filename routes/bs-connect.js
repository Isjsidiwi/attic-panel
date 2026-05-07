// =============================================
// routes/bs-connect.js
// Endpoint untuk Blood Strike (Hemorax)
// =============================================

const express = require('express');
const router = express.Router();

// POST https://qyz.vercel.app/api/connect
router.post('/connect', (req, res) => {
    const { game, user_key, serial } = req.body;

    // Response persis seperti yang kamu capture di Reqable
    // Hanya expired_date diubah jadi lifetime (bisa kamu ubah kapan saja)
    const response = {
        "status": true,
        "data": {
            "real": `BS-HEMORAX-VIP-${user_key ? user_key.substring(0, 8) : "CUSTOM"}-f4c61ab5-f04d-3300-b3e8-c1720ae56b64-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`,
            "token": "61a1c302db02026dc48c57f8eff693b3",
            "modname": "VIP MOD",
            "mod_status": "Safe",
            "credit": "110% SAFE",
            "ESP": "on",
            "Item": "on",
            "AIM": "on",
            "SilentAim": "on",
            "BulletTrack": "on",
            "Floating": "on",
            "Memory": "on",
            "Setting": "on",
            "expired_date": "2027-12-31 23:59:59",
            "EXP": "2027-12-31 23:59:59",
            "exdate": "2027-12-31 23:59:59",
            "device": "150",
            "rng": Math.floor(Math.random() * 9999999999)
        }
    };

    res.json(response);
});

module.exports = router;
