# Copilot instructions — attic-panel

This file contains repository-specific instructions for Copilot sessions to work efficiently.

## Quick commands
- Install dependencies: `npm install`
- Start in dev (watch): `npm run dev` (nodemon)
- Start production: `npm start`
- Tests: none defined. To run a single test, add a `test` script (e.g., using jest/mocha).
- Lint: no linter configured.

## High-level architecture
- Entry: `app.js` — Express server, EJS views (`views/`), static files served from `public/`.
- Routing: `routes/` contains `auth.js`, `admin.js`, `api.js`. `auth` handles JWT login/logout; `admin` is the UI for owners/resellers; `api` provides programmatic POST endpoints under `/api/*`.
- Data layer: `database.js` — connects to Turso via `@libsql/client`, exposes `initDB`, `all`, `get`, `run`. `initDB()` creates tables and runs simple migrations and seeds (config, owner user, key_prices).
- Configuration: `config.js` reads/writes the `config` DB table. Many config values are seeded from environment variables.
- Uploads: `public/uploads` — handled in `routes/admin.js` via multer; filenames are preserved.

## Key conventions and patterns
- Auth
  - JWT stored in cookie named `_token` (httpOnly). JWT_SECRET env var (fallback present in code).
  - `owner` vs `reseller` roles: Owners have full access; resellers have limited actions (e.g., generation constraints, no bulk editing). `middleware/auth` is used for route protection.
- Flash UI
  - Flash messages stored in `_flash` cookie as JSON {type, msg}. Handled in `app.js` (res.flash helper).
- Database shapes
  - `keys.device_serials` is stored as a JSON string (default `'[]'`). `max_devices` limits device binding. Many API routes update `login_count` and `last_login`.
  - `key_prices` holds pricing per game/duration (seeded for 1..30 days).
  - `config` table holds panel_name, admin credentials (hashed), salt, maintenance_mode, etc. Prefer changing via admin UI or `config.save` unless bootstrapping.
- API quirks
  - Endpoints accept multiple parameter names (e.g., `user_key`, `member_key`). Responses are JSON with `status` and `data`/`reason`.
  - `/api/*` routes perform device-locking logic (check/append `device_serials`) and check `is_active` / `expires_at`.
- Security and headers
  - app.js sets several security headers (CSP, HSTS in production, etc.). Keep cookie names and CSP decisions unless intentionally changing security posture.
- File uploads
  - Uploads are saved to `public/uploads` with original filename; be careful about collisions and size limits.

## Environment variables (commonly used)
- TURSO_DATABASE_URL, TURSO_AUTH_TOKEN — Turso DB client
- JWT_SECRET — JWT signing secret
- ADMIN_USERNAME, ADMIN_PASSWORD — initial owner account credentials (password used to seed DB; admin password is stored hashed in config)
- PANEL_NAME, SALT — UI and token salts
- SHOW_API_INFO — set to `1` to surface API hints in UI

## When making code changes (Copilot guidance)
- When adding or modifying schema: update `database.js:initDB()` and prefer `ensureColumn()` pattern to avoid destructive migrations.
- Use `database.{get,all,run}` helpers to interact with DB; avoid opening new DB clients.
- Preserve cookie names `_token` and `_flash` unless updating all code that reads/writes them.
- Preserve role checks in `admin.js` (owner vs reseller). Follow existing patterns for permission checks and error handling (res.flash + redirect).
- For API changes: follow existing parameter names and response shapes so external integrators remain compatible.

## Files to check before major changes
- app.js, database.js, config.js
- routes/{auth,admin,api}.js
- middleware/auth.js
- views/ and public/uploads

---

If you want, configure MCP servers (e.g., Playwright) for web testing or screenshots. Would you like help setting up any MCP servers for this project?
