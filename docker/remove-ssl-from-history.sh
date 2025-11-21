#!/bin/bash

# Script to remove SSL certificates from git history
# This will rewrite git history - use with caution!

echo "⚠️  WARNING: This will rewrite git history!"
echo "Make sure all team members are informed before proceeding."
echo ""
read -p "Are you sure you want to continue? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Aborted."
    exit 1
fi

echo ""
echo "🗑️  Removing SSL certificates from git history..."

# Remove files from all commits
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch docker/ssl/bkk-esemkasari.crt docker/ssl/bkk-esemkasari.key" \
  --prune-empty --tag-name-filter cat -- --all

echo ""
echo "✅ Files removed from git history!"
echo ""
echo "📝 Next steps:"
echo "1. Force push to remote: git push origin --force --all"
echo "2. Regenerate new SSL certificates: ./docker/generate-ssl.sh"
echo "3. Inform team members to:"
echo "   - git fetch origin"
echo "   - git reset --hard origin/dev"
echo "   - ./docker/generate-ssl.sh"
echo ""
echo "⚠️  Note: Force push will affect all branches. Coordinate with your team!"
