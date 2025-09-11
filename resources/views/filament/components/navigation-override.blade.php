<script>
document.addEventListener('DOMContentLoaded', function() {
    const initCustomNav = () => {
        const sidebar = document.querySelector('.fi-sidebar-nav');
        
        if (sidebar && !sidebar.dataset.customized) {
            // 기존 네비게이션 숨기기
            const originalNav = sidebar.querySelector('ul.fi-sidebar-nav-groups');
            if (originalNav) {
                originalNav.style.display = 'none';
            }
            
            // 커스텀 네비게이션이 없으면 추가
            if (!sidebar.querySelector('.custom-full-navigation')) {
                // 템플릿에서 HTML 가져오기
                const template = document.getElementById('custom-navigation-template');
                if (template) {
                    const customNav = template.content.cloneNode(true);
                    sidebar.appendChild(customNav);
                    
                    // Alpine.js 초기화
                    if (typeof Alpine !== 'undefined') {
                        Alpine.nextTick(() => {
                            const nav = sidebar.querySelector('.custom-full-navigation');
                            if (nav) {
                                Alpine.initTree(nav);
                            }
                        });
                    }
                }
            }
            
            sidebar.dataset.customized = 'true';
        }
    };
    
    // 초기 실행
    initCustomNav();
    
    // Livewire 네비게이션 후에도 실행
    if (window.Livewire) {
        Livewire.hook('message.processed', () => {
            setTimeout(initCustomNav, 100);
        });
    }
});
</script>

{{-- 커스텀 네비게이션 템플릿 --}}
<template id="custom-navigation-template">
    @include('filament.components.full-custom-navigation')
</template>

<style>
    /* 기본 Filament 네비게이션 숨기기 */
    .fi-sidebar-nav > ul.fi-sidebar-nav-groups:not(.custom-nav) {
        display: none !important;
    }
    
    /* 커스텀 네비게이션 표시 */
    .custom-full-navigation {
        display: block !important;
        width: 100%;
        height: 100%;
    }
    
    /* 사이드바 overflow 설정 - 2차 메뉴가 잘리지 않도록 */
    .fi-sidebar {
        overflow: visible !important;
    }
    
    .fi-sidebar-nav {
        overflow: visible !important;
    }
    
    .fi-sidebar-nav-scrollable {
        overflow: visible !important;
    }
    
    /* z-index 조정 */
    .fi-sidebar {
        z-index: 30;
    }
    
    .submenu-panel {
        z-index: 40;
    }
    
    /* 사이드바 접기 버튼만 숨기기 */
    .fi-sidebar-toggle {
        display: none !important;
    }
    
    button[x-on\:click="$store.sidebar.toggleCollapsedState()"] {
        display: none !important;
    }
</style>