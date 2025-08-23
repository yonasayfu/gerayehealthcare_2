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
        },
    },
    build: {
        target: 'esnext',
        minify: 'esbuild',
        rollupOptions: {
            output: {
                manualChunks(id) {
                    // Split core vendor libs
                    if (id.includes('node_modules')) {
                        if (id.includes('vue') || id.includes('@inertiajs') || id.includes('axios') || id.includes('ziggy-js')) {
                            return 'vendor';
                        }
                        // Put charting libs in their own chunk
                        if (id.includes('chart.js') || id.includes('vue-chartjs')) {
                            return 'charts';
                        }
                        // Group lucide icons separately (tree-shakeable, but this isolates if many icons are used)
                        if (id.includes('lucide-vue-next')) {
                            return 'icons';
                        }
                    }
                    // Leave others to default chunking
                },
            },
        },
    },
});