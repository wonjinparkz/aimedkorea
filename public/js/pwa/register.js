// PWA Registration Script for AimedKorea

// Check if browser supports service workers
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        registerServiceWorker();
    });
}

// Register service worker
async function registerServiceWorker() {
    try {
        // Use static version for service worker
        const SW_VERSION = '2025-01-12-v2';
        const registration = await navigator.serviceWorker.register('/service-worker.js?v=' + SW_VERSION, {
            scope: '/'
        });
        
        console.log('[PWA] Service Worker registered successfully:', registration.scope);
        
        // Check for updates
        registration.addEventListener('updatefound', () => {
            console.log('[PWA] New service worker found!');
            const newWorker = registration.installing;
            
            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    // New service worker is installed, but not yet active
                    console.log('[PWA] New service worker installed, showing update prompt');
                    showUpdatePrompt();
                }
            });
        });
        
        // Handle controller change
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            console.log('[PWA] Controller changed, reloading page');
            window.location.reload();
        });
        
    } catch (error) {
        console.error('[PWA] Service Worker registration failed:', error);
    }
}

// Show update prompt when new version is available
function showUpdatePrompt() {
    const updateBanner = document.createElement('div');
    updateBanner.className = 'fixed top-0 left-0 right-0 bg-blue-600 text-white p-4 shadow-lg z-50';
    updateBanner.innerHTML = `
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <p class="font-medium">새로운 버전이 있습니다. 업데이트하시겠습니까?</p>
            <button onclick="updateServiceWorker()" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50">
                업데이트
            </button>
        </div>
    `;
    document.body.appendChild(updateBanner);
}

// Update service worker
window.updateServiceWorker = async function() {
    const registration = await navigator.serviceWorker.getRegistration();
    if (registration && registration.waiting) {
        // Tell the waiting service worker to take control
        registration.waiting.postMessage({ type: 'SKIP_WAITING' });
    }
};

// Handle install prompt
let deferredPrompt;
let installButton = null;

window.addEventListener('beforeinstallprompt', (e) => {
    console.log('[PWA] beforeinstallprompt event fired');
    
    // Prevent the mini-infobar from appearing on mobile
    e.preventDefault();
    
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    
    // Show install button or banner
    showInstallPromotion();
    
    // Log the platforms
    console.log('[PWA] Platforms:', e.platforms);
});

