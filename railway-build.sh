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
    echo "� Setting up database variables..."
    export DB_CONNECTION=mysql
    export DB_HOST=$MYSQLHOST
    export DB_PORT=3306
    export DB_DATABASE=railway
    export DB_USERNAME=root
    export DB_PASSWORD=$MYSQLPASSWORD
    
    echo "🔗 Testing database connection..."
    php -r "
    try {
        \$pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
        echo '✅ Database connection successful!' . PHP_EOL;
    } catch (Exception \$e) {
        echo '❌ Database connection failed: ' . \$e->getMessage() . PHP_EOL;
        echo 'Continuing without database operations...' . PHP_EOL;
        exit 0;
    }
    "
    
    echo "�🗃️  Running database migrations..."
    php artisan migrate --force
    
    echo "🌱 Running database seeders..."
    php artisan db:seed --force
    
    echo "⚡ Optimizing Laravel..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "✅ Build completed successfully!"