# 📋 Guia de Instalação e Configuração

## Sistema de Gerenciamento de Tarefas Laravel + Vue.js

Este guia fornece instruções passo a passo para instalar e configurar a aplicação de gerenciamento de tarefas desenvolvida como teste técnico.

---

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter os seguintes softwares instalados:

### **Requisitos Essenciais:**
- **PHP** >= 8.2
- **Composer** (gerenciador de dependências PHP)
- **Node.js** >= 18.x
- **NPM** ou **Yarn**
- **Banco de dados**: SQLite (padrão) ou MySQL/PostgreSQL

### **Verificar Instalações:**
```bash
# Verificar PHP
php --version

# Verificar Composer
composer --version

# Verificar Node.js
node --version

# Verificar NPM
npm --version
```

---

## 🚀 Instalação Passo a Passo

### **1. Clonar o Repositório**
```bash
git clone <url-do-repositorio>
cd vue-laravel-task-manager
```

### **2. Instalar Dependências PHP**
```bash
composer install
```

### **3. Instalar Dependências JavaScript**
```bash
npm install
```

### **4. Configurar Ambiente**
```bash
# Copiar arquivo de configuração
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate
```

### **5. Configurar Banco de Dados**

#### **Opção A: SQLite (Recomendado para desenvolvimento)**
```bash
# Criar arquivo de banco SQLite
touch database/database.sqlite
```

#### **Opção B: MySQL (Recomendado para produção)**
1. **Criar banco de dados MySQL:**
```sql
CREATE DATABASE laravel_task_manager;
```

2. **Configurar .env:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_task_manager
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

### **6. Executar Migrações e Seeders**
```bash
# Migrar tabelas e popular com dados de teste
php artisan migrate --seed
```

### **7. Compilar Assets**
```bash
# Desenvolvimento (com watch)
npm run dev

# Produção (otimizado)
npm run build
```

### **8. Iniciar Servidor**
```bash
php artisan serve
```

**Aplicação disponível em:** `http://127.0.0.1:8000`

---

## 👥 Usuários Padrão Criados

Após executar os seeders, você terá os seguintes usuários:

| Tipo | Email | Senha | Permissões |
|------|-------|-------|------------|
| **Admin** | `admin@example.com` | `password` | Todas as funcionalidades |
| **User** | `user@example.com` | `password` | Apenas tarefas próprias |

---

## ⚙️ Configurações Avançadas

### **Configurar Broadcasting (Opcional)**
Para funcionalidades em tempo real:

1. **Instalar Pusher:**
```bash
composer require pusher/pusher-php-server
npm install pusher-js laravel-echo
```

2. **Configurar .env:**
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=seu_app_id
PUSHER_APP_KEY=sua_app_key
PUSHER_APP_SECRET=seu_app_secret
PUSHER_APP_CLUSTER=mt1
```

### **Configurar Email (Opcional)**
Para notificações por email:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu_email@gmail.com
MAIL_PASSWORD=sua_senha_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### **Configurar Filas (Opcional)**
Para processamento em background:

```env
QUEUE_CONNECTION=database
```

```bash
# Criar tabelas de filas
php artisan queue:table
php artisan migrate

# Executar worker
php artisan queue:work
```

---

## 🛠️ Comandos Úteis

### **Comandos de Desenvolvimento**
```bash
# Limpar todos os caches
php artisan optimize:clear

# Recriar banco com dados frescos
php artisan migrate:fresh --seed

# Criar usuário administrador
php artisan admin:create

# Executar testes
php artisan test

# Ver todas as rotas
php artisan route:list

# Compilar assets em watch mode
npm run dev

# Modo de produção
npm run build
```

### **Comandos de Manutenção**
```bash
# Cache de configuração (produção)
php artisan config:cache

# Cache de rotas (produção)
php artisan route:cache

# Cache de views (produção)
php artisan view:cache

# Otimização completa (produção)
php artisan optimize
```

---

## 🐛 Solução de Problemas

### **Problema: Erro de Permissões**
```bash
# Linux/Mac
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Windows
# Execute como administrador e ajuste permissões
```

### **Problema: Node.js/NPM Errors**
```bash
# Limpar cache do NPM
npm cache clean --force

# Deletar node_modules e reinstalar
rm -rf node_modules package-lock.json
npm install

# Se persistir, use Yarn
npm install -g yarn
yarn install
```

### **Problema: Composer Errors**
```bash
# Atualizar Composer
composer self-update

# Limpar cache
composer clear-cache

# Reinstalar dependências
rm -rf vendor composer.lock
composer install
```

### **Problema: Banco de Dados**
```bash
# Verificar conexão
php artisan tinker
DB::connection()->getPdo();

