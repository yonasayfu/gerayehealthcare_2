import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
            '~': resolve(__dirname, 'resources'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // Vendor chunk - separate large libraries
                    vendor: ['vue', '@inertiajs/vue3'],
                    
                    // UI Components chunk
                    ui: [
                        'primevue/config',
                        'primevue/button',
                        'primevue/inputtext',
                        'primevue/datatable',
                        'primevue/dialog',
                        'primevue/toast',
                        'primevue/dropdown',
                        'primevue/calendar',
                        'primevue/fileupload',
                        'primevue/chart',
                    ],
                    
                    // Admin modules chunk
                    admin: [
                        './resources/js/Pages/Admin/Patients',
                        './resources/js/Pages/Admin/Staff',
                        './resources/js/Pages/Admin/Invoices',
                        './resources/js/Pages/Admin/InventoryItems',
                    ],
                    
                    // Marketing modules chunk
                    marketing: [
                        './resources/js/Pages/Admin/MarketingAnalytics',
                        './resources/js/Pages/Admin/MarketingCampaigns',
                        './resources/js/Pages/Admin/MarketingLeads',
                        './resources/js/Pages/Admin/Events',
                    ],
                    
                    // Charts and analytics chunk
                    analytics: [
                        'chart.js',
                        'vue-chartjs',
                        './resources/js/Components/Charts',
                    ],
                },
                // Optimize chunk size
                chunkFileNames: (chunkInfo) => {
                    const facadeModuleId = chunkInfo.facadeModuleId ? chunkInfo.facadeModuleId.split('/').pop() : 'chunk';
                    return `js/${facadeModuleId}-[hash].js`;
                },
            },
        },
        // Optimize chunk sizes
        chunkSizeWarningLimit: 1000,
        
        // Enable source maps for development
        sourcemap: process.env.NODE_ENV === 'development',
        
        // Minification settings
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: process.env.NODE_ENV === 'production',
                drop_debugger: process.env.NODE_ENV === 'production',
            },
        },
    },
    
    // Development server optimization
    server: {
        hmr: {
            overlay: false,
        },
        // Warm up commonly used files
        warmup: {
            clientFiles: [
                './resources/js/Components/Layouts/AppLayout.vue',
                './resources/js/Components/UI/*.vue',
                './resources/js/Pages/Admin/Dashboard.vue',
            ],
        },
    },
    
    // Optimization for dependencies
    optimizeDeps: {
        include: [
            'vue',
            '@inertiajs/vue3',
            'primevue/config',
            'primevue/api',
            'chart.js',
            'vue-chartjs',
        ],
        exclude: ['vue-demi'],
    },
    
    // Asset optimization
    assetsInclude: ['**/*.xlsx', '**/*.csv'],
    
    // Build performance
    esbuild: {
        // Remove console logs in production
        drop: process.env.NODE_ENV === 'production' ? ['console', 'debugger'] : [],
    },
});