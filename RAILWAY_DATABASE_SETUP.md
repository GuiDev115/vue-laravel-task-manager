# Configuração do Banco de Dados Railway - Guia Passo a Passo

## Arquivos Modificados
✅ `.env.production` - Configuração corrigida para Railway
✅ `railway-migrate.sh` - Script robusto para migrações
✅ `railway-build.sh` - Build automático com migração
✅ `test-railway-db.sh` - Script para testar conexão
✅ `routes/web.php` - Rotas temporárias para debug

## Passo 1: Testar Conexão
Execute para verificar se as variáveis estão corretas:
```bash
./test-railway-db.sh
```

## Passo 2: Executar Migrações
Opção A - Script dedicado:
```bash
railway run ./railway-migrate.sh
```

Opção B - Comando direto:
```bash
railway run php artisan migrate --force
railway run php artisan db:seed --force
```

## Passo 3: Verificar via Web (se necessário)
Se os comandos acima não funcionarem, acesse:
- `https://seu-projeto.railway.app/test-db` - Testar conexão
- `https://seu-projeto.railway.app/run-migrations` - Executar migrações

⚠️ **IMPORTANTE**: Remova essas rotas após usar!

## Passo 4: Deploy Automático
O próximo deploy executará automaticamente as migrações através do `railway-build.sh`.

## Variáveis Esperadas no Railway
```
MYSQLHOST=valor_do_railway
MYSQLPASSWORD=BDAbFmzmmpMnyAVZehBbsZvOXjYHrZvI
```

## Solução de Problemas

### Se ainda der erro de variáveis
1. Vá para Railway Dashboard
2. Variables tab
3. Adicione manualmente:
   ```
   DB_CONNECTION=mysql
   DB_HOST=valor_real_do_mysqlhost
   DB_PORT=3306
   DB_DATABASE=railway
   DB_USERNAME=root
   DB_PASSWORD=BDAbFmzmmpMnyAVZehBbsZvOXjYHrZvI
   ```

### Verificar logs
```bash
railway logs
```

## Usuários de Teste (após seeders)
- **Admin**: admin@example.com / password
- **Users**: joao@example.com, maria@example.com, pedro@example.com / password
