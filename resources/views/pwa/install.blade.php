@extends('layouts.app')

@section('title', 'AI-MED 앱 설치')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-2xl mx-auto px-4">
        {{-- 메인 카드 --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            {{-- 헤더 이미지 --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-center">
                <img src="/images/icons/icon-192x192.png" alt="AI-MED" class="w-32 h-32 mx-auto mb-4 rounded-2xl shadow-lg">
                <h1 class="text-3xl font-bold text-white mb-2">AI-MED Korea</h1>
            </div>
            
            {{-- 설치 버튼 섹션 --}}
            <div class="p-8">
                {{-- 자동 설치 버튼 --}}
                <button onclick="installApp()" id="install-button" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-lg font-semibold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200 mb-6">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <span>앱 설치하기</span>
                    </div>
                </button>
                
                {{-- 설치 불가능 메시지 --}}
                <div id="install-not-available" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <p class="text-yellow-800 text-sm">
                        자동 설치가 지원되지 않습니다. 아래 수동 설치 방법을 참고해주세요.
                    </p>
                </div>
                
                {{-- 기기별 안내 --}}
                <div class="space-y-4">
                    <p class="text-center text-gray-600 text-sm">또는 수동으로 설치하세요</p>
                    
                    {{-- Android 안내 --}}
                    <div class="border border-gray-200 rounded-xl p-4">
                        <h3 class="font-semibold text-lg mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M17.6 9.48l1.84-3.18c.16-.31.04-.69-.26-.85c-.29-.15-.65-.04-.82.26l-1.88 3.24c-1.4-.6-2.96-.94-4.63-.94c-1.67 0-3.23.34-4.63.94L5.34 5.71c-.17-.3-.53-.41-.82-.26c-.3.16-.42.54-.26.85l1.84 3.18C2.72 11.16.62 14.36.25 18h23.5c-.37-3.64-2.47-6.84-5.85-8.52M7 16c-.55 0-1-.45-1-1s.45-1 1-1s1 .45 1 1s-.45 1-1 1m10 0c-.55 0-1-.45-1-1s.45-1 1-1s1 .45 1 1s-.45 1-1 1"/>
                            </svg>
                            Android (Chrome)
                        </h3>
                        <ol class="text-sm text-gray-600 space-y-1">
                            <li>1. Chrome 브라우저에서 이 페이지를 여세요</li>
                            <li>2. 메뉴(⋮) → "앱 설치" 클릭</li>
                            <li>3. "설치" 버튼을 클릭하세요</li>
                        </ol>
                    </div>
                    
                    {{-- iOS 안내 --}}
                    <div class="border border-gray-200 rounded-xl p-4">
                        <h3 class="font-semibold text-lg mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-800" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M18.71 19.5C17.88 20.74 17 21.95 15.66 21.97C14.32 22 13.89 21.18 12.37 21.18C10.84 21.18 10.37 21.95 9.09997 22C7.78997 22.05 6.79997 20.68 5.95997 19.47C4.24997 17 2.93997 12.45 4.69997 9.39C5.56997 7.87 7.12997 6.91 8.81997 6.88C10.1 6.86 11.32 7.75 12.11 7.75C12.89 7.75 14.37 6.68 15.92 6.84C16.57 6.87 18.39 7.1 19.56 8.82C19.47 8.88 17.39 10.1 17.41 12.63C17.44 15.65 20.06 16.66 20.09 16.67C20.06 16.74 19.67 18.11 18.71 19.5M13 3.5C13.73 2.67 14.94 2.04 15.94 2C16.07 3.17 15.6 4.35 14.9 5.19C14.21 6.04 13.07 6.7 11.95 6.61C11.8 5.46 12.36 4.26 13 3.5Z"/>
                            </svg>
                            iOS (Safari)
                        </h3>
                        <ol class="text-sm text-gray-600 space-y-1">
                            <li>1. Safari 브라우저에서 이 페이지를 여세요</li>
                            <li>2. 공유 버튼 
                                <svg class="inline-block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.632 8.048c.516-.159.934-.55 1.118-1.045a11.093 11.093 0 00.57-3.512c0-1.278-.217-2.508-.57-3.512a1.5 1.5 0 00-1.118-1.045m-9.632 8.048a8.97 8.97 0 016.948 0m-9.632-8.048A11.087 11.087 0 003.57 8.349a1.5 1.5 0 00-1.118 1.045 11.084 11.084 0 000 7.025 1.5 1.5 0 001.118 1.045m9.632-8.048A8.97 8.97 0 0112 5.25c1.77 0 3.427.51 4.822 1.392"></path>
                                </svg> 탭
                            </li>
                            <li>3. "홈 화면에 추가" 선택</li>
                            <li>4. 오른쪽 상단의 "추가" 탭</li>
                        </ol>
                    </div>
                    
                    {{-- 데스크톱 안내 --}}
                    <div class="border border-gray-200 rounded-xl p-4">
                        <h3 class="font-semibold text-lg mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M21 2H3C1.89 2 1 2.89 1 4V16C1 17.11 1.89 18 3 18H10L8 21V22H16V21L14 18H21C22.11 18 23 17.11 23 16V4C23 2.89 22.11 2 21 2M21 16H3V4H21V16Z"/>
                            </svg>
                            데스크톱 (Chrome/Edge)
                        </h3>
                        <ol class="text-sm text-gray-600 space-y-1">
                            <li>1. Chrome 또는 Edge 브라우저 사용</li>
                            <li>2. 주소창 우측의 설치 아이콘 클릭</li>
                            <li>3. "설치" 버튼을 클릭하세요</li>
                        </ol>
                    </div>
                </div>
                
                {{-- 특징 소개 --}}
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h2 class="text-xl font-semibold mb-4">앱 설치 시 장점</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-medium">오프라인 사용</p>
                                <p class="text-sm text-gray-600">인터넷 없이도 기본 기능 사용</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-medium">빠른 실행</p>
                                <p class="text-sm text-gray-600">홈 화면에서 바로 접속</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-medium">전체 화면</p>
                                <p class="text-sm text-gray-600">네이티브 앱처럼 사용</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-medium">푸시 알림</p>
                                <p class="text-sm text-gray-600">중요한 업데이트 실시간 수신</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- QR 코드 섹션 --}}
                <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600 mb-4">모바일에서 QR 코드로 접속하세요</p>
                    <div class="inline-block p-4 bg-white rounded-xl shadow-lg">
                        <div id="qrcode" class="mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- 홈으로 돌아가기 링크 --}}
        <div class="text-center mt-8">
            <a href="/" class="text-blue-600 hover:text-blue-700 font-medium">
                ← 홈으로 돌아가기
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- QR Code Library --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    // PWA 설치 변수
    let deferredPrompt = null;
    
    // beforeinstallprompt 이벤트 리스너
    window.addEventListener('beforeinstallprompt', (event) => {
        console.log('[PWA] beforeinstallprompt 이벤트 발생');
        // 기본 설치 프롬프트 방지
        event.preventDefault();
        // 이벤트 저장
        deferredPrompt = event;
        // 설치 버튼 활성화
        document.getElementById('install-button').disabled = false;
    });
    
    // 설치 함수
    window.installApp = async function() {
        console.log('[PWA] installApp 호출됨');
        
        // 이미 설치된 경우
        if (window.matchMedia('(display-mode: standalone)').matches) {
            alert('이미 앱이 설치되어 있습니다');
            return;
        }
        
        // iOS인 경우
        if (/iPhone|iPad|iPod/.test(navigator.userAgent)) {
            showIOSGuide();
            return;
        }
        
        // Samsung Internet인 경우
        if (/SamsungBrowser/i.test(navigator.userAgent)) {
            showSamsungGuide();
            return;
        }
        
        // deferredPrompt가 없는 경우
        if (!deferredPrompt) {
            console.log('[PWA] deferredPrompt 없음');
            document.getElementById('install-not-available').classList.remove('hidden');
            document.getElementById('install-button').classList.add('opacity-50');
            return;
        }
        
        // 설치 프롬프트 표시
        try {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`[PWA] 사용자 선택: ${outcome}`);
            
            if (outcome === 'accepted') {
                console.log('[PWA] 설치 승인됨');
            } else {
                console.log('[PWA] 설치 취소됨');
            }
            
            deferredPrompt = null;
        } catch (error) {
            console.error('[PWA] 설치 오류:', error);
            alert('설치 중 오류가 발생했습니다');
        }
    };
    
    // iOS 가이드 표시
    function showIOSGuide() {
        alert('iOS에서 설치하기:\n\n1. Safari 하단의 공유 버튼 탭\n2. "홈 화면에 추가" 선택\n3. "추가" 탭');
    }
    
    // Samsung Internet 가이드 표시
    function showSamsungGuide() {
        alert('Samsung Internet에서 설치하기:\n\n1. 하단 메뉴(≡) 클릭\n2. "페이지 추가" → "홈 화면" 선택\n3. "추가" 클릭');
    }
    
    // 앱 설치 완료 이벤트
    window.addEventListener('appinstalled', () => {
        console.log('[PWA] 앱 설치 완료');
        deferredPrompt = null;
        document.getElementById('install-button').textContent = '설치 완료';
        document.getElementById('install-button').disabled = true;
    });
    
    // QR 코드 생성
    document.addEventListener('DOMContentLoaded', function() {
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ url('/install') }}",
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
        
        // 초기 상태에서 이미 설치된 경우 확인
        if (window.matchMedia('(display-mode: standalone)').matches) {
            document.getElementById('install-button').innerHTML = '<div class="flex items-center justify-center space-x-3"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>이미 설치됨</span></div>';
            document.getElementById('install-button').disabled = true;
            document.getElementById('install-button').classList.add('opacity-50');
        }
    });
</script>
@endpush