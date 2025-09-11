<script>
document.addEventListener('DOMContentLoaded', function() {
    // 기본 Filament 네비게이션 숨기기 및 커스텀 네비게이션 로드
    const sidebar = document.querySelector('.fi-sidebar-nav');
    
    if (sidebar) {
        // 기존 네비게이션 내용 제거
        sidebar.innerHTML = '';
        
        // 커스텀 네비게이션 로드
        fetch('/admin/custom-navigation')
            .then(response => response.text())
            .then(html => {
                sidebar.innerHTML = html;
                
                // Alpine.js 재초기화
                if (typeof Alpine !== 'undefined') {
                    Alpine.initTree(sidebar);
                }
            })
            .catch(error => {
                console.error('Failed to load custom navigation:', error);
            });
    }
});

// CSS 스타일 추가
const style = document.createElement('style');
style.textContent = `
    /* 기본 Filament 네비게이션 숨기기 */
    .fi-sidebar-nav > ul {
        display: none !important;
    }
    
    /* 커스텀 네비게이션 표시 */
    .custom-full-navigation {
        display: flex !important;
    }
`;
document.head.appendChild(style);
</script>