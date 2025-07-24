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
    echo "🗃️ Configurando banco de dados..."
    
    # Aguardar banco estar disponível
    echo "⏳ Aguardando banco de dados estar pronto..."
    for i in {1..30}; do
        if php -r "try { new PDO('mysql:host=' . getenv('MYSQLHOST') . ';port=3306;dbname=railway', 'root', getenv('MYSQLPASSWORD')); echo 'ready'; exit(0); } catch(Exception \$e) { exit(1); }" 2>/dev/null; then
            echo "✅ Banco de dados está pronto!"
            break
        fi
        echo "Tentativa $i/30 - Aguardando banco..."
        sleep 10
    done
    
    # Executar script de migração
    echo "🚀 Executando script de migração..."
    ./railway-migrate.sh
    
    # Otimização final (só após migrações)
    echo "⚡ Otimização final..."
    php artisan config:cache
    php artisan view:cache
    # Não fazer route:cache ainda devido ao conflito
fi

echo "✅ Build completed successfully!"