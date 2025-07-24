#!/bin/bash
set -e

echo "🔍 Checking database variables..."
echo "MYSQLHOST: $MYSQLHOST"
echo "MYSQLPORT: $MYSQLPORT"
echo "MYSQLDATABASE: $MYSQLDATABASE"
echo "MYSQLUSER: $MYSQLUSER"

# Set Laravel database variables explicitly
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
    exit(1);
}
"

echo "🗃️ Running migrations..."
php artisan migrate --force

echo "🌱 Running seeders..."
php artisan db:seed --force

echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Database setup complete!"
