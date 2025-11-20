# Setup SSL/HTTPS untuk Development

Panduan lengkap untuk mengaktifkan HTTPS dengan SSL certificate pada environment development lokal.

## 🚀 Quick Start

### 1. Generate SSL Certificate

```bash
# Beri permission pada script
chmod +x docker/generate-ssl.sh

# Generate self-signed certificate
./docker/generate-ssl.sh
```

### 2. Tambahkan Domain ke `/etc/hosts`

```bash
# Edit hosts file dengan sudo
sudo nano /etc/hosts

# Tambahkan baris ini:
127.0.0.1 bkk-esemkasari.dev
```

Atau dengan satu command:

```bash
echo "127.0.0.1 bkk-esemkasari.dev" | sudo tee -a /etc/hosts
```

### 3. Rebuild Docker Containers

```bash
# Stop containers yang sedang berjalan
docker-compose down

# Rebuild dan start dengan konfigurasi SSL baru
docker-compose up -d --build
```

### 4. Akses Aplikasi

Buka browser dan akses: **https://bkk-esemkasari.dev**

⚠️ **Browser akan menampilkan warning keamanan** karena menggunakan self-signed certificate. Ini normal untuk development.

**Cara bypass warning:**
- **Chrome/Edge:** Klik "Advanced" → "Proceed to bkk-esemkasari.dev (unsafe)"
- **Firefox:** Klik "Advanced" → "Accept the Risk and Continue"

## 📋 Konfigurasi yang Telah Dibuat

### 1. SSL Certificate (`docker/ssl/`)
- `bkk-esemkasari.crt` - Certificate file
- `bkk-esemkasari.key` - Private key file
- Valid selama 365 hari

### 2. Nginx Configuration (`docker/nginx-ssl.conf`)
- ✅ HTTP → HTTPS redirect
- ✅ SSL/TLS 1.2 & 1.3
- ✅ Security headers (HSTS, X-Frame-Options, dll)
- ✅ Port 443 untuk HTTPS
- ✅ Port 80 untuk HTTP redirect

### 3. Docker Compose
- Port 80 untuk HTTP
- Port 443 untuk HTTPS
- Volume mounting untuk SSL certificates

### 4. Environment Variables (`.env`)
- `APP_URL` diubah ke `https://bkk-esemkasari.dev`

## 🔒 Security Features

### SSL/TLS Configuration
- TLS 1.2 dan 1.3 support
- Strong cipher suites
- Session caching untuk performance

### Security Headers
- `Strict-Transport-Security` - HSTS untuk force HTTPS
- `X-Frame-Options` - Proteksi dari clickjacking
- `X-Content-Type-Options` - Proteksi dari MIME sniffing
- `X-XSS-Protection` - XSS filtering

### Laravel HTTPS Configuration
- `HTTPS` parameter passed ke FastCGI
- `HTTP_SCHEME` set ke https
- Automatic HTTPS detection untuk redirects

## 🔄 Switching Between HTTP/HTTPS

### Untuk kembali ke HTTP (tanpa SSL):

1. Edit `docker-compose.yml`:
```yaml
volumes:
  - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf  # Ganti dari nginx-ssl.conf
```

2. Update `.env`:
```env
APP_URL=http://localhost:8000
```

3. Rebuild containers:
```bash
docker-compose up -d --build
```

### Untuk menggunakan HTTPS:

1. Edit `docker-compose.yml`:
```yaml
volumes:
  - ./docker/nginx-ssl.conf:/etc/nginx/conf.d/default.conf
```

2. Update `.env`:
```env
APP_URL=https://bkk-esemkasari.dev
```

3. Rebuild containers:
```bash
docker-compose up -d --build
```

## 🛠️ Troubleshooting

### Certificate Error di Browser
**Problem:** Browser menampilkan "Your connection is not private"

**Solution:** 
- Ini normal untuk self-signed certificate
- Klik "Advanced" dan lanjutkan ke situs
- Untuk production, gunakan certificate dari Let's Encrypt atau certificate authority lainnya

### Port 80/443 Already in Use
**Problem:** Port sudah digunakan aplikasi lain

**Solution:**
```bash
# Cek aplikasi yang menggunakan port
sudo lsof -i :80
sudo lsof -i :443

# Stop aplikasi tersebut atau ubah port di docker-compose.yml
```

### Domain tidak resolve
**Problem:** `bkk-esemkasari.dev` tidak bisa diakses

**Solution:**
```bash
# Pastikan entry ada di /etc/hosts
cat /etc/hosts | grep bkk-esemkasari

# Jika tidak ada, tambahkan:
echo "127.0.0.1 bkk-esemkasari.dev" | sudo tee -a /etc/hosts

# Flush DNS cache (optional, tergantung OS)
# Ubuntu/Debian:
sudo systemd-resolve --flush-caches
```

### SSL Certificate Expired
**Problem:** Certificate sudah kadaluarsa (setelah 365 hari)

**Solution:**
```bash
# Generate certificate baru
./docker/generate-ssl.sh

# Rebuild containers
docker-compose down
docker-compose up -d --build
```

## 📦 File Structure

```
bkk-esemkasari/
├── docker/
│   ├── nginx.conf           # HTTP only configuration
│   ├── nginx-ssl.conf       # HTTPS configuration (active)
│   ├── generate-ssl.sh      # SSL certificate generator
│   └── ssl/
│       ├── bkk-esemkasari.crt
│       └── bkk-esemkasari.key
├── docker-compose.yml       # Updated dengan SSL support
└── .env                     # APP_URL menggunakan HTTPS
```

## 🌐 Production Setup (Optional)

Untuk production, gunakan **Let's Encrypt** untuk SSL certificate gratis:

1. Install Certbot di server
2. Generate certificate:
```bash
sudo certbot --nginx -d yourdomain.com
```
3. Certbot akan otomatis update nginx configuration
4. Auto-renewal sudah ter-setup otomatis

## 📚 Referensi

- [Nginx SSL Configuration](https://nginx.org/en/docs/http/configuring_https_servers.html)
- [Laravel HTTPS](https://laravel.com/docs/12.x/requests#determining-if-request-is-https)
- [Let's Encrypt](https://letsencrypt.org/)
- [OpenSSL Documentation](https://www.openssl.org/docs/)

## ✅ Checklist Setup

- [ ] Generate SSL certificate dengan `./docker/generate-ssl.sh`
- [ ] Tambahkan `127.0.0.1 bkk-esemkasari.dev` ke `/etc/hosts`
- [ ] Update `.env` dengan `APP_URL=https://bkk-esemkasari.dev`
- [ ] Rebuild containers dengan `docker-compose up -d --build`
- [ ] Akses https://bkk-esemkasari.dev di browser
- [ ] Bypass certificate warning (klik Advanced)
- [ ] Verifikasi aplikasi berjalan dengan HTTPS

## 🎉 Selesai!

Aplikasi sekarang berjalan dengan SSL/HTTPS yang aman. Semua komunikasi antara browser dan server terenkripsi.
