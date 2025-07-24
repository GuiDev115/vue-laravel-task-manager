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

# Run Laravel migrations (only if database is available)
if [ "$RAILWAY_ENVIRONMENT" = "production" ]; then
    echo "🗃️  Running database migrations..."
    php artisan migrate --force
    
    echo "🌱 Running database seeders..."
    php artisan db:seed --force
    
    echo "⚡ Optimizing Laravel..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "✅ Build completed successfully!"