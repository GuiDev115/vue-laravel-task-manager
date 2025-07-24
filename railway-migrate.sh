#!/bin/bash
set -e

echo "🔍 Verificando variáveis do banco de dados..."

# Verificar se as variáveis existem
if [ -z "$MYSQLHOST" ]; then
    echo "❌ MYSQLHOST não encontrado"
    exit 1
fi

if [ -z "$MYSQLPASSWORD" ]; then
    echo "❌ MYSQLPASSWORD não encontrado"
    exit 1
fi

echo "✅ Host: $MYSQLHOST"
echo "✅ Port: 3306"
echo "✅ Database: railway"
echo "✅ Username: root"

# Configurar variáveis do Laravel
export DB_CONNECTION=mysql
export DB_HOST=$MYSQLHOST
export DB_PORT=3306
export DB_DATABASE=railway
export DB_USERNAME=root
export DB_PASSWORD=$MYSQLPASSWORD

echo "🔗 Testando conexão com o banco..."

# Testar conexão básica
php -r "
try {
    \$pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
    echo '✅ Conexão com banco bem-sucedida!' . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Falha na conexão: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

echo "🗃️ Executando migrações..."
php artisan migrate --force

echo "🌱 Executando seeders..."
php artisan db:seed --force

echo "⚡ Otimizando Laravel para produção..."
php artisan config:cache
php artisan view:cache
# Route cache removido temporariamente devido ao conflito de nomes

echo "✅ Configuração do banco de dados concluída com sucesso!"
