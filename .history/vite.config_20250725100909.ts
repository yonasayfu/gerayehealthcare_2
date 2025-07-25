import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

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
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'reka-ui/Label': path.resolve(__dirname, './node_modules/reka-ui/dist/Label/index.js'),
            'reka-ui/Input': path.resolve(__dirname, './node_modules/reka-ui/dist/Input/index.js'),
            'reka-ui/Textarea': path.resolve(__dirname, './node_modules/reka-ui/dist/Textarea/index.js'),
            'reka-ui/Select': path.resolve(__dirname, './node_modules/reka-ui/dist/Select/index.js'),
            'reka-ui/Calendar': path.resolve(__dirname, './node_modules/reka-ui/dist/Calendar/index.js'),
            'reka-ui/Popover': path.resolve(__dirname, './node_modules/reka-ui/dist/Popover/index.js'),
            'reka-ui/Button': path.resolve(__dirname, './node_modules/reka-ui/dist/Button/index.js'),
            'reka-ui/Sheet': path.resolve(__dirname, './node_modules/reka-ui/dist/Sheet/index.js'),
            'reka-ui/Avatar': path.resolve(__dirname, './node_modules/reka-ui/dist/Avatar/index.js'),
            'reka-ui/Collapsible': path.resolve(__dirname, './node_modules/reka-ui/dist/Collapsible/index.js'),
            'reka-ui/DropdownMenu': path.resolve(__dirname, './node_modules/reka-ui/dist/DropdownMenu/index.js'),
            'reka-ui/Checkbox': path.resolve(__dirname, './node_modules/reka-ui/dist/Checkbox/index.js'),
            'reka-ui/Dialog': path.resolve(__dirname, './node_modules/reka-ui/dist/Dialog/index.js'),
            'reka-ui/Separator': path.resolve(__dirname, './node_modules/reka-ui/dist/Separator/index.js'),
            'reka-ui/Tooltip': path.resolve(__dirname, './node_modules/reka-ui/dist/Tooltip/index.js'),
            'reka-ui/NavigationMenu': path.resolve(__dirname, './node_modules/reka-ui/dist/NavigationMenu/index.js'),
        },
    },
    optimizeDeps: {
        include: ['reka-ui'],
    },
    build: {
        commonjsOptions: {
            include: [/node_modules/],
        },
    },
    ssr: {
