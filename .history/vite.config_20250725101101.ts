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
            'reka-ui/Label': path.resolve(__dirname, './node_modules/reka-ui/dist/Label.js'),
            'reka-ui/Input': path.resolve(__dirname, './node_modules/reka-ui/dist/Input.js'),
            'reka-ui/Textarea': path.resolve(__dirname, './node_modules/reka-ui/dist/Textarea.js'),
            'reka-ui/Select': path.resolve(__dirname, './node_modules/reka-ui/dist/Select.js'),
            'reka-ui/Calendar': path.resolve(__dirname, './node_modules/reka-ui/dist/Calendar.js'),
            'reka-ui/Popover': path.resolve(__dirname, './node_modules/reka-ui/dist/Popover.js'),
            'reka-ui/Button': path.resolve(__dirname, './node_modules/reka-ui/dist/Button.js'),
            'reka-ui/Sheet': path.resolve(__dirname, './node_modules/reka-ui/dist/Sheet.js'),
            'reka-ui/Avatar': path.resolve(__dirname, './node_modules/reka-ui/dist/Avatar.js'),
            'reka-ui/Collapsible': path.resolve(__dirname, './node_modules/reka-ui/dist/Collapsible.js'),
            'reka-ui/DropdownMenu': path.resolve(__dirname, './node_modules/reka-ui/dist/DropdownMenu.js'),
            'reka-ui/Checkbox': path.resolve(__dirname, './node_modules/reka-ui/dist/Checkbox.js'),
            'reka-ui/Dialog': path.resolve(__dirname, './node_modules/reka-ui/dist/Dialog.js'),
            'reka-ui/Separator': path.resolve(__dirname, './node_modules/reka-ui/dist/Separator.js'),
            'reka-ui/Tooltip': path.resolve(__dirname, './node_modules/reka-ui/dist/Tooltip.js'),
            'reka-ui/NavigationMenu': path.resolve(__dirname, './node_modules/reka-ui/dist/NavigationMenu.js'),
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
        noExternal: ['reka-ui'],
    },
});
