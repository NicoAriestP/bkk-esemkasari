#!/bin/bash

# Script untuk trust SSL certificate di Ubuntu/Debian
# Usage: ./docker/trust-cert.sh

echo "🔐 Adding SSL certificate to system trusted certificates..."

# Copy certificate ke system trust store
sudo cp docker/ssl/bkk-esemkasari.crt /usr/local/share/ca-certificates/bkk-esemkasari.crt

# Update CA certificates
sudo update-ca-certificates

echo "✅ Certificate added to system trust store!"
echo ""
echo "📝 Next steps:"
echo "1. Restart browser completely (close all windows)"
echo "2. For Chrome: chrome://restart"
echo "3. Access: https://bkk-esemkasari.dev"
echo ""
echo "⚠️  Note: Certificate is valid for 365 days from generation date."
