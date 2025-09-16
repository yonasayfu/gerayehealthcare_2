import '../css/app.css';
// Use local Font Awesome (no CDN dependency)
import '@fortawesome/fontawesome-free/css/all.min.css';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Read CSRF token from meta for Echo auth requests
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || undefined;

// Feature flag so you can disable realtime in dev/mobile testing
const ENABLE_ECHO = (import.meta.env.VITE_ENABLE_ECHO ?? 'true') === 'true';
if (ENABLE_ECHO) {
    try {
        const scheme = (import.meta.env.VITE_REVERB_SCHEME ?? 'http').toString();
        const host = (import.meta.env.VITE_REVERB_HOST ?? window.location.hostname).toString();
        const port = Number(import.meta.env.VITE_REVERB_PORT ?? 6001);
        const useTLS = scheme === 'https';

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: host,
            wsPort: port,
            wssPort: port,
            forceTLS: useTLS,
            enabledTransports: useTLS ? ['ws', 'wss'] : ['ws'],
            authEndpoint: '/broadcasting/auth',
            withCredentials: true,
            auth: {
                headers: {
                    'X-CSRF-TOKEN': csrfToken ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            },
        });
    } catch (e) {
        console.warn('Echo initialization failed (disabled for this session):', e);
    }
} else {
    console.info('Realtime (Echo) disabled via VITE_ENABLE_ECHO=false');
}

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h, defineAsyncComponent } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import axios from 'axios';

axios.defaults.withCredentials = true; // Crucial for sending cookies with requests

// Configure Axios with minimal overhead
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}

// Fetch CSRF cookie for Sanctum SPA authentication, then initialize the app
axios.get('/sanctum/csrf-cookie').then(() => {
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
                // Ensure Ziggy uses the server-provided route list (@routes)
                 
                .use(ZiggyVue, (window as any).Ziggy)
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
}).catch(error => {
    console.error('Failed to fetch CSRF cookie:', error);
    // Optionally, render a fallback UI here if the app cannot start
    const root = document.getElementById('app');
    if (root) {
        root.innerHTML = `
            <div style="font-family: sans-serif; padding: 2rem; text-align: center; background-color: #fef2f2; color: #991b1b;">
                <h1 style="font-size: 1.5rem; font-weight: bold;">Application failed to start</h1>
                <p style="margin-top: 0.5rem;">Could not connect to the server to initialize a secure session. Please check your network connection and try again.</p>
                <p style="margin-top: 1rem; font-size: 0.875rem; color: #7f1d1d;">Error: ${error.message}</p>
            </div>
        `;
    }
});

// Initialize theme after app setup
initializeTheme();

// Register a simple service worker to cache built assets for offline reloads
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker
            .register('/sw.js')
            .catch((err) => console.warn('Service worker registration failed:', err));
    });
}
