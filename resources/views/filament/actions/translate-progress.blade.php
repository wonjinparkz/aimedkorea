{{-- Translation Progress Modal Component --}}
@props(['show' => false, 'totalPosts' => 0, 'completedPosts' => 0, 'currentLanguage' => '', 'currentPost' => ''])

<div 
    x-data="{ 
        show: @js($show),
        totalPosts: @js($totalPosts),
        completedPosts: @js($completedPosts),
        currentLanguage: @js($currentLanguage),
        currentPost: @js($currentPost),
        progress: 0,
        
        updateProgress() {
            this.progress = this.totalPosts > 0 ? Math.round((this.completedPosts / this.totalPosts) * 100) : 0;
        }
    }"
    x-init="updateProgress()"
    x-show="show"
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    style="display: none;"
>
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                🌐 게시글 번역 진행 중
            </h3>
            <div class="text-sm text-gray-500">
                <span x-text="completedPosts"></span> / <span x-text="totalPosts"></span>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>진행률</span>
                <span><span x-text="progress"></span>%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div 
                    class="bg-green-500 h-3 rounded-full transition-all duration-500 ease-out"
                    :style="`width: ${progress}%`"
                ></div>
            </div>
        </div>
        
        <!-- Current Status -->
        <div class="space-y-2 mb-6">
            <div class="flex items-center text-sm">
                <span class="font-medium text-gray-700 w-20">현재 언어:</span>
                <span class="text-blue-600" x-text="currentLanguage || '대기 중...'"></span>
            </div>
            <div class="flex items-center text-sm">
                <span class="font-medium text-gray-700 w-20">현재 게시글:</span>
                <span class="text-gray-600 truncate" x-text="currentPost || '대기 중...'"></span>
            </div>
        </div>
        
        <!-- Status Icons -->
        <div class="flex justify-center space-x-2 mb-4">
            <div class="flex items-center space-x-1">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-xs text-gray-600">번역 중</span>
            </div>
        </div>
        
        <!-- Warning Message -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-xs font-medium text-yellow-800">주의사항</p>
                    <p class="text-xs text-yellow-700 mt-1">
                        번역이 완료될 때까지 브라우저를 닫지 마세요.<br>
                        번역 중에는 다른 작업을 피해주세요.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Cancel Button (optional) -->
        <div class="flex justify-end">
            <button 
                @click="show = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                x-show="progress >= 100"
            >
                닫기
            </button>
        </div>
    </div>
</div>

{{-- JavaScript for real-time updates --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('translationProgress', () => ({
        show: false,
        totalPosts: 0,
        completedPosts: 0,
        currentLanguage: '',
        currentPost: '',
        progress: 0,
        
        init() {
            // Listen for translation progress events
            window.addEventListener('translation-progress', (event) => {
                this.updateProgress(event.detail);
            });
            
            // Listen for translation complete events
            window.addEventListener('translation-complete', (event) => {
                this.completeTranslation(event.detail);
            });
        },
        
        startTranslation(totalPosts) {
            this.show = true;
            this.totalPosts = totalPosts;
            this.completedPosts = 0;
            this.progress = 0;
            this.currentLanguage = '';
            this.currentPost = '';
        },
        
        updateProgress(data) {
            this.completedPosts = data.completed || this.completedPosts;
            this.currentLanguage = data.language || this.currentLanguage;
            this.currentPost = data.post || this.currentPost;
            this.progress = this.totalPosts > 0 ? Math.round((this.completedPosts / this.totalPosts) * 100) : 0;
        },
        
        completeTranslation(data) {
            this.progress = 100;
            this.currentLanguage = '완료';
            this.currentPost = data.message || '모든 번역이 완료되었습니다.';
            
            // Auto-hide after 3 seconds
            setTimeout(() => {
                this.show = false;
            }, 3000);
        }
    }));
});

// Global functions for triggering events
function startTranslationProgress(totalPosts) {
    window.dispatchEvent(new CustomEvent('translation-start', { 
        detail: { totalPosts } 
    }));
}

function updateTranslationProgress(completed, language, post) {
    window.dispatchEvent(new CustomEvent('translation-progress', { 
        detail: { completed, language, post } 
    }));
}

function completeTranslation(message) {
    window.dispatchEvent(new CustomEvent('translation-complete', { 
        detail: { message } 
    }));
}
</script>

{{-- Styles for animations --}}
<style>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}
</style>