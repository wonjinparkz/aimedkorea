// 간단한 PWA 설치 스크립트
let deferredPrompt = null;

// Service Worker 등록
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js')
            .then(registration => console.log('[PWA] Service Worker 등록 성공'))
            .catch(error => console.error('[PWA] Service Worker 등록 실패:', error));
    });
}

// beforeinstallprompt 이벤트 처리
window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredPrompt = event;
    console.log('[PWA] 설치 가능 상태');
});

// 전역 설치 함수
window.installApp = async function() {
    if (!deferredPrompt) {
        alert("이미 앱이 설치되어 있거나 앱을 설치할 수 없는 환경입니다");
        return;
    }
    
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    
    if (outcome === 'accepted') {
        console.log('[PWA] 사용자가 설치를 승인했습니다');
    }
    
    deferredPrompt = null;
};

// 앱 설치 완료 감지
window.addEventListener('appinstalled', () => {
    console.log('[PWA] 앱이 설치되었습니다');
    deferredPrompt = null;
});