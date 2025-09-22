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
        <!-- AlpineJS CDN removed; use Vue components or install Alpine locally if needed -->
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

        <!-- Local fonts (Instrument Sans) are loaded via @font-face in app.css -->
        <!-- Font Awesome now bundled locally via Vite (@fortawesome/fontawesome-free) -->
        @routes
        
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"]) {{-- Corrected 'pages' to 'Pages' --}}
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
