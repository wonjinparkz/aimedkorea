<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Critical CSS 인라인 처리 --}}
        <style>
            /* Critical CSS for above-the-fold content */
            body { margin: 0; font-family: system-ui, -apple-system, sans-serif; }
            .min-h-screen { min-height: 100vh; }
            .relative { position: relative; }
            .max-w-7xl { max-width: 80rem; }
            .mx-auto { margin-left: auto; margin-right: auto; }
            .px-4 { padding-left: 1rem; padding-right: 1rem; }
            .py-12 { padding-top: 3rem; padding-bottom: 3rem; }
            .text-center { text-align: center; }
            .font-bold { font-weight: 700; }
            .text-5xl { font-size: 3rem; line-height: 1; }
            .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
            .text-gray-900 { color: #111827; }
            .text-gray-600 { color: #4b5563; }
            .mb-4 { margin-bottom: 1rem; }
            .mb-8 { margin-bottom: 2rem; }
            .mb-12 { margin-bottom: 3rem; }
            .mt-2 { margin-top: 0.5rem; }
            .grid { display: grid; }
            .gap-6 { gap: 1.5rem; }
            [x-cloak] { display: none !important; }
            
            /* Preload animation */
            .skeleton { 
                background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
                background-size: 200% 100%;
                animation: loading 1.5s ease-in-out infinite;
            }
            @keyframes loading {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }
        </style>

        {{-- Preconnect to external domains --}}
        <link rel="dns-prefetch" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

        {{-- Preload critical resources --}}
        <link rel="preload" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" as="style">
        
        {{-- PWA Meta Tags --}}
        <meta name="theme-color" content="#1e40af">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="AimedKorea">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="application-name" content="AimedKorea">

        {{-- Web App Manifest --}}
        <link rel="manifest" href="/manifest.json">

        {{-- Icons - Only essential ones --}}
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/icons/apple-touch-icon.png">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts with font-display: swap -->
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        
        {{-- Defer additional icons and splash screens --}}
        <script>
            // Load non-critical resources after page load
            window.addEventListener('load', function() {
                // Load additional icons
                const icons = [
                    {rel: 'icon', type: 'image/png', sizes: '32x32', href: '/favicon-32x32.png'},
                    {rel: 'icon', type: 'image/png', sizes: '16x16', href: '/favicon-16x16.png'},
                    {rel: 'mask-icon', href: '/images/icons/safari-pinned-tab.svg', color: '#1e40af'}
                ];
                
                icons.forEach(icon => {
                    const link = document.createElement('link');
                    Object.keys(icon).forEach(key => {
                        link[key] = icon[key];
                    });
                    document.head.appendChild(link);
                });

                // Load PWA meta tags
                const metas = [
                    {name: 'msapplication-TileColor', content: '#1e40af'},
                    {name: 'msapplication-TileImage', content: '/images/icons/icon-144x144.png'},
                    {name: 'msapplication-config', content: '/browserconfig.xml'}
                ];
                
                metas.forEach(meta => {
                    const metaTag = document.createElement('meta');
                    Object.keys(meta).forEach(key => {
                        metaTag[key] = meta[key];
                    });
                    document.head.appendChild(metaTag);
                });
            });
        </script>
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        {{-- Defer Livewire Scripts --}}
        <script>
            // Load Livewire scripts after initial render
            if (window.requestIdleCallback) {
                requestIdleCallback(() => {
                    @livewireScripts
                });
            } else {
                setTimeout(() => {
                    @livewireScripts
                }, 1);
            }
        </script>

        {{-- PWA Service Worker - Defer registration --}}
        @include('pwa.register')

        {{-- Connection status handler --}}
        <script>
            // Defer connection status check
            window.addEventListener('load', function() {
                function updateOnlineStatus() {
                    if (!navigator.onLine) {
                        document.body.classList.add('offline');
                    } else {
                        document.body.classList.remove('offline');
                    }
                }

                window.addEventListener('online', updateOnlineStatus);
                window.addEventListener('offline', updateOnlineStatus);
                updateOnlineStatus();
            });
        </script>

        @stack('scripts')
    </body>
</html>