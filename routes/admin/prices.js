const express = require('express');
const router = express.Router();
const auth = require('../../middleware/auth');
const db = require('../../database');
const { normalizePrice, GAME_OPTIONS, PRICE_DAYS } = require('../../utils/adminUtils');

const requireOwner = auth.requireOwner;

router.post('/', auth, requireOwner, async (req, res) => {
  const prices = req.body.prices || {};
  for (const game of GAME_OPTIONS) {
    const gamePrices = prices[game.value] || {};
    for (const day of PRICE_DAYS) {
      await db.run(
        'INSERT INTO key_prices (game, duration_days, price_credit) VALUES (?, ?, ?) ON CONFLICT(game, duration_days) DO UPDATE SET price_credit=excluded.price_credit',
        [game.value, day, normalizePrice(gamePrices[day])]
      );
    }
  }

  res.flash('success', 'Harga key berhasil disimpan.');
  res.redirect('/admin/settings');
});

module.exports = router;
