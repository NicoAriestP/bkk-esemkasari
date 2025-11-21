# SSL Certificates Directory

This directory contains SSL certificates for local HTTPS development.

## ⚠️ IMPORTANT SECURITY NOTICE

**Never commit SSL certificates or private keys to version control!**

These files are automatically ignored by `.gitignore`:
- `*.crt` - Certificate files
- `*.key` - Private key files
- `*.pem` - PEM format files

## 🔐 Generating Certificates

Each developer must generate their own SSL certificates locally:

```bash
# Run the certificate generation script
./docker/generate-ssl.sh
```

This will create:
- `bkk-esemkasari.crt` - Self-signed certificate
- `bkk-esemkasari.key` - Private key

## 📝 Setup Instructions

1. Generate certificates (first time only):
   ```bash
   chmod +x docker/generate-ssl.sh
   ./docker/generate-ssl.sh
   ```

2. Add domain to `/etc/hosts`:
   ```bash
   echo "127.0.0.1 bkk-esemkasari.dev" | sudo tee -a /etc/hosts
   ```

3. Trust certificate (optional, to avoid browser warnings):
   ```bash
   chmod +x docker/trust-cert.sh
   ./docker/trust-cert.sh
   ```

4. Start Docker containers:
   ```bash
   docker compose up -d --build
   ```

## 🗑️ Cleanup

If you need to regenerate certificates:

```bash
# Remove old certificates
rm docker/ssl/bkk-esemkasari.crt docker/ssl/bkk-esemkasari.key

# Generate new ones
./docker/generate-ssl.sh
```

## 🚨 If Certificates Were Accidentally Committed

If SSL certificates were accidentally pushed to the repository:

1. **Regenerate new certificates immediately** (old ones are compromised)
2. Remove from git history (already done via `.gitignore`)
3. Force push to remove from remote repository
4. Inform team members to regenerate their local certificates

**Remember:** Self-signed certificates for development are not as critical as production certificates, but it's still best practice to never commit them.
