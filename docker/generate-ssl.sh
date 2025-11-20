#!/bin/bash

# Script to generate self-signed SSL certificate for local development
# Usage: ./docker/generate-ssl.sh

echo "🔐 Generating self-signed SSL certificate for bkk-esemkasari.dev..."

# Create ssl directory if it doesn't exist
mkdir -p docker/ssl

# Generate private key and certificate
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout docker/ssl/bkk-esemkasari.key \
    -out docker/ssl/bkk-esemkasari.crt \
    -subj "/C=ID/ST=JawaTimur/L=Bojonegoro/O=SMKN Purwosari/OU=BKK/CN=bkk-esemkasari.dev" \
    -addext "subjectAltName=DNS:bkk-esemkasari.dev,DNS:*.bkk-esemkasari.dev,DNS:localhost"

echo "✅ SSL certificate generated successfully!"
echo ""
echo "📝 Next steps:"
echo "1. Add '127.0.0.1 bkk-esemkasari.dev' to /etc/hosts"
echo "2. Run: docker-compose up -d --build"
echo "3. Access: https://bkk-esemkasari.dev"
echo ""
echo "⚠️  Note: Your browser will show a security warning because this is a self-signed certificate."
echo "   This is normal for development. Click 'Advanced' and proceed to the site."
