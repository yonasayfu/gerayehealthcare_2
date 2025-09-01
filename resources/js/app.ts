import '../css/app.css';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h, defineAsyncComponent } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import axios from 'axios';

// Configure Axios with minimal overhead
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}

// Lazy load AppLayout to reduce initial bundle
const AppLayout = defineAsyncComponent({
    loader: () => import('@/layouts/AppLayout.vue'),
    loadingComponent: () => h('div', { class: 'min-h-screen bg-gray-50 flex items-center justify-center' }, [
        h('div', { class: 'animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600' })
    ]),
    delay: 200, // Only show loading after 200ms
    timeout: 10000, // Timeout after 10 seconds
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        // Simple component resolution without dynamic options
        const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue');
        return resolvePageComponent(`./pages/${name}.vue`, pages).catch((error) => {
            console.error(`Failed to load component: ${name}`, error);
            // Fallback component
            return import('./components/ErrorFallback.vue').catch(() => ({
                template: '<div class="p-8 text-center"><h1 class="text-xl font-semibold text-red-600">Component not found</h1><p class="text-gray-600 mt-2">The requested page could not be loaded.</p></div>'
            }));
        });
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('AppLayout', AppLayout);
            
        // Global error handling
        app.config.errorHandler = (err, instance, info) => {
            console.error('Vue error:', err, info);
        };
        
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
        includeCSS: true,
        showSpinner: true,
    },
});

// Initialize theme after app setup
initializeTheme();