# Reset completo do banco
php artisan migrate:fresh --seed
```

---

## 🚀 Deploy em Produção

### **1. Preparar Ambiente**
```bash
# Definir ambiente de produção
APP_ENV=production
APP_DEBUG=false

# Otimizar aplicação
php artisan optimize
composer install --optimize-autoloader --no-dev

# Compilar assets
npm run build
```

### **2. Configurar Servidor Web**

#### **Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name seudominio.com;
    root /var/www/vue-laravel-task-manager/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### **Apache Configuration (.htaccess já incluído)**

### **3. Configurar SSL (Opcional)**
```bash
# Com Certbot (Let's Encrypt)
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d seudominio.com
```

### **4. Configurar Supervisor para Filas**
```ini
# /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/vue-laravel-task-manager/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/vue-laravel-task-manager/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 📊 Monitoramento e Logs

### **Logs da Aplicação**
```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Logs por data
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log
```

### **Monitoramento de Performance**
```bash
# Instalar Laravel Telescope (desenvolvimento)
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

---

## 🔧 Customizações

### **Modificar Configurações**
- **Idioma:** Altere `APP_LOCALE` no `.env`
- **Timezone:** Altere `timezone` em `config/app.php`
- **Items por página:** Modifique controladores
- **Logo/Título:** Edite componentes Vue

### **Adicionar Funcionalidades**
1. **Criar Migration:** `php artisan make:migration`
2. **Criar Model:** `php artisan make:model`
3. **Criar Controller:** `php artisan make:controller`
4. **Criar Component:** Arquivo `.vue` em `resources/js/Components`

---

## 📞 Suporte

Se encontrar problemas durante a instalação:

1. **Verifique os pré-requisitos**
2. **Consulte a seção de solução de problemas**
3. **Verifique os logs de erro**
4. **Entre em contato com o desenvolvedor**

---

**📋 Instalação concluída! O sistema está pronto para uso.**
```bash
npm install
```

### **4. Configurar Arquivo de Ambiente**
```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate
```

---

## ⚙️ Configuração

### **1. Configurar Banco de Dados**

#### **Opção A: SQLite (Recomendado para desenvolvimento)**
```bash
# O arquivo .env já está configurado para SQLite
# Criar arquivo de banco
touch database/database.sqlite
```

#### **Opção B: MySQL**
Edite o arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

#### **Opção C: PostgreSQL**
Edite o arquivo `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=task_manager
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### **2. Executar Migrações**
```bash
# Executar migrações do banco de dados
php artisan migrate

# (Opcional) Executar seeders para dados de teste
php artisan db:seed
```

### **3. Configurar Frontend**
```bash
# Compilar assets para desenvolvimento
npm run dev

# OU compilar para produção
npm run build
```

---

## 🔧 Configurações Adicionais

### **1. Configurações de Sessão e Cache**
No arquivo `.env`, configure:
```env
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
CACHE_DRIVER=file
```

### **2. Configurações de Email (Opcional)**
Para funcionalidades de email:
```env
MAIL_MAILER=smtp
MAIL_HOST=seu_servidor_smtp
MAIL_PORT=587
MAIL_USERNAME=seu_email
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
```

### **3. Configurações de Queue (Opcional)**
Para processamento assíncrono:
```env
QUEUE_CONNECTION=database
```

---

## 🏃‍♂️ Executando a Aplicação

### **Desenvolvimento**

#### **Terminal 1: Servidor Laravel**
```bash
php artisan serve
```
Acesse: `http://127.0.0.1:8000`

#### **Terminal 2: Watch de Assets (Opcional)**
```bash
npm run dev
```

### **Produção**

#### **1. Compilar Assets**
```bash
npm run build
```

#### **2. Otimizar Laravel**
```bash
# Limpar e cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimizar autoloader
composer install --optimize-autoloader --no-dev
```

#### **3. Configurar Servidor Web**
Configure seu servidor web (Apache/Nginx) para apontar para a pasta `public/`.

---

## 👥 Configuração Inicial de Usuários

### **1. Criar Usuário Administrador**
```bash
php artisan tinker
```

```php
// No console do Tinker
$user = new App\Models\User();
$user->name = 'Administrador';
$user->email = 'admin@example.com';
$user->password = bcrypt('password123');
$user->role = 'admin';
$user->save();
```

### **2. Criar Usuário Regular**
```php
// No console do Tinker
$user = new App\Models\User();
$user->name = 'Usuário Teste';
$user->email = 'user@example.com';
$user->password = bcrypt('password123');
$user->role = 'user';
$user->save();
```

