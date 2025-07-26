import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configurar para usar credenciais (cookies) com Sanctum
window.axios.defaults.withCredentials = true;

// Função para obter CSRF token atualizado
function getCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : null;
}

// Função para renovar CSRF token
async function renewCsrfToken() {
    try {
        await axios.get('/sanctum/csrf-cookie');
        const newToken = getCsrfToken();
        if (newToken) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
        }
        return newToken;
    } catch (error) {
        console.error('Failed to renew CSRF token:', error);
        return null;
    }
}

// Configurar CSRF token inicial
const initialToken = getCsrfToken();
if (initialToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = initialToken;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Configurar interceptor para requests
window.axios.interceptors.request.use(async function (config) {
    config.withCredentials = true;
    
    // Sempre obter o token mais recente
    const currentToken = getCsrfToken();
    if (currentToken) {
        config.headers['X-CSRF-TOKEN'] = currentToken;
    }
    
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Configurar interceptor para responses - renovar token em caso de erro 419
window.axios.interceptors.response.use(function (response) {
    return response;
}, async function (error) {
    const originalRequest = error.config;
    
    // Se erro 419 (CSRF token mismatch) e não é retry
    if (error.response && error.response.status === 419 && !originalRequest._retry) {
        originalRequest._retry = true;
        
        console.warn('CSRF token mismatch, attempting to renew...');
        
        const newToken = await renewCsrfToken();
        if (newToken) {
            originalRequest.headers['X-CSRF-TOKEN'] = newToken;
            return axios(originalRequest);
        }
    }
    
    if (error.response && error.response.status === 401) {
        console.warn('Unauthorized request detected. User may need to login again.');
        // Opcional: redirecionar para login
        // window.location.href = '/login';
    }
    
    return Promise.reject(error);
});
