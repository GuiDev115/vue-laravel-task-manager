# Vue Laravel Task Manager

Uma aplicação completa de gerenciamento de tarefas (to-do list) desenvolvida com Laravel no backend e Vue.js no frontend.

## 🚀 Funcionalidades

### Características Principais
- ✅ Sistema de autenticação completo (login, registro, recuperação de senha)
- ✅ Dashboard com estatísticas em tempo real
- ✅ CRUD completo de tarefas (criar, ler, atualizar, deletar)
- ✅ Sistema de permissões (Admin/Usuário)
- ✅ Busca avançada por título e descrição
- ✅ Filtros por status (pendente/concluída)
- ✅ Paginação de resultados
- ✅ Exportação para CSV
- ✅ Interface responsiva e moderna
- ✅ Validações de formulário
- ✅ Testes automatizados

### Permissões do Sistema
- **Administradores:** Podem criar, editar, deletar e visualizar todas as tarefas de todos os usuários
- **Usuários:** Podem visualizar, editar status e deletar apenas suas próprias tarefas

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP
- **MySQL** - Banco de dados
- **Laravel Sanctum** - Autenticação API
- **Laravel Breeze** - Scaffolding de autenticação
- **Inertia.js** - Ponte entre Laravel e Vue.js

### Frontend
- **Vue.js 3** - Framework JavaScript
- **Tailwind CSS** - Framework CSS
- **Vite** - Build tool
- **Axios** - Cliente HTTP
- **Inertia.js** - SPA sem API

## 📋 Pré-requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0
- NPM ou Yarn

## 🔧 Instalação e Configuração

### 1. Clone o repositório
```bash
git clone <repository-url>
cd vue-laravel-task-manager
```

### 2. Instale as dependências do PHP
```bash
composer install
```

### 3. Instale as dependências do Node.js
```bash
npm install
```

### 4. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure o banco de dados
Edite o arquivo `.env` com suas configurações de banco:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_task_manager
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 6. Crie o banco de dados
```sql
CREATE DATABASE laravel_task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Execute as migrações e seeders
```bash
php artisan migrate
php artisan db:seed
```

### 8. Compile os assets
```bash
npm run build
```

### 9. Inicie o servidor
```bash
php artisan serve
```

A aplicação estará disponível em `http://localhost:8000`

## 👥 Usuários de Teste

Após executar os seeders, você terá os seguintes usuários disponíveis:

### Administrador
- **Email:** admin@example.com
- **Senha:** password
- **Permissões:** Acesso total ao sistema

### Usuários Regulares
- **Email:** joao@example.com | **Senha:** password
- **Email:** maria@example.com | **Senha:** password
- **Email:** pedro@example.com | **Senha:** password

## 📖 Como Usar

### 1. Faça Login
- Acesse a aplicação
- Use um dos usuários de teste ou registre-se

### 2. Dashboard
- Visualize estatísticas das suas tarefas
- Acesse rapidamente a lista de tarefas

### 3. Gerenciar Tarefas
- **Ver tarefas:** Navegue para "Tarefas" no menu
- **Filtrar:** Use os filtros de busca e status
- **Criar (Admin):** Clique em "Nova Tarefa"
- **Editar:** Clique no ícone de edição
- **Completar:** Clique no checkbox da tarefa
- **Excluir:** Clique no ícone de lixeira
- **Exportar:** Clique em "Exportar CSV"

## 🗂️ Estrutura do Projeto

```
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   └── TaskController.php
│   └── Models/
│       ├── Task.php
│       └── User.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Dashboard.vue
│   │   │   └── Tasks/
│   │   │       └── Index.vue
│   │   └── Layouts/
│   └── views/
├── routes/
│   ├── web.php
│   └── api.php
└── tests/
    └── Feature/
        └── TaskControllerTest.php
```

## 🧪 Testes

### Executar todos os testes
```bash
php artisan test
```

### Executar testes específicos
```bash
php artisan test --filter=TaskControllerTest
```

### Testes Implementados
- ✅ Admin pode criar tarefas
- ✅ Usuário comum não pode criar tarefas
- ✅ Usuário pode ver suas próprias tarefas
- ✅ Usuário pode alterar status de suas tarefas
- ✅ Usuário não pode alterar tarefas de outros
- ✅ Admin pode ver todas as tarefas
- ✅ Funcionalidade de busca
- ✅ Filtros por status

## 🔌 API Endpoints

### Autenticação
- `GET /api/user` - Informações do usuário autenticado

### Tarefas
- `GET /api/tasks` - Listar tarefas (com filtros e busca)
- `POST /api/tasks` - Criar tarefa (apenas admin)
- `GET /api/tasks/{id}` - Visualizar tarefa específica
- `PUT /api/tasks/{id}` - Atualizar tarefa
- `DELETE /api/tasks/{id}` - Excluir tarefa
- `PATCH /api/tasks/{id}/toggle` - Alternar status da tarefa
- `GET /api/tasks/export/csv` - Exportar tarefas para CSV

### Parâmetros de Filtro
- `search` - Buscar por título ou descrição
- `status` - Filtrar por status (pending/completed)
- `per_page` - Número de itens por página (padrão: 10)

## 🎨 Interface

A aplicação possui uma interface moderna e responsiva com:
- Design limpo e intuitivo
- Tema escuro/claro automático
- Componentes interativos
- Feedback visual para ações
- Navegação fluida

## 🔒 Segurança

- Autenticação baseada em sessão
- Validação de dados no frontend e backend
- Proteção CSRF
- Sanitização de entrada
- Controle de acesso baseado em roles

## 📱 Responsividade

A aplicação é totalmente responsiva e funciona perfeitamente em:
- Desktop
- Tablet
- Mobile

## 🚀 Deploy

### Produção
1. Configure o ambiente de produção
2. Execute `npm run build`
3. Configure o servidor web (Apache/Nginx)
4. Configure SSL
5. Execute as migrações: `php artisan migrate --force`
6. Configure cache: `php artisan config:cache`

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT.

---

Desenvolvido com ❤️ usando Laravel e Vue.js
