<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        {{-- Additional Meta Tags --}}
        @stack('meta')

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-2YV3S6V60E"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-2YV3S6V60E', {
                'cookie_domain': 'ai-med.co.kr',
                'cookie_flags': 'SameSite=None;Secure'
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

        {{-- PWA Meta Tags --}}
        <meta name="theme-color" content="#1e40af">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="AimedKorea">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="application-name" content="AimedKorea">
        <meta name="msapplication-TileColor" content="#1e40af">
        <meta name="msapplication-TileImage" content="/images/icons/icon-144x144.png">
        <meta name="msapplication-config" content="/browserconfig.xml">

        {{-- Web App Manifest --}}
        <link rel="manifest" href="/manifest.json?v=2025-01-12-v2">

        {{-- Critical CSS for faster initial render --}}
        <style>
            /* Critical CSS - inline for immediate rendering */
            *,::after,::before{box-sizing:border-box}
            body{margin:0;font-family:system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;line-height:1.5;color:#212529;background-color:#fff}
            .min-h-screen{min-height:100vh}
            .bg-gray-100{background-color:#f7fafc}
            .max-w-7xl{max-width:80rem}
            .mx-auto{margin-left:auto;margin-right:auto}
            .px-4{padding-left:1rem;padding-right:1rem}
            .py-12{padding-top:3rem;padding-bottom:3rem}
            .text-center{text-align:center}
            .text-5xl{font-size:3rem;line-height:1}
            .text-3xl{font-size:1.875rem;line-height:2.25rem}
            .font-bold{font-weight:700}
            .text-gray-900{color:#1a202c}
            .text-gray-600{color:#718096}
            .mb-4{margin-bottom:1rem}
            .mb-8{margin-bottom:2rem}
            .mb-12{margin-bottom:3rem}
            .grid{display:grid}
            .gap-6{gap:1.5rem}
            @media(min-width:768px){
                .md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}
                .sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}
            }
            @media(min-width:1024px){
                .lg\:grid-cols-3{grid-template-columns:repeat(3,minmax(0,1fr))}
                .lg\:px-8{padding-left:2rem;padding-right:2rem}
                .lg\:text-6xl{font-size:3.75rem;line-height:1}
            }
            /* Hide elements until fully loaded */
            [x-cloak]{display:none!important}
        </style>

        {{-- Icons --}}
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/icons/apple-touch-icon.png">
        <link rel="mask-icon" href="/images/icons/safari-pinned-tab.svg" color="#1e40af">

        {{-- iOS Splash Screens --}}
        <link rel="apple-touch-startup-image" href="/images/splash/splash-640x1136.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="/images/splash/splash-750x1334.png" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="/images/splash/splash-1242x2208.png" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3)">
        <link rel="apple-touch-startup-image" href="/images/splash/splash-1125x2436.png" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)">
        <link rel="apple-touch-startup-image" href="/images/splash/splash-1536x2048.png" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="/images/splash/splash-1668x2224.png" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)">
        <link rel="apple-touch-startup-image" href="/images/splash/splash-2048x2732.png" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)">

        {{-- Defer Tailwind CSS loading --}}
        {{-- Tailwind CDN removed - using Vite compiled CSS instead --}}

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        <style>
            [x-cloak] { display: none !important; }
            
            /* Offline mode styles */
            body.offline {
                position: relative;
            }
            
            body.offline::before {
                content: '오프라인 모드';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background-color: #ef4444;
                color: white;
                text-align: center;
                padding: 0.5rem;
                z-index: 9999;
                font-size: 0.875rem;
            }
            
            /* PWA installed mode */
            @media (display-mode: standalone) {
                /* Custom styles for installed PWA */
            }
        </style>
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N8GJF2QW" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <x-banner />

        <div class="min-h-screen flex flex-col">
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
            <main class="flex-grow">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>

            <!-- Footer -->
            @livewire('footer')
        </div>

        @stack('modals')

        @livewireScripts
        
        {{-- PWA Registration Script --}}
        <script src="{{ asset('js/pwa/register.js') }}?v=2025-01-12-v2" defer></script>
        
        {{-- GTM Menu Click Tracking --}}
        @if(config('app.env') === 'production')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // GTM 메뉴 클릭 추적 초기화
                function initMenuTracking() {
                    // 데이터 속성이 있는 모든 메뉴 항목 선택
                    const menuItems = document.querySelectorAll('[data-gtm-menu-type]');
                    
                    menuItems.forEach(function(item) {
                        item.addEventListener('click', function(e) {
                            // GTM dataLayer가 존재하는지 확인
                            if (typeof window.dataLayer === 'undefined') {
                                window.dataLayer = [];
                            }
                            
                            // 클릭된 요소의 데이터 속성 읽기
                            const menuData = {
                                'event': 'menu_click',
                                'menuType': this.getAttribute('data-gtm-menu-type'),
                                'menuCategory': this.getAttribute('data-gtm-menu-category'),
                                'menuLabel': this.getAttribute('data-gtm-menu-label'),
                                'menuPosition': this.getAttribute('data-gtm-menu-position'),
                                'menuParent': this.getAttribute('data-gtm-menu-parent') || null,
                                'menuURL': this.href || this.getAttribute('data-gtm-menu-url'),
                                'pageURL': window.location.href,
                                'pageTitle': document.title,
                                'timestamp': new Date().toISOString(),
                                'userAgent': navigator.userAgent,
                                'screenResolution': window.screen.width + 'x' + window.screen.height,
                                'viewportSize': window.innerWidth + 'x' + window.innerHeight
                            };
                            
                            // dataLayer에 이벤트 푸시
                            window.dataLayer.push(menuData);
                            
                            // 디버그 모드 (URL에 ?debug=gtm 포함 시)
                            if (window.location.search.includes('debug=gtm')) {
                                console.log('GTM Menu Click Event:', menuData);
                            }
                        });
                    });
                    
                    // 동적으로 추가되는 메뉴를 위한 이벤트 위임
                    document.addEventListener('click', function(e) {
                        const menuItem = e.target.closest('[data-gtm-menu-type]');
                        
                        if (menuItem && !menuItem.hasAttribute('data-gtm-initialized')) {
                            // 이미 초기화된 항목은 제외
                            menuItem.setAttribute('data-gtm-initialized', 'true');
                            
                            const menuData = {
                                'event': 'menu_click_dynamic',
                                'menuType': menuItem.getAttribute('data-gtm-menu-type'),
                                'menuCategory': menuItem.getAttribute('data-gtm-menu-category'),
                                'menuLabel': menuItem.getAttribute('data-gtm-menu-label'),
                                'menuPosition': menuItem.getAttribute('data-gtm-menu-position'),
                                'menuParent': menuItem.getAttribute('data-gtm-menu-parent') || null,
                                'menuURL': menuItem.href || menuItem.getAttribute('data-gtm-menu-url'),
                                'isDynamic': true
                            };
                            
                            window.dataLayer = window.dataLayer || [];
                            window.dataLayer.push(menuData);
                        }
                    });
                }
                
                // 초기화 실행
                initMenuTracking();
                
                // Alpine.js와의 통합을 위한 전역 함수
                window.trackGTMMenuClick = function(menuType, category, label, position, url, parent) {
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({
                        'event': 'menu_click_alpine',
                        'menuType': menuType,
                        'menuCategory': category,
                        'menuLabel': label,
                        'menuPosition': position,
                        'menuParent': parent || null,
                        'menuURL': url,
                        'source': 'alpine'
                    });
                };
            });
        </script>
        @endif
        
        @stack('scripts')
    </body>
</html>
