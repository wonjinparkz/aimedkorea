@php
    // 현재 언어 설정 가져오기
    $currentLang = session('locale', 'kor');
    
    // 언어별 라벨
    $labels = [
        'home' => [
            'kor' => '홈',
            'eng' => 'Home',
            'chn' => '首页',
            'hin' => 'होम',
            'arb' => 'الرئيسية'
        ],
        'diagnosis' => [
            'kor' => '셀프체크',
            'eng' => 'Self-Check',
            'chn' => '自我检查',
            'hin' => 'स्व-जांच',
            'arb' => 'الفحص الذاتي'
        ],
        'dashboard' => [
            'kor' => '대시보드',
            'eng' => 'Dashboard',
            'chn' => '仪表板',
            'hin' => 'डैशबोर्ड',
            'arb' => 'لوحة القيادة'
        ],
        'mypage' => [
            'kor' => '마이페이지',
            'eng' => 'My Page',
            'chn' => '我的页面',
            'hin' => 'मेरा पेज',
            'arb' => 'صفحتي'
        ],
        'login' => [
            'kor' => '로그인',
            'eng' => 'Login',
            'chn' => '登录',
            'hin' => 'लॉगिन',
            'arb' => 'تسجيل الدخول'
        ]
    ];
@endphp

{{-- Mobile Bottom Navigation Bar --}}
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 md:hidden">
    <div class="grid grid-cols-4 h-16">
        {{-- Home --}}
        <a href="/" 
           class="flex flex-col items-center justify-center py-2 px-2 hover:bg-gray-50 transition-colors {{ request()->is('/') ? 'text-blue-600' : 'text-gray-600' }}"
           data-gtm-menu-type="mobile-bottom"
           data-gtm-menu-category="navigation"
           data-gtm-menu-label="{{ $labels['home'][$currentLang] ?? $labels['home']['kor'] }}"
           data-gtm-menu-position="1">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-xs">{{ $labels['home'][$currentLang] ?? $labels['home']['kor'] }}</span>
        </a>
        
        {{-- Self Diagnosis --}}
        <a href="/surveys" 
           class="flex flex-col items-center justify-center py-2 px-2 hover:bg-gray-50 transition-colors {{ request()->is('surveys*') ? 'text-blue-600' : 'text-gray-600' }}"
           data-gtm-menu-type="mobile-bottom"
           data-gtm-menu-category="navigation"
           data-gtm-menu-label="{{ $labels['diagnosis'][$currentLang] ?? $labels['diagnosis']['kor'] }}"
           data-gtm-menu-position="2">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-xs">{{ $labels['diagnosis'][$currentLang] ?? $labels['diagnosis']['kor'] }}</span>
        </a>
        
        {{-- Dashboard --}}
        <a href="{{ auth()->check() ? '/recovery-dashboard' : '/login' }}" 
           class="flex flex-col items-center justify-center py-2 px-2 hover:bg-gray-50 transition-colors {{ request()->is('dashboard*') || request()->is('recovery-dashboard*') ? 'text-blue-600' : 'text-gray-600' }}"
           data-gtm-menu-type="mobile-bottom"
           data-gtm-menu-category="navigation"
           data-gtm-menu-label="{{ $labels['dashboard'][$currentLang] ?? $labels['dashboard']['kor'] }}"
           data-gtm-menu-position="3">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="text-xs">{{ $labels['dashboard'][$currentLang] ?? $labels['dashboard']['kor'] }}</span>
        </a>
        
        {{-- My Page --}}
        <a href="{{ auth()->check() ? '/user/profile' : '/login' }}" 
           class="flex flex-col items-center justify-center py-2 px-2 hover:bg-gray-50 transition-colors {{ request()->is('user/profile*') ? 'text-blue-600' : 'text-gray-600' }}"
           data-gtm-menu-type="mobile-bottom"
           data-gtm-menu-category="navigation"
           data-gtm-menu-label="{{ $labels['mypage'][$currentLang] ?? $labels['mypage']['kor'] }}"
           data-gtm-menu-position="4">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-xs">{{ $labels['mypage'][$currentLang] ?? $labels['mypage']['kor'] }}</span>
        </a>
    </div>
</div>

{{-- Add padding to body to prevent content from being hidden behind bottom nav --}}
<style>
    @media (max-width: 767px) {
        body {
            padding-bottom: 64px; /* Height of bottom navigation */
        }
    }
</style>