#!/bin/bash

# Script untuk start ngrok dengan production build
# Usage: ./docker/start-ngrok.sh

echo "🚀 Starting Ngrok for Production Build..."
echo ""

# Check if ngrok is installed
if ! command -v ngrok &> /dev/null; then
    echo "❌ Ngrok tidak terinstall!"
    echo "Install dengan: sudo snap install ngrok"
    exit 1
fi

# Check if Docker is running
if ! docker ps &> /dev/null; then
    echo "❌ Docker tidak running!"
    echo "Start Docker dengan: docker compose up -d"
    exit 1
fi

# Build production assets
echo "📦 Building production assets..."
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Build failed!"
    exit 1
fi

echo "✅ Build completed!"
echo ""

# Start Docker if not running
echo "🐳 Checking Docker containers..."
docker compose up -d

# Optimize Laravel
echo "⚡ Optimizing Laravel..."
docker exec -it bkk-esemkasari-app php artisan config:cache
docker exec -it bkk-esemkasari-app php artisan route:cache
docker exec -it bkk-esemkasari-app php artisan view:cache
docker exec -it bkk-esemkasari-app php artisan optimize

echo ""
echo "✅ Laravel optimized!"
echo ""
echo "🌐 Starting ngrok tunnel..."
echo ""
echo "⚠️  IMPORTANT: After ngrok starts:"
echo "1. Copy the HTTPS URL (e.g., https://abc123.ngrok-free.app)"
echo "2. Update .env: APP_URL=https://abc123.ngrok-free.app"
echo "3. Run: docker compose down lalu docker compose up -d (supaya menggunakan nginx.conf karena ssl sudah disediakan oleh ngrok,
tidak cukup menggunakan docker compose restart karena ini mengganti file config yang dipakai)"
echo ""
echo "Press Ctrl+C to stop ngrok"
echo ""

# Start ngrok dengan static subdomain
# Ganti 'abcd-abcd-abcd' dengan subdomain gratis Anda dari ngrok dashboard
ngrok http 80 --domain=abcd-abcd-abcd.ngrok-free.dev
