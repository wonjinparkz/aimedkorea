<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AI-MED Korea') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Mobile App Styles -->
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        body {
            overscroll-behavior: contain;
            -webkit-font-smoothing: antialiased;
        }
        
        .mobile-container {
            min-height: 100vh;
            padding-bottom: 64px;
            background: linear-gradient(180deg, #f0f9ff 0%, #ffffff 100%);
        }
        
        .hero-mobile {
            position: relative;
            overflow: hidden;
            border-radius: 0 0 24px 24px;
        }
        
        .quick-menu-item {
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }
        
        .quick-menu-item:active {
            transform: scale(0.95);
        }
        
        .card-mobile {
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .card-mobile:active {
            transform: scale(0.98);
        }
        
        .scroll-snap-x {
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        
        .scroll-snap-item {
            scroll-snap-align: start;
        }
        
        .tab-indicator {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .pulse-dot {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="mobile-container">
        {{-- Mobile App Header --}}
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center space-x-3">
                    <img src="/images/logo.png" alt="AI-MED" class="h-8 w-auto">
                    <div>
                        <h1 class="text-sm font-bold text-gray-900">AI-MED Korea</h1>
                        <p class="text-xs text-gray-500">{{ __('ai_recovery_system') }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    {{-- Notification Icon --}}
                    <button class="relative p-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full pulse-dot"></span>
                    </button>
                    
                    {{-- Profile Avatar --}}
                    @auth
                        <a href="/user/profile" class="block">
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full border-2 border-blue-500">
                        </a>
                    @else
                        <a href="/login" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-full">
                            {{ __('login') }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>
        
        {{-- Hero Section Mobile --}}
        @if($heroes->count() > 0)
            <div class="hero-mobile relative h-56 bg-gradient-to-br from-blue-500 to-purple-600">
                <div class="swiper-container h-full">
                    <div class="swiper-wrapper">
                        @foreach($heroes as $hero)
                            <div class="swiper-slide relative">
                                @if($hero->background_image)
                                    <img src="{{ Storage::url($hero->background_image) }}" 
                                         alt="{{ $hero->title }}"
                                         class="w-full h-full object-cover">
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent">
                                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                        <h2 class="text-lg font-bold mb-1">
                                            {{ $hero->title }}
                                        </h2>
                                        <p class="text-sm opacity-90 line-clamp-2">
                                            {{ $hero->subtitle }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        @endif
        
        {{-- Quick Menu Grid --}}
        <div class="px-4 -mt-8 relative z-10">
            <div class="grid grid-cols-4 gap-3">
                <a href="/surveys" class="quick-menu-item rounded-2xl p-3 text-center">
                    <div class="w-12 h-12 mx-auto mb-2 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">{{ __('self_check') }}</span>
                </a>
                
                <a href="/recovery-dashboard" class="quick-menu-item rounded-2xl p-3 text-center">
                    <div class="w-12 h-12 mx-auto mb-2 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">{{ __('dashboard') }}</span>
                </a>
                
                <a href="/routines" class="quick-menu-item rounded-2xl p-3 text-center">
                    <div class="w-12 h-12 mx-auto mb-2 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">{{ __('routines') }}</span>
                </a>
                
                <a href="/news" class="quick-menu-item rounded-2xl p-3 text-center">
                    <div class="w-12 h-12 mx-auto mb-2 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">{{ __('news') }}</span>
                </a>
            </div>
        </div>
        
        {{-- Featured Content --}}
        @if($featuredPost)
            <div class="px-4 mt-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-gray-900">{{ __('featured') }}</h3>
                    <a href="/featured" class="text-xs text-blue-600 font-medium">{{ __('view_all') }}</a>
                </div>
                
                <a href="{{ route('posts.show', ['type' => $featuredPost->type, 'post' => $featuredPost]) }}" class="block card-mobile bg-white p-4">
                    <div class="flex space-x-3">
                        @if($featuredPost->image)
                            <img src="{{ Storage::url($featuredPost->image) }}" 
                                 alt="{{ $featuredPost->title }}"
                                 class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-gray-900 line-clamp-2">
                                {{ $featuredPost->title }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                {{ Str::limit($featuredPost->summary, 60) }}
                            </p>
                            <div class="flex items-center mt-2 text-xs text-gray-400">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $featuredPost->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif
        
        {{-- Routines Section --}}
        @if($routinePosts->count() > 0)
            <div class="mt-6">
                <div class="px-4 flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-gray-900">{{ __('daily_routines') }}</h3>
                    <a href="/routines" class="text-xs text-blue-600 font-medium">{{ __('view_all') }}</a>
                </div>
                
                <div class="overflow-x-auto px-4 pb-2 scroll-snap-x">
                    <div class="flex space-x-3">
                        @foreach($routinePosts as $post)
                            <a href="{{ route('posts.show', ['type' => $post->type, 'post' => $post]) }}" class="scroll-snap-item flex-shrink-0 w-64 card-mobile bg-white p-3">
                                @if($post->image)
                                    <img src="{{ Storage::url($post->image) }}" 
                                         alt="{{ $post->title }}"
                                         class="w-full h-32 rounded-xl object-cover mb-3">
                                @endif
                                <h4 class="text-sm font-semibold text-gray-900 line-clamp-1">
                                    {{ $post->title }}
                                </h4>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                    {{ Str::limit($post->summary, 50) }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        {{-- Research Areas Mobile --}}
        @if($tabPosts->count() > 0)
            <div class="mt-6 px-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-gray-900">{{ __('research_areas') }}</h3>
                </div>
                
                @php
                    $colors = [
                        'bg-blue-500', 'bg-purple-500', 'bg-pink-500', 
                        'bg-red-500', 'bg-orange-500', 'bg-teal-500', 'bg-rose-500'
                    ];
                    $icons = [
                        'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                        'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z',
                        'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                        'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                        'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
                    ];
                @endphp
                
                <div class="grid grid-cols-2 gap-3">
                    @foreach($tabPosts->take(6) as $index => $post)
                        <a href="{{ route('posts.show', ['type' => $post->type, 'post' => $post]) }}" class="card-mobile {{ $colors[$index % count($colors)] }} p-4 text-white">
                            <svg class="w-8 h-8 mb-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$index % count($icons)] }}"></path>
                            </svg>
                            <h4 class="text-sm font-bold line-clamp-2">
                                {{ $post->title }}
                            </h4>
                        </a>
                    @endforeach
                </div>
                
                @if($tabPosts->count() > 6)
                    <a href="/research" class="mt-3 block text-center py-3 bg-gray-100 rounded-xl text-sm font-medium text-gray-700">
                        {{ __('view_more_research') }}
                    </a>
                @endif
            </div>
        @endif
        
        {{-- Latest Updates --}}
        @if($blogPosts->count() > 0)
            <div class="mt-6 mb-8">
                <div class="px-4 flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-gray-900">{{ __('latest_updates') }}</h3>
                    <a href="/blog" class="text-xs text-blue-600 font-medium">{{ __('view_all') }}</a>
                </div>
                
                <div class="px-4 space-y-3">
                    @foreach($blogPosts->take(3) as $post)
                        <a href="{{ route('posts.show', ['type' => $post->type, 'post' => $post]) }}" class="block card-mobile bg-white p-3">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 line-clamp-1">
                                        {{ $post->title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $post->created_at->format('M d, Y') }} · 3 min read
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    
    {{-- Mobile Navigation --}}
    <x-mobile-navigation />
    
    {{-- Swiper JS for Hero Slider --}}
    @if($heroes->count() > 0)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Swiper('.swiper-container', {
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    }
                });
            });
        </script>
    @endif
</body>
</html>