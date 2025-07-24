#!/bin/bash
# Script para configurar variáveis de banco no Railway

echo "🔧 Configurando variáveis de banco de dados no Railway..."

# Primeira, vamos tentar descobrir qual MySQL usar
echo "📋 Serviços MySQL disponíveis:"
railway service

echo ""
echo "📝 Você precisa seguir estes passos:"
echo ""
echo "1. No Railway Dashboard (https://railway.app):"
echo "   - Vá para seu projeto 'worthy-transformation'"
echo "   - Entre no ambiente 'production'"
echo ""
echo "2. Configure a conexão do MySQL:"
echo "   - Vá para o serviço MySQL que você criou"
echo "   - Copie as variáveis de conexão"
echo "   - Vá para o serviço 'vue-laravel-task-manager'"
echo "   - Na aba 'Variables', adicione:"
echo ""
echo "   DB_CONNECTION=mysql"
echo "   DB_HOST=[valor do MYSQLHOST do serviço MySQL]"
echo "   DB_PORT=3306"
echo "   DB_DATABASE=railway"
echo "   DB_USERNAME=root"
echo "   DB_PASSWORD=[valor do MYSQL_ROOT_PASSWORD do serviço MySQL]"
echo ""
echo "3. Ou conecte os serviços automaticamente:"
echo "   - No dashboard, vá para 'vue-laravel-task-manager'"
echo "   - Clique em 'Connect'"
echo "   - Selecione o serviço MySQL"
echo "   - Isso criará as variáveis automaticamente"

echo ""
echo "🚀 Alternativa rápida via CLI:"
echo "railway add --service MySQL"
