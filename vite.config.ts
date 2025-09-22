import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';
import { visualizer } from 'rollup-plugin-visualizer';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        ...(process.env.ANALYZE
            ? [
                  visualizer({
                      filename: 'stats.html',
                      open: false,
                      gzipSize: true,
                      brotliSize: true,
                      template: 'treemap',
                  }),
              ]
            : []),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            '@src': path.resolve(__dirname, './resources/js/features/messaging'),
        },
    },
    server: {
        proxy: {
            '/api': 'http://127.0.0.1:8000',
            '/sanctum': 'http://127.0.0.1:8000',
            '/broadcasting': 'http://127.0.0.1:8000',
        },
    },
    build: {
        target: 'esnext',
        minify: 'terser', // Use terser for better compression
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        rollupOptions: {
            output: {
                // Simple but effective chunking strategy
                manualChunks(id) {
                    // Vue core
                    if (id.includes('vue') && !id.includes('vue-chartjs')) {
                        return 'vue-core';
                    }
                    // Inertia
                    if (id.includes('@inertiajs')) {
                        return 'inertia';
                    }
                    // Charts
                    if (id.includes('chart.js') || id.includes('vue-chartjs')) {
                        return 'charts';
                    }
                    // Calendar
                    if (id.includes('@fullcalendar')) {
                        return 'calendar';
                    }
                    // Icons
                    if (id.includes('lucide-vue-next')) {
                        return 'icons';
                    }
                    // UI Framework
                    if (id.includes('reka-ui') || id.includes('@vueuse')) {
                        return 'ui-framework';
                    }
                    // Utils
                    if (id.includes('axios') || id.includes('lodash') || id.includes('date-fns')) {
                        return 'utils';
                    }
                    // Ziggy routes
                    if (id.includes('ziggy-js')) {
                        return 'ziggy';
                    }
                    // Everything else in misc
                    if (id.includes('node_modules')) {
                        return 'misc';
                    }
                },
            },
        },
        chunkSizeWarningLimit: 1000, // Increase warning limit for large chunks
    },
});
