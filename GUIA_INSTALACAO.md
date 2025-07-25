# 📋 Guia de Instalação e Configuração

## Sistema de Gerenciamento de Tarefas Laravel + Vue.js

Este guia fornece instruções passo a passo para instalar e configurar a aplicação de gerenciamento de tarefas.

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

## 🚀 Instalação

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
