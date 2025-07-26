import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

export function useCsrf() {
    const page = usePage();
    
    // Obter CSRF token da página do Inertia
    const csrfToken = computed(() => page.props.csrf_token);

    const updateAxiosToken = (token) => {
        if (window.axios && token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            
            const metaTag = document.head.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', token);
            }
        }
    };
    
    watch(csrfToken, (newToken) => {
        if (newToken) {
            updateAxiosToken(newToken);
        }
    }, { immediate: true });
    
    const renewToken = async () => {
        try {
            await window.axios.get('/sanctum/csrf-cookie');
            const newToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
            if (newToken) {
                updateAxiosToken(newToken);
            }
            return newToken;
        } catch (error) {
            console.error('Failed to renew CSRF token:', error);
            return null;
        }
    };
    
    return {
        csrfToken,
        updateAxiosToken,
        renewToken
    };
}
