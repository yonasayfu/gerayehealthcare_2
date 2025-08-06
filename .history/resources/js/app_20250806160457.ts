import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import Alpine from 'alpinejs';
import axios from 'axios'; // Import axios

window.Alpine = Alpine;
Alpine.start();

// Configure Axios to send CSRF token
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content;
axios.defaults.withCredentials = true; // Ensure cookies are sent with requests

import AppLayout from '@/layouts/AppLayout.vue';
import PrintableReport from '@/components/PrintableReport.vue';
import EthiopianDatePicker from '@/components/EthiopianDatePicker.vue'; // Re-add global registration

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('AppLayout', AppLayout)
            .component('PrintableReport', PrintableReport)
            .component('EthiopianDatePicker', EthiopianDatePicker) // Re-register EthiopianDatePicker
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