### **3. Sair do Tinker**
```php
exit
```

---

## 🔐 Configuração de Segurança

### **1. Configurar HTTPS (Produção)**
No arquivo `.env`:
```env
APP_URL=https://seu-dominio.com
SESSION_SECURE_COOKIE=true
```

### **2. Configurar CORS (Se necessário)**
```bash
php artisan vendor:publish --tag="cors"
```

### **3. Configurar Rate Limiting**
As rotas já possuem rate limiting configurado no arquivo `routes/api.php`.

---

## 🗂️ Estrutura de Arquivos Importantes

```
vue-laravel-task-manager/
├── app/
│   ├── Http/Controllers/
│   │   └── TaskController.php      # Controller principal
│   └── Models/
│       ├── Task.php                # Model de tarefas
│       └── User.php                # Model de usuários
├── database/
│   ├── migrations/                 # Migrações do banco
│   └── seeders/                    # Seeders para dados iniciais
├── resources/
│   ├── js/
│   │   └── Pages/Tasks/
│   │       └── Index.vue           # Página principal de tarefas
│   └── views/
│       └── app.blade.php           # Layout principal
├── routes/
│   ├── web.php                     # Rotas web
│   └── api.php                     # Rotas da API
├── public/                         # Arquivos públicos
└── .env                           # Configurações de ambiente
```

---

## 🧪 Testes

### **Executar Testes**
```bash
# Executar todos os testes
php artisan test

# Executar testes com cobertura
php artisan test --coverage
```

### **Criar Dados de Teste**
```bash
# Executar factory e seeders
php artisan db:seed --class=TaskSeeder
php artisan db:seed --class=UserSeeder
```

---

## 🐛 Solução de Problemas

### **Problema: Erro 500 - Internal Server Error**
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### **Problema: Erro 419 - CSRF Token Mismatch**
```bash
# Limpar sessões
php artisan session:table
php artisan migrate

# Recompilar assets
npm run build
```

### **Problema: Permissões de Arquivo**
```bash
# Configurar permissões (Linux/Mac)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### **Problema: Assets não carregam**
```bash
# Recompilar assets
npm run build

# Verificar se o link simbólico existe
php artisan storage:link
```

---

## 📚 Comandos Úteis

### **Desenvolvimento**
```bash
# Limpar todos os caches
php artisan optimize:clear

# Recriar banco de dados
php artisan migrate:fresh --seed

# Gerar nova chave da aplicação
php artisan key:generate

# Watch de mudanças nos assets
npm run dev
```

### **Manutenção**
```bash
# Colocar aplicação em manutenção
php artisan down

# Tirar aplicação de manutenção
php artisan up

# Backup do banco de dados (SQLite)
cp database/database.sqlite database/backup_$(date +%Y%m%d_%H%M%S).sqlite
```

---

## 🌐 Acesso à Aplicação

### **URLs Principais**
- **Homepage**: `http://127.0.0.1:8000`
- **Login**: `http://127.0.0.1:8000/login`
- **Dashboard**: `http://127.0.0.1:8000/dashboard`
- **Tarefas**: `http://127.0.0.1:8000/tasks`

### **Credenciais Padrão**
- **Admin**: `admin@example.com` / `password123`
- **Usuário**: `user@example.com` / `password123`

---

## 📖 Documentação Adicional

- **[Funcionalidades Implementadas](FUNCIONALIDADES_IMPLEMENTADAS.md)**
- **[Campo de Status](CAMPO_STATUS_TAREFA.md)**
- **[Correção de Erro 419](CORREÇÃO_ERRO_419.md)**
- **[Laravel Documentation](https://laravel.com/docs)**
- **[Vue.js Documentation](https://vuejs.org/guide/)**
- **[Inertia.js Documentation](https://inertiajs.com/)**

---

## 🔄 Atualização da Aplicação

### **Para atualizar uma instalação existente:**

```bash
# 1. Baixar mudanças
git pull origin main

# 2. Atualizar dependências
composer install
npm install

# 3. Executar migrações
php artisan migrate

# 4. Recompilar assets
npm run build

# 5. Limpar caches
php artisan optimize:clear
```

---

## 📞 Suporte

Para dúvidas ou problemas:

1. **Verificar logs**: `storage/logs/laravel.log`
2. **Console do navegador**: Verificar erros JavaScript
3. **Documentação**: Consultar arquivos de documentação inclusos
4. **Issues**: Reportar problemas no repositório

---

**Versão da Documentação**: 1.0  
**Última Atualização**: 24 de julho de 2025
