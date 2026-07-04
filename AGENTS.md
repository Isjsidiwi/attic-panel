# AGENTS.md — attic-panel

## Quick commands

```bash
npm install          # install deps
npm run dev          # dev server with nodemon (port 3000)
npm start            # production start
npm run lint         # eslint .  (extends: recommended + prettier)
npm run format       # prettier --write .
```

No test framework or test scripts defined.

## Architecture

- **Entry:** `app.js` — Express server, EJS views (`views/`), static files (`public/`).
- **Database:** Turso (serverless SQLite via `@libsql/client`). Init + migrations in `database.js:initDB()`. Use `ensureColumn()` for additive schema changes.
- **DB helpers:** `db.get(sql, args)`, `db.all(sql, args)`, `db.run(sql, args)` from `database.js`.
- **Config:** `config.js` reads/writes the `config` DB table. Seeded from env + defaults.
- **Auth:** JWT in `_token` cookie (httpOnly). `middleware/auth.js` protects routes; `auth.requireOwner` for owner-only. Roles: `owner` (full access) / `reseller` (keys + credit only).
- **Flash messages:** `res.flash(type, msg)` stores `_flash` cookie. Views read `res.locals.success_msg` / `error_msg`.
- **Session cookie name:** `suki_session` (for `cookie-session`).

## Key directories

| Path | Purpose |
|------|---------|
| `routes/` | Route handlers — `auth.js`, `admin/index.js` (dashboard, keys, settings, resellers, prices, profile, files), `api.js` (game API endpoints), `game_khusus/` (HG Mods, BR Mods, Valorant — custom RSA handshakes) |
| `middleware/auth.js` | JWT verification + role checks |
| `services/` | Business logic — `gameAuth.js` (key validation + device locking), `payment.js` (Orderkuota QRIS), `githubFiles.js` (GitHub contents API uploads), `storeOrders.js` (payment polling + fulfillment) |
| `views/` | EJS templates — admin (dashboard, keys, settings, etc.), store (`store/`), partials (`partials/`) |
| `certs/` | RSA private keys for game handshakes (`private_hgmods.pem`, `private_brmods.pem`, `private_auth.pem`), loader payloads. **Never expose.** |
| `public/` | Static files + `.so` library downloads. Uploads via `services/githubFiles.js` to GitHub repo. |
| `routes/store_admin/` | Store admin CRUD for products, keys, orders, referrals |
| `routes/store_index.js` | Public store frontend (products, checkout, order status) |

## Dev setup

```bash
cp .env.example .env   # fill TURSO_DATABASE_URL + TURSO_AUTH_TOKEN
npm install
npm run dev
```

`.env` is gitignored.

## Important env vars

- `TURSO_DATABASE_URL`, `TURSO_AUTH_TOKEN` — Turso connection
- `JWT_SECRET` — JWT signing key
- `GITHUB_TOKEN`, `GITHUB_OWNER`, `GITHUB_REPO`, `GITHUB_BRANCH`, `GITHUB_UPLOAD_PATH` — file upload service
- `STORE_ORDER_EXPIRE_MINUTES` — QRIS order expiry (default 10)
- `PAYMENT_MUTATION_PAGES` — pages of mutations to check (default 3)

## Conventions

- **Schema changes:** always additive. Add columns via `ensureColumn()` in `initDB()`. Never drop/recreate tables.
- **Cookie contract:** `_token` (auth), `_flash` (flash messages). Keep these names if refactoring.
- **API parameters:** endpoints accept multiple param names (`user_key`, `member_key`). Preserve response shapes for external clients.
- **Game endpoints:** `routes/game_khusus/` use custom encryption (RSA-AES for HG Mods, RSA-XOR with sign for BR Mods/Auth API). Private keys loaded from `certs/` at startup.
- **Device locking:** `keys.device_serials` is JSON array string, limited by `keys.max_devices`.
- **Login rate limit:** 5 attempts per 15 min window — `express-rate-limit` on `POST /login`. Additional in-memory username lockout (5 fails → 15 min ban).
- **Payment:** QRIS via Orderkuota API through a proxy. `services/payment.js:checkPayment()` polls mutation history. Store order flow in `services/storeOrders.js:verifyAndFulfillOrder()`.
- **Vercel:** `vercel.json` routes all traffic to `app.js`, serves `.so` files as `application/octet-stream` downloads.

## Files to read before major changes

`app.js`, `database.js`, `config.js`, `middleware/auth.js`, `services/gameAuth.js`
