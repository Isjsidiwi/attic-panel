const express = require('express');
const router = express.Router();

/* =============================================
   ENDPOINT UTAMA UNTUK CHEAT MLBB
   ============================================= */
router.post('/game/MLBB', async (req, res) => {
    const userKey = (req.body.user_key || '').trim();

    // Response yang sesuai dengan parser native (sub_41238)
    res.json({
        "success": true,
        "session_token": "eaf8a86b70cc9b566a3a424a323d452b80ea02b77228a2c04e45d87c78455a2a",
        "name_key": userKey || "ATTIC-4N3E-J2EB-CG8Q",
        "expiry_date": "2026-12-31 23:59:59",
        "remaining_devices": "99/100",
        "vip": "NO",
        "file_url": null,
        "version": "1.0",
        "announcement": "NEW UPDATE SOON ?",
        "seller": "lord"
    });
});

module.exports = router;
