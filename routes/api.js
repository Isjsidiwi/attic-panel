router.post('/game/MLBB', async (req, res) => {
  const userKey = (req.body.user_key || '').trim();

  res.json({
    "success": true,
    "seller": "lord",
    "version": "1.0"
  });
});
