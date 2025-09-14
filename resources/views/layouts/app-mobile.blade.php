<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#667eea">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'AIMED Korea') }}">
    
    <title>{{ config('app.name', 'AIMED Korea') }}</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icons/icon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/icons/icon-180x180.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    
    <!-- Mobile App Specific Styles -->
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        input, textarea {
            -webkit-user-select: text;
            user-select: text;
        }
        
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f4f6;
        }
        
        /* Prevent bounce scrolling on iOS */
        body {
            position: fixed;
            overflow: hidden;
        }
        
        .mobile-app-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Safe area insets for notched devices */
        .mobile-app-container {
            padding-top: env(safe-area-inset-top);
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Loading spinner */
        .app-loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .app-loading.hidden {
            display: none;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e5e7eb;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            padding-bottom: calc(8px + env(safe-area-inset-bottom));
            z-index: 100;
        }
        
        .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 0;
            text-decoration: none;
            color: #9ca3af;
            transition: color 0.2s;
        }
        
        .nav-item.active {
            color: #667eea;
        }
        
        .nav-item svg {
            width: 24px;
            height: 24px;
            margin-bottom: 4px;
        }
        
        .nav-item span {
            font-size: 11px;
            font-weight: 500;
        }
        
        /* Pull to refresh */
        .pull-to-refresh {
            position: absolute;
            top: -60px;
            left: 0;
            right: 0;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s;
        }
        
        .pull-to-refresh.active {
            transform: translateY(60px);
        }
        
        /* Toast notifications */
        .toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 12px 24px;
            border-radius: 24px;
            font-size: 14px;
            z-index: 9998;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .toast.show {
            opacity: 1;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Loading Screen -->
    <div class="app-loading" id="appLoading">
        <div class="spinner"></div>
    </div>
    
    <!-- Main App Container -->
    <div class="mobile-app-container" id="appContainer">
        <!-- Pull to Refresh -->
        <div class="pull-to-refresh" id="pullToRefresh">
            <div class="spinner"></div>
        </div>
        
        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
        
        <!-- Bottom Navigation -->
        @if(!isset($hideBottomNav) || !$hideBottomNav)
        <nav class="bottom-nav">
            <a href="/" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>홈</span>
            </a>
            
            <a href="{{ route('surveys.index') }}" class="nav-item {{ request()->routeIs('surveys.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>설문</span>
            </a>
            
            @auth
            <a href="{{ route('recovery.dashboard') }}" class="nav-item {{ request()->routeIs('recovery.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>대시보드</span>
            </a>
            @endauth
            
            <a href="/posts/blog" class="nav-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <span>콘텐츠</span>
            </a>
            
            @auth
            <a href="{{ route('profile.show') }}" class="nav-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>프로필</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="nav-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span>로그인</span>
            </a>
            @endauth
        </nav>
        @endif
    </div>
    
    <!-- Toast Container -->
    <div id="toast" class="toast"></div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    
    <script>
        // Hide loading screen when page is loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('appLoading').classList.add('hidden');
            }, 300);
        });
        
        // Pull to refresh functionality
        let pullToRefreshStartY = 0;
        let isPulling = false;
        
        const appContainer = document.getElementById('appContainer');
        const pullToRefresh = document.getElementById('pullToRefresh');
        
        appContainer.addEventListener('touchstart', function(e) {
            if (appContainer.scrollTop === 0) {
                pullToRefreshStartY = e.touches[0].clientY;
                isPulling = true;
            }
        });
        
        appContainer.addEventListener('touchmove', function(e) {
            if (!isPulling) return;
            
            const currentY = e.touches[0].clientY;
            const pullDistance = currentY - pullToRefreshStartY;
            
            if (pullDistance > 0 && pullDistance < 100) {
                pullToRefresh.style.transform = `translateY(${pullDistance}px)`;
            }
        });
        
        appContainer.addEventListener('touchend', function(e) {
            if (!isPulling) return;
            
            const pullDistance = parseInt(pullToRefresh.style.transform.replace('translateY(', '').replace('px)', '') || 0);
            
            if (pullDistance > 60) {
                pullToRefresh.classList.add('active');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                pullToRefresh.style.transform = 'translateY(0)';
            }
            
            isPulling = false;
        });
        
        // Toast notification function
        function showToast(message, duration = 3000) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            
            setTimeout(function() {
                toast.classList.remove('show');
            }, duration);
        }
        
        // PWA install prompt
        let deferredPrompt;
        
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });
        
        // Handle back button
        if (window.history && window.history.length > 1) {
            document.addEventListener('DOMContentLoaded', function() {
                const backButtons = document.querySelectorAll('.back-button');
                backButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        if (window.history.length > 1) {
                            e.preventDefault();
                            window.history.back();
                        }
                    });
                });
            });
        }
        
        // Prevent zoom on input focus (iOS)
        document.addEventListener('gesturestart', function(e) {
            e.preventDefault();
        });
        
        // Add haptic feedback for buttons (if supported)
        if ('vibrate' in navigator) {
            document.addEventListener('click', function(e) {
                if (e.target.matches('button, a, .btn-primary, .btn-secondary, .action-card')) {
                    navigator.vibrate(10);
                }
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>