const express = require('express');
const router = express.Router();

router.post('/game/MLBB', async (req, res) => {
  res.json({ "success": true, "seller": "lord", "version": "1.0" });
});

module.exports = router;
