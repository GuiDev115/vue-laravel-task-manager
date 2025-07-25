# 📋 Sistema de Gerenciamento de Tarefas

Um sistema completo de gerenciamento de tarefas desenvolvido com **Laravel 11** (backend) e **Vue.js 3** (frontend), utilizando **Inertia.js** para uma experiência SPA seamless.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)
![Inertia.js](https://img.shields.io/badge/Inertia.js-1.x-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.x-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

---

## 🎯 Teste Técnico - Funcionalidades Implementadas

### ✅ **Requisitos Principais Concluídos**

#### **Sistema CRUD de Tarefas**
- ✅ **Criar, visualizar, editar e excluir tarefas** - Sistema completo de gerenciamento
- ✅ **Laravel + Vue.js + MySQL** - Stack tecnológica implementada conforme solicitado
- ✅ **Sistema de autenticação** - Login/logout com controle de acesso por usuário
- ✅ **Listas de tarefas personalizadas** - Cada usuário acessa apenas suas tarefas

#### **Interface de Usuário**
- ✅ **Página principal com tarefas pendentes e concluídas** - Dashboard completo
- ✅ **Adicionar tarefas com título, descrição e data de vencimento** - Apenas administradores
- ✅ **Marcar como concluída e remover tarefas** - Funcionalidade completa
- ✅ **Validação de formulário** - Campos obrigatórios e regras de negócio
- ✅ **Interface responsiva com Vue.js** - Design moderno e interativo

#### **Testes e Documentação**
- ✅ **Testes automatizados** - Testes unitários e de feature implementados
- ✅ **Documentação completa** - Guia de instalação e configuração detalhado

### ✅ **Requisitos Adicionais Implementados**

#### **Funcionalidades Avançadas**
- ✅ **Sistema de busca** - Pesquisa por título e descrição em tempo real
- ✅ **Paginação** - 5 tarefas por página com navegação intuitiva
- ✅ **Validação de data** - Data de vencimento deve ser no futuro
- ✅ **Exportação CSV** - Download de tarefas em formato planilha
- ✅ **Interface de administração** - Gestão completa de usuários e tarefas

#### **Recursos Laravel Avançados**
- ✅ **Seeders para dados iniciais** - Usuários e tarefas de exemplo
- ✅ **Artisan Commands** - Comando para criar usuários administradores
- ✅ **Events/Listeners/Jobs** - Sistema de eventos para CRUD de tarefas
- ✅ **Eventos customizados** - TaskCreated, TaskUpdated, TaskDeleted

#### **Arquitetura e Boas Práticas**
- ✅ **Middleware customizado** - Controle de acesso para administradores
- ✅ **Form Requests** - Validação organizada e reutilizável
- ✅ **Resource Controllers** - Estrutura RESTful completa
- ✅ **Factory e Seeder** - Dados de teste automatizados
- ✅ **API Routes** - Endpoints para operações AJAX

---

## ✨ Funcionalidades do Sistema

### 🎯 **Gerenciamento de Tarefas**
- **Criação de tarefas** (apenas administradores)
- **Edição e exclusão** de tarefas próprias
- **Status configurável** (Pendente/Concluída)
- **Data de vencimento** com validação de data futura
- **Atribuição de usuários** com controle de permissões
- **Descrição detalhada** e título obrigatório

### 🔐 **Sistema de Usuários**
- **Autenticação completa** (login/logout/registro)
- **Roles de usuário** (Admin/User)
- **Controle de permissões** por middleware
- **Gestão de perfil** com edição de dados
- **Interface de administração** para gestão de usuários

### 🔍 **Filtros e Busca**
- **Busca em tempo real** por título e descrição
- **Filtros por status** (Todas/Pendentes/Concluídas)
- **Ordenação inteligente** por data e prioridade

### 📊 **Recursos Avançados**
- **Exportação para CSV** com dados completos
- **Interface responsiva** para todos os dispositivos
- **Broadcasting em tempo real** com Laravel Echo
- **Validação robusta** em frontend e backend
- **Mensagens de feedback** para todas as ações

---

## 🚀 Instalação Rápida

### **1. Clonar e Instalar**
```bash
git clone git@github.com:GuiDev115/vue-laravel-task-manager.git
cd vue-laravel-task-manager
composer install
npm install
```

### **2. Configurar Ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

### **3. Configurar Banco de Dados**
```bash
# Para MySQL (produção) - configure as credenciais no .env
php artisan migrate --seed
```

### **4. Compilar Assets e Executar**
```bash
npm run build
php artisan serve
```

**Acesse**: `http://127.0.0.1:8000`

> 📖 **Para instruções detalhadas**, consulte o [**Guia de Instalação Completo**](GUIA_INSTALACAO.md)

---

## 👥 Usuários Padrão

| Tipo | Email | Senha | Permissões |
|------|-------|-------|------------|
| **Admin** | `admin@example.com` | `password` | Criar, editar, excluir todas as tarefas |
| **User** | `maria@example.com` ou `pedro@example.com` | `password` | Visualizar e gerenciar apenas tarefas próprias |

### **Criar Administrador Personalizado**
```bash
php artisan admin:create --name="Seu Nome" --email="seu@email.com" --password="suasenha"
```

---

## 🛠️ Tecnologias Utilizadas

### **Backend**
- **Laravel 11** - Framework PHP moderno
- **MySQL/SQLite** - Banco de dados relacional
- **Eloquent ORM** - Mapeamento objeto-relacional
- **Laravel Sanctum** - Autenticação API
- **Laravel Echo** - Broadcasting em tempo real

### **Frontend**
- **Vue.js 3** - Framework JavaScript reativo
- **Inertia.js** - SPA sem complexidade de API
- **Tailwind CSS** - Framework CSS utilitário
- **Vite** - Build tool rápido
- **TypeScript** - Tipagem estática

### **Ferramentas de Desenvolvimento**
- **Composer** - Gerenciador de dependências PHP
- **NPM** - Gerenciador de dependências JavaScript
- **PHPUnit** - Testes automatizados
- **Laravel Telescope** - Debug e monitoramento

---

## 📁 Estrutura do Projeto

```
vue-laravel-task-manager/
├── app/
│   ├── Console/Commands/          # Comandos Artisan customizados
│   ├── Events/                    # Eventos do sistema
│   ├── Http/
│   │   ├── Controllers/           # Controladores da aplicação
│   │   ├── Middleware/            # Middleware customizado
│   │   └── Requests/              # Form Requests para validação
│   ├── Models/                    # Modelos Eloquent
│   └── Observers/                 # Observers para monitoramento
├── database/
│   ├── factories/                 # Factories para testes
│   ├── migrations/                # Migrações do banco
│   └── seeders/                   # Seeders para dados iniciais
├── resources/
│   ├── js/
│   │   ├── Components/            # Componentes Vue reutilizáveis
│   │   ├── Layouts/               # Layouts da aplicação
│   │   ├── Pages/                 # Páginas Vue (Inertia)
│   │   └── types/                 # Tipos TypeScript
│   └── css/                       # Estilos CSS
├── routes/
│   ├── api.php                    # Rotas da API
│   ├── web.php                    # Rotas web
│   └── auth.php                   # Rotas de autenticação
└── tests/                         # Testes automatizados
```

---

## 🧪 Testes

### **Executar Testes**
```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter TaskTest

# Testes com cobertura
php artisan test --coverage
```

### **Tipos de Testes Implementados**
- **Testes de Feature** - Funcionalidades completas
- **Testes de Unit** - Componentes isolados
- **Testes de API** - Endpoints da aplicação
- **Testes de Middleware** - Controle de acesso

---

## 🚀 Comandos Artisan Customizados

### **Criar Usuário Administrador**
```bash
php artisan admin:create
```

### **Limpar Cache Completo**
```bash
php artisan optimize:clear
```

### **Migrar e Popular Banco**
```bash
php artisan migrate:fresh --seed
```

---

## 📊 Eventos e Listeners

### **Eventos Implementados**
- `TaskCreated` - Disparado ao criar uma tarefa
- `TaskUpdated` - Disparado ao atualizar uma tarefa
- `TaskDeleted` - Disparado ao excluir uma tarefa

### **Observers**
- `TaskObserver` - Monitora mudanças em tarefas automaticamente

---

## 🔒 Segurança

### **Medidas Implementadas**
- **Autenticação obrigatória** para todas as rotas protegidas
- **Autorização por roles** (Admin/User)
- **Validação de entrada** em todos os formulários
- **Proteção CSRF** em todas as requisições
- **Sanitização de dados** antes do armazenamento

---

## 🌐 API Endpoints

### **Tarefas**
```
GET    /api/tasks              # Listar tarefas do usuário
POST   /api/tasks              # Criar nova tarefa (admin)
PUT    /api/tasks/{id}          # Atualizar tarefa
DELETE /api/tasks/{id}          # Excluir tarefa
```

### **Busca e Filtros**
```
GET    /api/tasks/search?q=termo           # Buscar tarefas
GET    /api/tasks?status=completed         # Filtrar por status
GET    /api/tasks?page=2                   # Paginação
```

---

## 📈 Performance

### **Otimizações Implementadas**
- **Eager Loading** para relacionamentos
- **Paginação** para grandes datasets
- **Cache de queries** frequentes
- **Compressão de assets** com Vite
- **Lazy Loading** de componentes Vue

---

**⭐ Se este projeto foi útil, considere dar uma estrela no repositório!**
