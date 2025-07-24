#!/bin/bash
# Script para testar conexão com banco Railway

echo "🔍 Testando variáveis do Railway..."

echo "Verificando variáveis disponíveis:"
railway run env | grep MYSQL || echo "Nenhuma variável MYSQL encontrada"

echo ""
echo "Testando conexão com banco:"
railway run php -r "
echo 'Host: ' . getenv('MYSQLHOST') . PHP_EOL;
echo 'Port: 3306' . PHP_EOL;
echo 'Database: railway' . PHP_EOL;
echo 'Username: root' . PHP_EOL;
echo 'Password: ' . (getenv('MYSQLPASSWORD') ? 'SET' : 'NOT SET') . PHP_EOL;
echo '' . PHP_EOL;

try {
    \$host = getenv('MYSQLHOST');
    \$password = getenv('MYSQLPASSWORD');
    
    if (!$host || !$password) {
        throw new Exception('Variáveis MYSQLHOST ou MYSQLPASSWORD não encontradas');
    }
    
    \$pdo = new PDO('mysql:host=' . \$host . ';port=3306;dbname=railway', 'root', \$password);
    echo '✅ Conexão bem-sucedida!' . PHP_EOL;
    
    // Testar se consegue listar tabelas
    \$stmt = \$pdo->query('SHOW TABLES');
    \$tables = \$stmt->fetchAll();
    echo 'Tabelas encontradas: ' . count(\$tables) . PHP_EOL;
    
} catch (Exception \$e) {
    echo '❌ Erro de conexão: ' . \$e->getMessage() . PHP_EOL;
}
"
