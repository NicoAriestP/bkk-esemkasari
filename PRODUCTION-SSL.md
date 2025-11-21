# Production SSL Setup dengan Let's Encrypt

Panduan lengkap setup SSL certificate gratis dari Let's Encrypt untuk production deployment.

## 🌐 Prerequisites

- Domain sudah pointing ke server (A record ke IP server)
- Server Ubuntu/Debian dengan akses sudo
- Port 80 dan 443 terbuka
- Nginx sudah terinstall

## 🚀 Setup Let's Encrypt (Certbot)

### 1. Install Certbot

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install certbot python3-certbot-nginx -y
```

### 2. Stop Nginx Sementara (agar port 80 bebas)

```bash
sudo systemctl stop nginx
```

### 3. Generate Certificate

```bash
# Ganti dengan domain production Anda
sudo certbot certonly --standalone -d yourdomain.com -d www.yourdomain.com

# Atau jika Nginx sudah jalan:
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

Follow the prompts:
- Email: masukkan email Anda (untuk renewal notifications)
- Terms: Agree
- Share email: pilih No/Yes

### 4. Certificate Location

Certificate akan tersimpan di:
```
Certificate: /etc/letsencrypt/live/yourdomain.com/fullchain.pem
Private Key: /etc/letsencrypt/live/yourdomain.com/privkey.pem
```

## 📝 Nginx Configuration untuk Production

```nginx
# HTTP to HTTPS redirect
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Certbot validation
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    
    # Redirect all other traffic to HTTPS
    location / {
        return 301 https://$host$request_uri;
    }
}

# HTTPS server
server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    # Let's Encrypt SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # SSL Security Settings (Mozilla Intermediate)
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    ssl_session_tickets off;
    
    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    ssl_trusted_certificate /etc/letsencrypt/live/yourdomain.com/chain.pem;
    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;
    
    # Security Headers
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    
    # Laravel Application
    root /var/www/bkk-esemkasari/public;
    index index.php index.html;
    
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param HTTPS on;
        fastcgi_param HTTP_SCHEME https;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Static assets caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Max upload size
    client_max_body_size 10M;
}
```

### 5. Test & Reload Nginx

```bash
# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

## 🔄 Auto-Renewal Setup

Certbot otomatis membuat cron job untuk renewal. Verifikasi:

```bash
# Check auto-renewal
sudo certbot renew --dry-run

# Manual renewal (jika diperlukan)
sudo certbot renew
```

Cron job location: `/etc/cron.d/certbot`

## ✅ Verifikasi SSL

### Test di Browser
1. Akses: https://yourdomain.com
2. Klik lock icon di address bar
3. Check certificate details
4. Should show: **Let's Encrypt Authority X3**

### Online SSL Test
- [SSL Labs](https://www.ssllabs.com/ssltest/) - Should get **A+ rating**
- [Why No Padlock](https://www.whynopadlock.com/)

## 🐳 Production dengan Docker

Jika deploy dengan Docker, gunakan volume untuk certificate:

```yaml
# docker-compose.prod.yml
services:
  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www/app
      - ./docker/nginx-prod.conf:/etc/nginx/conf.d/default.conf
      - /etc/letsencrypt:/etc/letsencrypt:ro  # Mount Let's Encrypt certs
    depends_on:
      - app
```

### Certificate Renewal dengan Docker

```bash
# Stop nginx container
docker compose -f docker-compose.prod.yml stop nginx

# Renew certificate di host
sudo certbot renew

# Start nginx container
docker compose -f docker-compose.prod.yml start nginx
```

## 📋 Production Checklist

- [ ] Domain A record pointing ke server IP
- [ ] Port 80 dan 443 terbuka di firewall
- [ ] Certbot terinstall
- [ ] Generate Let's Encrypt certificate
- [ ] Update Nginx config dengan path certificate
- [ ] Test Nginx config: `sudo nginx -t`
- [ ] Reload Nginx: `sudo systemctl reload nginx`
- [ ] Verify SSL di browser (no warning, green lock)
- [ ] Test auto-renewal: `sudo certbot renew --dry-run`
- [ ] Update Laravel `.env`: `APP_URL=https://yourdomain.com`
- [ ] Setup SSL Labs test untuk A+ rating

## 🛠️ Troubleshooting

### Certificate tidak bisa di-generate
```bash
# Check domain DNS
dig yourdomain.com

# Check port 80 accessible
sudo netstat -tlnp | grep :80

# Check Certbot logs
sudo tail -f /var/log/letsencrypt/letsencrypt.log
```

### Renewal gagal
```bash
# Check renewal configuration
sudo certbot certificates

# Force renewal
sudo certbot renew --force-renewal
```

### Mixed content warning
Update semua URL di aplikasi dari `http://` ke `https://` atau gunakan relative URL.

## 💰 Cost Comparison

| Provider | Annual Cost | Auto-Renewal | Wildcard Support |
|----------|-------------|--------------|------------------|
| **Let's Encrypt** | **FREE** | ✅ Yes | ✅ Yes |
| Cloudflare SSL | FREE | ✅ Yes | ✅ Yes |
| DigiCert | $200+ | ❌ Manual | ✅ Yes |
| Sectigo | $100+ | ❌ Manual | ✅ Yes |

## 🎯 Rekomendasi

**Untuk BKK Esemkasari:**
1. ✅ **Development:** Self-signed (current setup)
2. ✅ **Production:** Let's Encrypt (gratis, otomatis)
3. ✅ **Alternative:** Cloudflare SSL (gratis + CDN bonus)

## 📚 Resources

- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
- [Certbot Instructions](https://certbot.eff.org/)
- [Mozilla SSL Configuration Generator](https://ssl-config.mozilla.org/)
- [SSL Best Practices](https://github.com/ssllabs/research/wiki/SSL-and-TLS-Deployment-Best-Practices)
