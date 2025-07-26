import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { router } from '@inertiajs/vue3';
import csrfPlugin from './plugins/csrf.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Configurar interceptor do Inertia para renovar CSRF token
router.on('start', () => {
    // Renovar CSRF token antes de cada navegação
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
    if (csrfToken && window.axios) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
    }
});

router.on('success', () => {
    // Atualizar CSRF token após navegação bem-sucedida
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
    if (csrfToken && window.axios) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
    }
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(csrfPlugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
