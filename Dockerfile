FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite
RUN a2enmod headers

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci --legacy-peer-deps
RUN npm run build

# Create Laravel directories
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 storage bootstrap/cache

# Configure Apache properly
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Configure Apache to use Railway's PORT
RUN echo 'Listen ${PORT:-80}' > /etc/apache2/ports.conf

# Create proper Apache config with dynamic port
COPY <<EOF /etc/apache2/sites-available/000-default.conf
<VirtualHost *:\${PORT:-80}>
    ServerName localhost
    DocumentRoot /var/www/html/public
    
    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php
        
        # Laravel URL rewriting
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
    
    # Logs
    ErrorLog /dev/stderr
    CustomLog /dev/stdout combined
    LogLevel warn
    
    # Security
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
</VirtualHost>
EOF

# Create startup script
COPY <<'EOF' /start.sh
#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Set default port if not provided
export PORT=${PORT:-80}
echo "📡 Using port: $PORT"

# Process Apache config with PORT variable
envsubst '${PORT}' < /etc/apache2/sites-available/000-default.conf > /tmp/apache-site.conf
mv /tmp/apache-site.conf /etc/apache2/sites-available/000-default.conf

# Wait for MySQL if available
if [ ! -z "$MYSQL_HOST" ]; then
    echo "⏳ Waiting for MySQL..."
    for i in {1..30}; do
        if mysqladmin ping -h"$MYSQL_HOST" -P"${MYSQL_PORT:-3306}" --silent 2>/dev/null; then
            echo "✅ MySQL is ready"
            break
        fi
        echo "Waiting for MySQL... ($i/30)"
        sleep 2
    done
fi

# Laravel setup
echo "🔧 Setting up Laravel..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Generate key if needed
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force || echo "⚠️ Migration failed, continuing..."

# Cache config for production
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Caching configuration..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Test if Laravel is working
echo "🧪 Testing Laravel..."
php artisan --version

# Create simple ping endpoint for backup health check
echo '<?php echo "PONG"; ?>' > /var/www/html/public/ping.php

# Test Apache config
apache2ctl configtest

# Start Apache
echo "🌐 Starting Apache on port $PORT..."
exec apache2-foreground
EOF

RUN chmod +x /start.sh

# Install envsubst for port substitution
RUN apt-get update && apt-get install -y gettext-base && apt-get clean

# Remove old healthcheck since we're disabling it
# HEALTHCHECK --interval=30s --timeout=10s --start-period=120s --retries=3 \
#     CMD curl -f http://localhost/health.php || curl -f http://localhost/ping.php || exit 1

EXPOSE ${PORT:-80}
CMD ["/start.sh"]