<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('self_check') }} - {{ config('app.name', 'AI-MED Korea') }}</title>
    
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
            background: #f8fafc;
        }
        
        .mobile-container {
            min-height: 100vh;
            padding-bottom: 64px;
        }
        
        .survey-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .survey-card:active {
            transform: scale(0.98);
        }
        
        .category-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            stroke-linecap: round;
        }
        
        .floating-action-btn {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 30;
        }
        
        .floating-action-btn:active {
            transform: scale(0.95);
        }
        
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .badge-new {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="mobile-container">
        {{-- Mobile App Header --}}
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-100">
            <div class="px-4 py-3">
                <div class="flex items-center justify-between mb-3">
                    <a href="/" class="p-2 -ml-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-lg font-bold text-gray-900">{{ __('self_check') }}</h1>
                    <button class="p-2 -mr-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                </div>
                
                {{-- Category Tabs --}}
                <div class="flex space-x-2 overflow-x-auto pb-2 -mx-4 px-4">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-full text-sm font-medium whitespace-nowrap">
                        {{ __('all') }}
                    </button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap">
                        {{ __('mental_health') }}
                    </button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap">
                        {{ __('physical_health') }}
                    </button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium whitespace-nowrap">
                        {{ __('lifestyle') }}
                    </button>
                </div>
            </div>
        </header>
        
        {{-- Welcome Section --}}
        <div class="px-4 py-6">
            <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-5 text-white">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h2 class="text-xl font-bold mb-2">{{ __('check_your_health') }}</h2>
                        <p class="text-sm opacity-90 mb-4">{{ __('ai_powered_assessment') }}</p>
                        
                        @auth
                            <div class="flex items-center space-x-3">
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="w-10 h-10 rounded-full border-2 border-white/30">
                                <div>
                                    <p class="text-xs opacity-75">{{ __('welcome_back') }}</p>
                                    <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                                </div>
                            </div>
                        @else
                            <a href="/login" class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-sm font-medium">
                                {{ __('login_to_track') }}
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endauth
                    </div>
                    <div class="ml-4">
                        <svg class="w-20 h-20 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Quick Stats --}}
        @auth
            <div class="px-4 mb-6">
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ auth()->user()->survey_responses()->count() }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('completed') }}</div>
                    </div>
                    <div class="bg-white rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">85%</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('health_score') }}</div>
                    </div>
                    <div class="bg-white rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $surveys->count() }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('available') }}</div>
                    </div>
                </div>
            </div>
        @endauth
        
        {{-- Survey List --}}
        <div class="px-4 pb-8">
            <h3 class="text-base font-bold text-gray-900 mb-4">{{ __('available_assessments') }}</h3>
            
            <div class="space-y-4">
                @foreach($surveys as $index => $survey)
                    @php
                        $colors = [
                            'bg-gradient-to-br from-blue-500 to-blue-600',
                            'bg-gradient-to-br from-purple-500 to-purple-600',
                            'bg-gradient-to-br from-green-500 to-green-600',
                            'bg-gradient-to-br from-orange-500 to-orange-600',
                            'bg-gradient-to-br from-pink-500 to-pink-600',
                            'bg-gradient-to-br from-teal-500 to-teal-600',
                        ];
                        $bgColor = $colors[$index % count($colors)];
                        
                        $icons = [
                            'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                            'M13 10V3L4 14h7v7l9-11h-7z',
                            'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'
                        ];
                        $icon = $icons[$index % count($icons)];
                        
                        // Check if user has completed this survey
                        $isCompleted = auth()->check() && auth()->user()->survey_responses()->where('survey_id', $survey->id)->exists();
                    @endphp
                    
                    <a href="{{ route('surveys.show', $survey) }}" class="block survey-card bg-white">
                        <div class="{{ $bgColor }} p-4 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                            
                            <div class="relative">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="bg-white/20 backdrop-blur-md rounded-xl p-2">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
                                        </svg>
                                    </div>
                                    @if($isCompleted)
                                        <div class="bg-white/90 px-2 py-1 rounded-full flex items-center">
                                            <svg class="w-4 h-4 text-green-600 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs font-semibold text-green-600">{{ __('completed') }}</span>
                                        </div>
                                    @elseif($index == 0)
                                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold badge-new">
                                            NEW
                                        </span>
                                    @endif
                                </div>
                                
                                <h3 class="text-white font-bold text-lg mb-1">{{ $survey->title }}</h3>
                                <p class="text-white/80 text-sm">
                                    {{ Str::limit($survey->description, 60) }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs">{{ $survey->questions ? count($survey->questions) * 2 : 5 }} {{ __('minutes') }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 8.728a4 4 0 015.656 0M12 12.01V12m-3.536 3.536a4 4 0 005.656 0"></path>
                                        </svg>
                                        <span class="text-xs">{{ $survey->questions ? count($survey->questions) : 10 }} {{ __('questions') }}</span>
                                    </div>
                                </div>
                                
                                @if($survey->has_detailed_version)
                                    <span class="text-xs text-blue-600 font-semibold">
                                        {{ __('detailed_available') }}
                                    </span>
                                @endif
                            </div>
                            
                            @if($isCompleted)
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">{{ __('last_taken') }}: {{ auth()->user()->survey_responses()->where('survey_id', $survey->id)->latest()->first()->created_at->diffForHumans() }}</span>
                                        <span class="text-xs font-semibold text-blue-600">{{ __('retake') }} →</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
            
            @if($surveys->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('no_surveys_available') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('check_back_later') }}</p>
                </div>
            @endif
        </div>
        
        {{-- Floating Action Button --}}
        @auth
            <a href="/recovery-dashboard" class="floating-action-btn">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </a>
        @endauth
    </div>
    
    {{-- Mobile Navigation --}}
    <x-mobile-navigation />
</body>
</html>