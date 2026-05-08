# ATTIC PANEL v2 — Vercel + Turso

## Deploy ke Vercel

### 1. Siapkan Turso
Di Vercel Dashboard → Storage → pilih database Turso kamu → **Connect Project** → pilih project ini.
Ini otomatis inject `TURSO_DATABASE_URL` dan `TURSO_AUTH_TOKEN` ke environment.

### 2. Tambah Environment Variables
Di Vercel → Settings → Environment Variables, tambahkan:

| Key | Value |
|-----|-------|
| `JWT_SECRET` | random string panjang, misal `openssl rand -hex 32` |
| `ADMIN_USERNAME` | `admin` |
| `ADMIN_PASSWORD` | `admin123` |
| `PANEL_NAME` | `ATTIC PANEL` |
| `SALT` | `Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E` |

> Setelah deploy pertama, langsung ganti password via halaman Settings.

### 3. Push ke GitHub → Vercel auto-deploy

```bash
git init
git add .
git commit -m "attic panel vercel"
git remote add origin https://github.com/user/repo.git
git push -u origin main
```

Buka Vercel → Import Repository → selesai.

---

## Dev lokal

```bash
cp .env.example .env
# isi .env dengan kredensial Turso kamu
npm install
npm run dev
```

## API

```
POST /api/game/MLBB
Body: user_key, serial, resource
```
