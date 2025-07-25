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
    
    // Garantir que o CSRF token está sempre presente
    if (!config.headers['X-CSRF-TOKEN']) {
        const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            config.headers['X-CSRF-TOKEN'] = csrfToken.content;
        }
    }
    
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Configurar interceptor para tratar erros de autenticação
window.axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response && error.response.status === 401) {
        console.warn('Unauthorized request detected. User may need to login again.');
        // Opcional: redirecionar para login
        // window.location.href = '/login';
    }
    return Promise.reject(error);
});
