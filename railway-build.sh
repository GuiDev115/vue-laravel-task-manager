#!/bin/bash
set -e

echo "🔨 Building Laravel + Vue.js application..."

# Clear npm cache
echo "📦 Clearing npm cache..."
npm cache clean --force

# Remove node_modules and package-lock.json
echo "🗑️  Removing existing node_modules..."
rm -rf node_modules package-lock.json

# Install npm dependencies with legacy peer deps
echo "📥 Installing npm dependencies..."
npm install --legacy-peer-deps --no-audit --no-fund

# Build the application
echo "🏗️  Building assets..."
npm run build

echo "✅ Build completed successfully!"