// Show install promotion
function showInstallPromotion() {
    // Check if already installed
    if (window.matchMedia('(display-mode: standalone)').matches) {
        console.log('[PWA] Already installed');
        return;
    }
    
    // Check if install was previously dismissed
    const dismissed = localStorage.getItem('pwa-install-dismissed');
    if (dismissed && Date.now() - parseInt(dismissed) < 7 * 24 * 60 * 60 * 1000) {
        // Don't show again for 7 days
        return;
    }
    
    // Create install banner
    const installBanner = document.createElement('div');
    installBanner.id = 'pwa-install-banner';
    installBanner.className = 'fixed bottom-0 left-0 right-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 shadow-2xl z-40 transform translate-y-0 transition-transform duration-300';
    installBanner.innerHTML = `
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="/images/icons/icon-72x72.png" alt="AimedKorea" class="w-12 h-12 rounded-lg shadow" onerror="this.style.display='none'">
                    <div>
                        <h3 class="font-semibold text-lg">AimedKorea 앱 설치</h3>
                        <p class="text-blue-100 text-sm">홈 화면에 추가하고 오프라인에서도 사용하세요</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="pwa-install-button" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition">
                        설치
                    </button>
                    <button onclick="dismissInstallBanner()" class="text-white hover:text-blue-200 p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add to page after a delay
    setTimeout(() => {
        document.body.appendChild(installBanner);
        
        // Add click handler
        installButton = document.getElementById('pwa-install-button');
        if (installButton) {
            installButton.addEventListener('click', installPWA);
        }
    }, 3000); // Show after 3 seconds
}

// Install PWA
async function installPWA() {
    // Check if already installed
    if (window.matchMedia('(display-mode: standalone)').matches) {
        alert('AI-MED 앱이 이미 설치되어 있습니다.');
        return;
    }
    
    // For iOS devices
    if (/iPhone|iPad|iPod/.test(navigator.userAgent) && !window.navigator.standalone) {
        showIOSInstallGuide();
        return;
    }
    
    // For Android/Chrome
    if (!deferredPrompt) {
        console.log('[PWA] No deferred prompt available');
        
        // Check if Samsung Internet
        if (/SamsungBrowser/i.test(navigator.userAgent)) {
            showSamsungInstallGuide();
            return;
        }
        
        // Show manual install instructions
        showManualInstallGuide();
        return;
    }
    
    // Hide the banner
    const banner = document.getElementById('pwa-install-banner');
    if (banner) {
        banner.style.transform = 'translateY(100%)';
        setTimeout(() => banner.remove(), 300);
    }
    
    // Show the install prompt
    deferredPrompt.prompt();
    
    // Wait for the user to respond to the prompt
    const { outcome } = await deferredPrompt.userChoice;
    
    console.log(`[PWA] User response to install prompt: ${outcome}`);
    
    if (outcome === 'accepted') {
        console.log('[PWA] User accepted the install prompt');
        // Track installation
        if (typeof gtag !== 'undefined') {
            gtag('event', 'pwa_install', {
                'event_category': 'engagement'
            });
        }
    } else {
        console.log('[PWA] User dismissed the install prompt');
    }
    
    // Clear the deferred prompt
    deferredPrompt = null;
}

// Make installPWA globally accessible
window.installPWA = installPWA;

// Dismiss install banner
window.dismissInstallBanner = function() {
    const banner = document.getElementById('pwa-install-banner');
    if (banner) {
        banner.style.transform = 'translateY(100%)';
        setTimeout(() => banner.remove(), 300);
    }
    
    // Remember dismissal
    localStorage.setItem('pwa-install-dismissed', Date.now().toString());
};

// Track app installation
window.addEventListener('appinstalled', () => {
    console.log('[PWA] App was installed');
    
    // Hide install promotion
    const banner = document.getElementById('pwa-install-banner');
    if (banner) {
        banner.remove();
    }
    
    // Track installation
    if (typeof gtag !== 'undefined') {
        gtag('event', 'pwa_installed', {
            'event_category': 'engagement'
        });
    }
});

// Online/Offline detection
window.addEventListener('online', () => {
    console.log('[PWA] Back online');
    updateOnlineStatus(true);
});

window.addEventListener('offline', () => {
    console.log('[PWA] Gone offline');
    updateOnlineStatus(false);
});

// Update online status indicator
function updateOnlineStatus(isOnline) {
    // Only show notification when going offline
    if (isOnline) {
        // Remove existing offline notification when back online
        const existingNotification = document.getElementById('connection-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        // Update body class
        document.body.classList.remove('offline');
        return;
    }
    
    // Remove existing notification
    const existingNotification = document.getElementById('connection-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create offline notification only
    const notification = document.createElement('div');
    notification.id = 'connection-notification';
    notification.className = 'fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 transform transition-transform duration-300 bg-red-500 text-white';
    notification.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span>오프라인 상태입니다</span>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
    
    // Update body class
    document.body.classList.add('offline');
}

// iOS specific install instructions
if (/iPhone|iPad|iPod/.test(navigator.userAgent) && !window.navigator.standalone) {
    // Check if iOS install guide was shown before
    const iosGuideShown = localStorage.getItem('ios-install-guide-shown');
    if (!iosGuideShown) {
        setTimeout(() => {
            showIOSInstallGuide();
        }, 10000); // Show after 10 seconds
    }
}

// Show iOS install guide
function showIOSInstallGuide() {
    // Remove existing guide if any
    const existingGuide = document.getElementById('ios-install-guide');
    if (existingGuide) {
        existingGuide.remove();
    }
    
    const guide = document.createElement('div');
    guide.id = 'ios-install-guide';
    guide.className = 'fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-2xl p-6 z-50 transform translate-y-0 transition-transform duration-300';
    guide.innerHTML = `
        <div class="max-w-md mx-auto">
            <h3 class="text-lg font-semibold mb-4">AI-MED를 홈 화면에 추가</h3>
            <ol class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 mr-3 text-sm">1</span>
                    <span>하단의 공유 버튼 <svg class="inline-block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.632 8.048c.516-.159.934-.55 1.118-1.045a11.093 11.093 0 00.57-3.512c0-1.278-.217-2.508-.57-3.512a1.5 1.5 0 00-1.118-1.045m-9.632 8.048a8.97 8.97 0 016.948 0m-9.632-8.048A11.087 11.087 0 003.57 8.349a1.5 1.5 0 00-1.118 1.045 11.084 11.084 0 000 7.025 1.5 1.5 0 001.118 1.045m9.632-8.048A8.97 8.97 0 0112 5.25c1.77 0 3.427.51 4.822 1.392"></path></svg>을 탭하세요</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 mr-3 text-sm">2</span>
                    <span>"홈 화면에 추가"를 선택하세요</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 mr-3 text-sm">3</span>
                    <span>오른쪽 상단의 "추가"를 탭하세요</span>
                </li>
            </ol>
            <button onclick="closeIOSGuide()" class="w-full mt-6 bg-blue-500 text-white py-3 rounded-lg font-medium">
                알겠습니다
            </button>
        </div>
    `;
    
    document.body.appendChild(guide);
}

// Show manual install guide for Chrome/Android
function showManualInstallGuide() {
    // Remove existing guide if any
    const existingGuide = document.getElementById('manual-install-guide');
    if (existingGuide) {
        existingGuide.remove();
    }
    
    const guide = document.createElement('div');
    guide.id = 'manual-install-guide';
    guide.className = 'fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-2xl p-6 z-50 transform translate-y-0 transition-transform duration-300';
    guide.innerHTML = `
        <div class="max-w-md mx-auto">
            <h3 class="text-lg font-semibold mb-4">AI-MED 앱 설치하기</h3>
            <ol class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 mr-3 text-sm">1</span>
                    <span>브라우저 메뉴(⋮) 클릭</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 mr-3 text-sm">2</span>
                    <span>"앱 설치" 또는 "홈 화면에 추가" 선택</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 mr-3 text-sm">3</span>
                    <span>"설치" 버튼 클릭</span>
                </li>
            </ol>
            <button onclick="closeManualGuide()" class="w-full mt-6 bg-blue-500 text-white py-3 rounded-lg font-medium">
                알겠습니다
            </button>
        </div>
    `;
    
    document.body.appendChild(guide);
}

// Close manual install guide
window.closeManualGuide = function() {
    const guide = document.getElementById('manual-install-guide');
    if (guide) {
        guide.style.transform = 'translateY(100%)';
        setTimeout(() => guide.remove(), 300);
    }
};

// Close iOS guide
window.closeIOSGuide = function() {
    const guide = document.getElementById('ios-install-guide');
    if (guide) {
        guide.style.transform = 'translateY(100%)';
        setTimeout(() => guide.remove(), 300);
    }
    localStorage.setItem('ios-install-guide-shown', 'true');
};

// Show Samsung Internet install guide
function showSamsungInstallGuide() {
    // Remove existing guide if any
    const existingGuide = document.getElementById('samsung-install-guide');
    if (existingGuide) {
        existingGuide.remove();
    }
    
    const guide = document.createElement('div');
    guide.id = 'samsung-install-guide';
    guide.className = 'fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-2xl p-6 z-50 transform translate-y-0 transition-transform duration-300';
    guide.innerHTML = `
        <div class="max-w-md mx-auto">
            <h3 class="text-lg font-semibold mb-4">Samsung Internet에서 AI-MED 설치</h3>
            <ol class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 mr-3 text-sm">1</span>
                    <span>하단 메뉴바의 메뉴(≡) 버튼 클릭</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 mr-3 text-sm">2</span>
                    <span>"페이지 추가" → "홈 화면" 선택</span>
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0 mr-3 text-sm">3</span>
                    <span>"추가" 버튼 클릭</span>
                </li>
            </ol>
            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    💡 더 나은 PWA 경험을 위해 Chrome 브라우저 사용을 권장합니다.
                </p>
            </div>
            <button onclick="closeSamsungGuide()" class="w-full mt-6 bg-blue-500 text-white py-3 rounded-lg font-medium">
                알겠습니다
            </button>
        </div>
    `;
    
    document.body.appendChild(guide);
}

// Close Samsung guide
window.closeSamsungGuide = function() {
    const guide = document.getElementById('samsung-install-guide');
    if (guide) {
        guide.style.transform = 'translateY(100%)';
        setTimeout(() => guide.remove(), 300);
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    console.log('[PWA] DOM loaded, checking PWA status');
    
    // Check if running as installed PWA
    if (window.matchMedia('(display-mode: standalone)').matches || 
        window.navigator.standalone === true) {
        console.log('[PWA] Running as installed PWA');
        document.body.classList.add('pwa-installed');
        
        // Track PWA usage
        if (typeof gtag !== 'undefined') {
            gtag('event', 'pwa_usage', {
                'event_category': 'engagement',
                'event_label': 'standalone'
            });
        }
    }
    
    // Only update status if offline (don't show online notification on page load)
    if (!navigator.onLine) {
        updateOnlineStatus(false);
    }
});