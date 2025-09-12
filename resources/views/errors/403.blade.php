<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - 접근 권한이 없습니다</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
    
    <script>
        // 다크모드 감지
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            {{-- 로고 또는 아이콘 --}}
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-900/20">
                <svg class="h-10 w-10 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            
            {{-- 에러 내용 --}}
            <div class="mt-6 text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">403</h1>
                <h2 class="mt-2 text-xl font-semibold text-gray-700 dark:text-gray-300">접근 권한이 없습니다</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    이 페이지에 접근할 권한이 없습니다.<br>
                    관리자에게 권한 요청을 문의해 주세요.
                </p>
            </div>
            
            {{-- 액션 버튼들 --}}
            <div class="mt-8 space-y-3">
                <div class="flex justify-center space-x-4">
                    <button 
                        onclick="history.back()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        이전 페이지로
                    </button>
                    
                    <a 
                        href="{{ route('filament.admin.pages.dashboard') }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        대시보드로
                    </a>
                </div>
                
                @auth
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            현재 로그인: {{ auth()->user()->name }}
                            @if(auth()->user()->getHighestRole())
                                ({{ auth()->user()->getHighestRole()->display_name }})
                            @endif
                        </p>
                    </div>
                @endauth
            </div>
            
            {{-- 추가 도움말 --}}
            <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            권한이 필요한 경우
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>
                                이 기능을 사용하려면 관리자에게 권한 요청을 문의하세요.<br>
                                시스템 관리자가 적절한 역할과 권한을 부여해 드릴 것입니다.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            @auth
                @if(auth()->user()->hasRole('admin'))
                    <!-- 관리자를 위한 추가 디버그 정보 -->
                    <div class="mt-8 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            관리자 디버그 정보
                        </h3>
                        <div class="text-xs text-red-700 dark:text-red-300 space-y-1 font-mono">
                            <p><strong>요청 경로:</strong> {{ request()->getPathInfo() }}</p>
                            <p><strong>HTTP 메서드:</strong> {{ request()->getMethod() }}</p>
                            <p><strong>라우트명:</strong> {{ request()->route()?->getName() ?? 'N/A' }}</p>
                            <p><strong>현재 사용자:</strong> {{ auth()->user()->username }} ({{ auth()->user()->email }})</p>
                            <p><strong>사용자 역할:</strong> {{ auth()->user()->roles->pluck('display_name')->join(', ') }}</p>
                            <p><strong>시간:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                            <p><strong>IP 주소:</strong> {{ request()->ip() }}</p>
                            <p class="mt-2 pt-2 border-t border-red-200 dark:border-red-700">
                                <strong>로그 확인:</strong> 
                                <code class="bg-red-100 dark:bg-red-900 px-1 rounded">php artisan logs:security --today</code>
                            </p>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</body>
</html>