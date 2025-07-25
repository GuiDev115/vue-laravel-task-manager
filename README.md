# 📋 Sistema de Gerenciamento de Tarefas

Um sistema completo de gerenciamento de tarefas desenvolvido com **Laravel 11** (backend) e **Vue.js 3** (frontend), utilizando **Inertia.js** para uma experiência SPA seamless.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

---

## ✨ Funcionalidades Principais

### 🎯 **Gerenciamento de Tarefas**
- ✅ **Criação de tarefas** (apenas administradores)
- ✅ **Edição e exclusão** de tarefas próprias
- ✅ **Status configurável** (Pendente/Concluída)
- ✅ **Data de vencimento** com validação
- ✅ **Atribuição de usuários**
- ✅ **Descrição detalhada**

### 🔐 **Sistema de Usuários**
- ✅ **Autenticação completa** (login/logout/registro)
- ✅ **Roles de usuário** (Admin/User)
- ✅ **Controle de permissões**
- ✅ **Gestão de perfil**

### 🔍 **Filtros e Busca**
- ✅ **Busca por título e descrição**
- ✅ **Filtros por status** (Todas/Pendentes/Concluídas)
- ✅ **Ordenação inteligente**
- ✅ **Paginação**

### 📊 **Recursos Avançados**
- ✅ **Exportação para CSV**
- ✅ **Interface responsiva**
- ✅ **Modo escuro**
- ✅ **Validação em tempo real**
- ✅ **Mensagens de feedback**

---

## 🚀 Instalação Rápida

### **1. Clonar e Instalar**
```bash
git clone <url-do-repositorio>
cd vue-laravel-task-manager
composer install
npm install
```

### **2. Configurar**
```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
```

### **3. Compilar e Executar**
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
| **User** | `user@example.com` | `password` | Editar apenas tarefas próprias |

---

## 🛠️ Tecnologias Utilizadas

### **Backend**
- **Laravel 11** - Framework PHP
- **SQLite/MySQL** - Banco de dados
- **Eloquent ORM** - Mapeamento objeto-relacional
- **Laravel Sanctum** - Autenticação API

### **Frontend**
- **Vue.js 3** - Framework JavaScript
- **Inertia.js** - SPA sem API
- **Tailwind CSS** - Framework CSS
- **Vite** - Build tool

### **Ferramentas**
- **Composer** - Gerenciador de dependências PHP
- **NPM** - Gerenciador de dependências JavaScript
- **PHP CS Fixer** - Padronização de código

---

## 📁 Estrutura do Projeto

```
vue-laravel-task-manager/
├── 📂 app/
│   ├── 📂 Http/Controllers/     # Controllers da aplicação
│   └── 📂 Models/               # Models Eloquent
├── 📂 database/
│   ├── 📂 migrations/           # Migrações do banco
│   └── 📂 seeders/              # Seeders de dados
├── 📂 resources/
│   ├── 📂 js/
│   │   └── 📂 Pages/           # Componentes Vue.js
│   └── 📂 views/               # Templates Blade
├── 📂 routes/
│   ├── 📄 web.php              # Rotas web
│   └── 📄 api.php              # Rotas API
├── 📂 public/                  # Arquivos públicos
└── 📄 .env                     # Configurações
```

---

## 🎯 Como Usar

### **Para Administradores:**

1. **Criar Tarefa**
   - Clique em "Nova Tarefa"
   - Preencha título (obrigatório)
   - Adicione descrição e data de vencimento
   - Selecione usuário para atribuir
   - Defina status (Pendente/Concluída)

2. **Gerenciar Todas as Tarefas**
   - Visualizar tarefas de todos os usuários
   - Editar qualquer tarefa
   - Excluir tarefas quando necessário

### **Para Usuários:**

1. **Visualizar Tarefas**
   - Ver apenas tarefas atribuídas a você
   - Filtrar por status ou buscar

2. **Gerenciar Suas Tarefas**
   - Marcar como concluída/pendente
   - Editar informações
   - Excluir se necessário

---

## 📋 Funcionalidades Detalhadas

### **✅ Validações Implementadas**
- **Título**: Obrigatório, máximo 255 caracteres
- **Descrição**: Opcional, máximo 1000 caracteres
- **Data de vencimento**: Deve ser hoje ou futura
- **Usuário**: Obrigatório para administradores
- **Permissões**: Verificação em frontend e backend

### **🔒 Segurança**
- **CSRF Protection**: Tokens em todas as requisições
- **Autenticação**: Middleware em rotas protegidas
- **Autorização**: Controle de acesso por role
- **Validação**: Dupla validação (client/server)

### **🎨 Interface**
- **Design Responsivo**: Funciona em mobile e desktop
- **Modo Escuro**: Suporte completo
- **Feedback Visual**: Estados de loading e mensagens
- **Acessibilidade**: Labels e navegação adequados

---

## 📚 Documentação

| Documento | Descrição |
|-----------|-----------|
| [**Guia de Instalação**](GUIA_INSTALACAO.md) | Instruções completas de instalação e configuração |
| [**Funcionalidades**](FUNCIONALIDADES_IMPLEMENTADAS.md) | Detalhes de todas as funcionalidades implementadas |
| [**Campo de Status**](CAMPO_STATUS_TAREFA.md) | Documentação do campo de seleção de status |
| [**Correção 419**](CORREÇÃO_ERRO_419.md) | Solução para problemas de CSRF token |

---

## 🧪 Testes

```bash
# Executar testes
php artisan test

# Executar com cobertura
php artisan test --coverage

# Dados de teste
php artisan db:seed
```

---

## 🔧 Comandos Úteis

### **Desenvolvimento**
```bash
# Servidor de desenvolvimento
php artisan serve

# Watch de assets
npm run dev

# Limpar caches
php artisan optimize:clear
```

### **Produção**
```bash
# Compilar assets
npm run build

# Otimizar Laravel
php artisan optimize
```

---

## 🤝 Contribuição

1. **Fork** o projeto
2. **Crie** uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. **Push** para a branch (`git push origin feature/AmazingFeature`)
5. **Abra** um Pull Request

---

## 📝 Licença

Este projeto está sob a licença **MIT**. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 📞 Suporte

- **Documentação**: Consulte os arquivos de documentação inclusos
- **Issues**: Reporte problemas nas Issues do GitHub
- **Logs**: Verifique `storage/logs/laravel.log` para debugging

---

## 🎯 Roadmap

- [ ] **Notificações** por email
- [ ] **Upload** de anexos em tarefas
- [ ] **Comentários** em tarefas
- [ ] **Subtarefas**
- [ ] **Dashboard** com gráficos
- [ ] **API REST** completa
- [ ] **App Mobile** (React Native)

---

**Desenvolvido com ❤️ usando Laravel e Vue.js**

---

**Versão**: 1.0  
**Última Atualização**: 24 de julho de 2025

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
