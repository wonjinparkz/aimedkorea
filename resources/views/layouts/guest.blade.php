<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Google Analytics 4 --}}
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-2YV3S6V60E"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-2YV3S6V60E', {
                'cookie_domain': 'auto',
                'cookie_flags': 'SameSite=None;Secure',
                'cookie_prefix': 'AIMED_'
            });
        </script>

                <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-N8GJF2QW');</script>
        <!-- End Google Tag Manager -->
        
        {{-- GTM DataLayer 초기화 (GTM 로드 전에 실행) --}}
        <script>
            window.dataLayer = window.dataLayer || [];
            // GTM Preview Helper
            (function() {
                var gtmPreviewCookie = document.cookie.match(/gtm_preview=([^;]+)/);
                var gtmDebugCookie = document.cookie.match(/gtm_debug=([^;]+)/);
                if (gtmPreviewCookie || gtmDebugCookie || window.location.search.includes('gtm_')) {
                    console.log('GTM Preview/Debug Mode Active');
                    window.dataLayer.push({
                        'event': 'gtm.dom',
                        'gtm.element': document,
                        'gtm.elementClasses': '',
                        'gtm.elementId': '',
                        'gtm.elementTarget': '',
                        'gtm.elementUrl': window.location.href
                    });
                }
            })();
        </script>

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N8GJF2QW" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        
        <div class="font-sans text-gray-900 antialiased min-h-screen flex flex-col">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
