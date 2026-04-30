const express = require('express');
const router = express.Router();

router.post('/game/MLBB', async (req, res) => {
  // RESPONSE SUPER MINIMAL — ini yang paling aman untuk parser native di libngotran.so
  res.json({
    "success": true,
    "seller": "lord",
    "version": "1.0"
  });
});

module.exports = router;
