<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class([
    'dark' => ($appearance ?? 'system') == 'dark',
    'theme-dark' => ($appearance ?? 'system') == 'dark',
    'theme-light' => ($appearance ?? 'system') == 'light',
])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                let appearance = '{{ $appearance ?? "system" }}';
                if (appearance === 'theme-dark') appearance = 'dark';
                if (appearance === 'theme-light') appearance = 'light';

                const root = document.documentElement;
                const removeThemeClasses = () => {
                    root.classList.remove('theme-light', 'theme-dark');
                };

                if (appearance === 'dark') {
                    removeThemeClasses();
                    root.classList.add('dark', 'theme-dark');
                } else if (appearance === 'light') {
                    removeThemeClasses();
                    root.classList.remove('dark');
                    root.classList.add('theme-light');
                } else {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    removeThemeClasses();
                    if (prefersDark) {
                        root.classList.add('dark', 'theme-dark');
                    } else {
                        root.classList.remove('dark');
                        root.classList.add('theme-light');
                    }
                }
            })();
        </script>
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Geraye') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Optimize Bunny Fonts loading -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <!-- Preload the stylesheet for faster first paint, then apply -->
        <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        </noscript>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        @routes
        
        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
