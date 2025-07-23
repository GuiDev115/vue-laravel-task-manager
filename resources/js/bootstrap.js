import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configurar CSRF token para requisições AJAX
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Configurar interceptor para adicionar headers de sessão do Inertia
window.axios.interceptors.request.use(function (config) {
    // Se for uma requisição dentro do contexto do Inertia, usar as credenciais da sessão
    config.withCredentials = true;
    return config;
});
