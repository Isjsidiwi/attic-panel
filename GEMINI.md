# ATTIC PANEL v2 — Project Context & Instructions

This document provides foundational context and instructions for the `attic-panel` project, a comprehensive key management and store panel for game modifications.

## Project Overview

`attic-panel` is a multi-role management system (Owner, Reseller, Customer) designed to handle licensing (keys), product sales, and configuration for various mobile and desktop game mods.

### Core Technologies

- **Runtime:** Node.js
- **Framework:** Express.js
- **View Engine:** EJS (Embedded JavaScript templates)
- **Styling:** Tailwind CSS (via CDN)
- **Database:** Turso (Managed SQLite) using `@libsql/client`
- **Authentication:** JWT (JSON Web Tokens) with `cookie-parser` and `cookie-session`
- **Security:** `bcryptjs` for password hashing, RSA for game communication handshakes
- **Deployment:** Vercel

### Architecture

- `app.js`: Main entry point and server configuration.
- `database.js`: DB initialization, schema management, and CRUD helpers.
- `config.js`: Dynamic configuration system stored in the database.
- `routes/`: Modularized route handlers (Admin, Auth, API, Store).
- `middleware/`: Custom middleware (e.g., authentication, flash messages).
- `services/`: Business logic for payments, GitHub integration, and order management.
- `views/`: UI templates organized by module (Admin, Store, Partials).
- `public/`: Static assets (images, CSS, JS, and some binary files).

---

## Building and Running

### Prerequisites

- Node.js (v18+ recommended)
- A Turso database instance
- Environment variables configured (see `.env.example` or `README.md`)

### Development

```bash
npm install
npm run dev
```

Starts the server using `nodemon` on port 3000 (default).

### Production

```bash
npm start
```

---

## Key Features & Workflows

### 1. Authentication & Roles

- **Owner:** Full access to dashboard, keys, users (resellers), settings, and store management.
- **Reseller:** Limited access. Can generate keys for specific games using a credit system.
- **Customer:** Accesses the `/store` to browse products and buy keys.

### 2. Key Management

- Keys are generated with specific durations (1-30 days) and assigned to games.
- Supports device locking (HWID) with configurable `max_devices`.
- Integration with a Telegram Bot for remote key generation and HWID resets.

### 3. Game API Handshake

- **RSA-AES Handshake:** The `/mod/LoginData.php` endpoint in `app.js` handles a secure handshake with game clients using RSA (`private_key.pem`) to exchange AES keys for encrypted communication.
- **Game Endpoints:** `/api/game/:gameName` endpoints handle login validation and token generation.

### 4. Database Schema

Defined in `database.js` via `initDB()`. Major tables:

- `users`: Managed roles, credits, and status.
- `keys`: License codes, expiry, device serials, and usage stats.
- `config`: Key-value pairs for panel-wide settings.
- `store_products` & `store_keys`: Inventory and stock management.

---

## Development Conventions

### Flash Messages

Use `res.flash(type, message)` to set temporary notifications. These are stored in a `_flash` cookie and consumed by the next rendered view.

### Database Operations

Prefer using the helper functions exported from `database.js`:

- `db.get(sql, args)`: Get a single row.
- `db.all(sql, args)`: Get multiple rows.
- `db.run(sql, args)`: Execute a statement (Insert, Update, Delete).

### Security

- Always use the `auth` middleware for protected routes.
- Sensitive values like `JWT_SECRET` and `SALT` must be set in environment variables.
- The `private_key.pem` is critical for game communication; ensure it is present but never exposed.

---

## Project Status & TODOs

- [x] Initial Vercel + Turso setup.
- [x] Reseller credit system.
- [x] Telegram Bot integration.
- [ ] Implement robust payment gateway (see `services/payment.js`).
- [ ] Enhance mobile responsiveness of the store.
