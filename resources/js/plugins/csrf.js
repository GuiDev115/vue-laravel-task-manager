import { useCsrf } from '../composables/useCsrf.js';

export default {
    install(app) {
        // Plugin global para gerenciar CSRF token
        app.config.globalProperties.$csrf = {
            renew: async () => {
                const { renewToken } = useCsrf();
                return await renewToken();
            }
        };
        
        app.mixin({
            mounted() {
                const { csrfToken, updateAxiosToken } = useCsrf();
                if (csrfToken.value) {
                    updateAxiosToken(csrfToken.value);
                }
            }
        });
    }
};
