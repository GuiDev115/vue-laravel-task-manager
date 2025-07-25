# 🔧 Comandos Úteis - Referência Rápida

## Comandos mais utilizados para o Sistema de Gerenciamento de Tarefas

---

## 🚀 Instalação Inicial

```bash
# Clonar projeto
git clone <url-do-repositorio>
cd vue-laravel-task-manager

# Instalar dependências
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar banco de dados
touch database/database.sqlite
php artisan migrate --seed

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

---

## 🔄 Desenvolvimento Diário

### **Iniciar Desenvolvimento**
```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Watch de Assets (opcional)
npm run dev
```

### **Atualizar Código**
```bash
# Baixar mudanças
git pull origin main

# Atualizar dependências
composer install
npm install

# Executar migrações
php artisan migrate

# Recompilar assets
npm run build
```

---

## 🗄️ Banco de Dados

### **Migrações**
```bash
# Executar migrações pendentes
php artisan migrate

# Executar migrações com dados iniciais
php artisan migrate --seed

# Reverter última migração
php artisan migrate:rollback

# Recriar banco do zero
php artisan migrate:fresh --seed

# Ver status das migrações
php artisan migrate:status
```

### **Seeders**
```bash
# Executar todos os seeders
php artisan db:seed

# Executar seeder específico
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=TaskSeeder

# Recriar dados de teste
php artisan migrate:fresh --seed
```

---

## 🎨 Frontend/Assets

### **Compilação**
```bash
# Desenvolvimento (com watch)
npm run dev

# Produção (otimizado)
npm run build

# Verificar sintaxe
npm run lint
```

### **Dependências JavaScript**
```bash
# Instalar nova dependência
npm install nome-do-pacote

# Atualizar dependências
npm update

# Verificar vulnerabilidades
npm audit
npm audit fix
```

---

## 🧹 Limpeza e Cache

### **Limpar Caches**
```bash
# Limpar todos os caches
php artisan optimize:clear

# Comandos individuais
php artisan cache:clear      # Cache da aplicação
php artisan config:clear     # Cache de configuração
php artisan route:clear      # Cache de rotas
php artisan view:clear       # Cache de views
php artisan event:clear      # Cache de eventos
```

### **Gerar Caches (Produção)**
```bash
# Gerar todos os caches
php artisan optimize

# Comandos individuais
php artisan config:cache     # Cachear configurações
php artisan route:cache      # Cachear rotas
php artisan view:cache       # Cachear views
php artisan event:cache      # Cachear eventos
```

---

## 🧪 Testes e Debug

### **Testes**
```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Executar com cobertura
php artisan test --coverage

# Executar teste específico
php artisan test tests/Feature/TaskTest.php
```

### **Debug**
```bash
# Console interativo
php artisan tinker

# Ver rotas
php artisan route:list

# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Informações da aplicação
php artisan about
```

---

## 🔧 Utilitários Laravel

### **Artisan Úteis**
```bash
# Gerar nova chave
php artisan key:generate

# Colocar em manutenção
php artisan down
php artisan up

# Link simbólico para storage
php artisan storage:link

# Listar comandos disponíveis
php artisan list

# Ajuda para comando específico
php artisan help migrate
```

### **Gerar Código**
```bash
# Controller
php artisan make:controller NomeController

# Model
php artisan make:model Nome

# Migration
php artisan make:migration create_table_name

# Seeder
php artisan make:seeder NomeSeeder

# Request
php artisan make:request NomeRequest

# Middleware
php artisan make:middleware NomeMiddleware
```

---

## 🐳 Docker (Se usando)

```bash
# Subir containers
docker-compose up -d

# Executar comandos no container
docker-compose exec app php artisan migrate

# Ver logs
docker-compose logs -f app

# Parar containers
docker-compose down
```

---

## 📊 Monitoramento

### **Logs**
```bash
# Ver logs recentes
tail -n 50 storage/logs/laravel.log

# Acompanhar logs em tempo real
tail -f storage/logs/laravel.log

# Limpar logs
> storage/logs/laravel.log
```

### **Performance**
```bash
# Ver informações do sistema
php artisan about

# Estatísticas de cache
php artisan cache:table

# Estatísticas de sessões
php artisan session:table
```

---

## 🔄 Git

### **Comandos Básicos**
```bash
# Status atual
git status

# Adicionar mudanças
git add .

# Commit
git commit -m "Mensagem descritiva"

# Push
git push origin main

# Pull
git pull origin main

# Ver histórico
git log --oneline
```

---

## 🚨 Emergência/Troubleshooting

### **Problemas Comuns**
```bash
# Erro de permissões
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Regenerar autoload
composer dump-autoload

# Recriar node_modules
rm -rf node_modules
npm install

# Reset completo
php artisan optimize:clear
composer dump-autoload
npm run build
```

### **Backup Rápido**
```bash
# Backup do banco SQLite
cp database/database.sqlite backup_$(date +%Y%m%d_%H%M%S).sqlite

# Backup dos uploads
tar -czf storage_backup_$(date +%Y%m%d_%H%M%S).tar.gz storage/app/public
```

---

## 📝 Comandos por Contexto

### **Primeira Instalação**
```bash
composer install && npm install && cp .env.example .env && php artisan key:generate && touch database/database.sqlite && php artisan migrate --seed && npm run build
```

### **Atualização de Projeto**
```bash
git pull && composer install && npm install && php artisan migrate && npm run build && php artisan optimize:clear
```

### **Deploy Produção**
```bash
git pull && composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan migrate --force && php artisan optimize
```

### **Reset Desenvolvimento**
```bash
php artisan migrate:fresh --seed && npm run build && php artisan optimize:clear
```

---

**💡 Dica**: Salve este arquivo como referência rápida e adicione seus próprios comandos personalizados conforme necessário.
