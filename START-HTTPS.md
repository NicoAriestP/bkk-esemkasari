# 🚀 Panduan Lengkap: Akses Aplikasi dengan https://bkk-esemkasari.dev

## ✅ Apa yang Sudah Dikonfigurasi

1. **SSL Certificate** sudah di-generate di `docker/ssl/`
2. **Domain** `bkk-esemkasari.dev` sudah ditambahkan ke `/etc/hosts`
3. **Nginx** dikonfigurasi untuk HTTPS dengan redirect dari HTTP
4. **Vite** dikonfigurasi untuk HTTPS dengan HMR websocket secure
5. **Laravel** `APP_URL` sudah diset ke `https://bkk-esemkasari.dev`

## 🎯 Cara Menjalankan (Step-by-Step)

### 1. Start Docker Containers

```bash
docker compose up -d --build
```

Tunggu sampai containers selesai building dan running.

### 2. Setup Laravel di Container

```bash
# Masuk ke container
docker exec -it bkk-esemkasari-app bash

# Di dalam container, jalankan:
php artisan migrate
php artisan storage:link
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Keluar dari container
exit
```

### 3. Start Vite Dev Server (di Host, BUKAN di Container)

```bash
# Di terminal host (bukan di dalam Docker)
npm run dev
```

Vite akan jalan di `https://localhost:5173` dengan SSL.

### 4. Akses Aplikasi

Buka browser dan akses: **https://bkk-esemkasari.dev**

⚠️ **Penting:** Browser akan menampilkan warning SSL karena self-signed certificate:
- **Chrome/Edge:** Klik "Advanced" → "Proceed to bkk-esemkasari.dev (unsafe)"
- **Firefox:** Klik "Advanced" → "Accept the Risk and Continue"

## 📊 Penjelasan Alur Kerja

```
Browser (https://bkk-esemkasari.dev)
    ↓
Nginx (Docker Port 443) 
    ↓
Laravel PHP-FPM (Docker)
    ↓
Render Inertia View dengan Vite assets
    ↓
Vite Dev Server (https://localhost:5173) → HMR via WSS
    ↓
Browser load JS/CSS dari Vite
```

### Keuntungan Setup Ini:

✅ **Pretty URL**: Akses langsung `https://bkk-esemkasari.dev`, tidak perlu `localhost:5173`
✅ **HTTPS**: Semua komunikasi terenkripsi
✅ **HMR Working**: Hot Module Replacement tetap aktif untuk development
✅ **Production-like**: Environment mirip production tapi dengan dev tools

## 🔍 Verifikasi Setup

### Cek Docker Containers Running:
```bash
docker ps
```

Harus ada 3 containers:
- `bkk-esemkasari-app` (PHP-FPM)
- `bkk-esemkasari-nginx` (Web Server)
- `bkk-esemkasari-mysql` (Database)

### Cek Vite Dev Server:
```bash
# Harus terlihat output seperti:
# VITE v6.x.x ready in xxx ms
# ➜ Local:   https://localhost:5173/
# ➜ Network: https://0.0.0.0:5173/
```

### Cek Nginx Logs (jika ada masalah):
```bash
docker logs bkk-esemkasari-nginx
```

## 🛠️ Troubleshooting

### Problem: "Mixed Content" Error
**Solusi:** Pastikan Vite dev server jalan dengan HTTPS (sudah dikonfigurasi otomatis)

### Problem: "Cannot GET /"
**Solusi:** 
```bash
docker exec -it bkk-esemkasari-app php artisan route:clear
docker exec -it bkk-esemkasari-app php artisan config:clear
```

### Problem: Assets tidak load
**Solusi:** Pastikan `npm run dev` sedang running di host

### Problem: HMR tidak working
**Solusi:** 
1. Cek `npm run dev` jalan tanpa error
2. Cek browser console untuk error websocket
3. Pastikan tidak ada firewall blocking port 5173

### Problem: 502 Bad Gateway
**Solusi:**
```bash
# Restart containers
docker compose restart

# Cek PHP-FPM logs
docker logs bkk-esemkasari-app
```

### Problem: Database Connection Error
**Solusi:**
```bash
# Pastikan MySQL container running
docker ps | grep mysql

# Test koneksi
docker exec -it bkk-esemkasari-app php artisan migrate:status
```

## 📝 Daily Workflow

### Mulai Development:
```bash
# Terminal 1: Start Docker (jika belum running)
docker compose up -d

# Terminal 2: Start Vite
npm run dev

# Browser: https://bkk-esemkasari.dev
```

### Selesai Development:
```bash
# Stop Vite (Ctrl+C di terminal Vite)

# Stop Docker (optional, bisa tetap running)
docker compose stop
```

## 🌐 Perbedaan dengan localhost:5173

| Aspek | localhost:5173 | bkk-esemkasari.dev |
|-------|---------------|-------------------|
| Server | Vite Direct | Nginx → Laravel → Vite |
| URL | localhost:5173 | bkk-esemkasari.dev |
| HTTPS | ❌ | ✅ |
| Production-like | ❌ | ✅ |
| Backend API | Perlu proxy | Direct ✅ |
| Multi-guard Auth | ❌ | ✅ |
| File Upload | ❌ | ✅ |

## ✨ Kesimpulan

Dengan setup ini:
- ✅ Aplikasi diakses melalui **https://bkk-esemkasari.dev** (bukan localhost:5173)
- ✅ Tampilan **sama persis** dengan production
- ✅ HMR tetap aktif untuk development experience yang cepat
- ✅ Secure dengan SSL/HTTPS
- ✅ Semua fitur Laravel (auth, storage, dll) bekerja sempurna

**Tidak perlu akses localhost:5173 lagi!** 🎉
