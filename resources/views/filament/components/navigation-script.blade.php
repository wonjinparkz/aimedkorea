<script>
document.addEventListener('DOMContentLoaded', function() {
    // Alpine.js 컴포넌트 정의
    if (typeof Alpine !== 'undefined') {
        Alpine.data('customNavigation', () => ({
            activeGroup: null,
            submenuPanel: null,
            groupMenuItems: {},
            
            init() {
                // 네비게이션 구조 수정
                this.modifyNavigationStructure();
                // 현재 페이지에 해당하는 그룹 자동 열기
                this.detectCurrentGroup();
            },
            
            modifyNavigationStructure() {
                const sidebar = document.querySelector('.fi-sidebar-nav');
                if (!sidebar) return;
                
                // 서브메뉴 패널 생성
                const submenuPanel = document.createElement('div');
                submenuPanel.className = 'submenu-panel fi-sidebar-submenu';
                submenuPanel.setAttribute('x-data', '{}');
                
                document.body.appendChild(submenuPanel);
                this.submenuPanel = submenuPanel;
                
                // 그룹별 메뉴 아이템 저장 및 숨기기
                const groups = sidebar.querySelectorAll('.fi-sidebar-group');
                groups.forEach(group => {
                    const groupButton = group.querySelector('.fi-sidebar-group-button');
                    const groupItems = group.querySelector('.fi-sidebar-group-items');
                    
                    if (groupButton && groupItems) {
                        const groupLabel = groupButton.querySelector('.fi-sidebar-group-label')?.textContent?.trim();
                        
                        if (groupLabel && groupLabel !== '대시보드') {
                            // 메뉴 아이템 저장
                            this.groupMenuItems[groupLabel] = groupItems.cloneNode(true);
                            
                            // 1차 뎁스에서 하위 메뉴 숨기기
                            groupItems.style.display = 'none';
                            
                            // 그룹 아이콘 토글 제거
                            const toggleIcon = groupButton.querySelector('.fi-sidebar-group-toggle-icon');
                            if (toggleIcon) {
                                toggleIcon.style.display = 'none';
                            }
                            
                            // 클릭 이벤트 추가
                            groupButton.style.cursor = 'pointer';
                            groupButton.addEventListener('click', (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                this.toggleGroup(groupLabel, groupButton);
                            });
                        }
                    }
                });
            },
            
            toggleGroup(groupName, headerElement) {
                if (this.activeGroup === groupName) {
                    // 같은 그룹 클릭 시 닫기
                    this.activeGroup = null;
                    this.submenuPanel.classList.remove('open');
                    this.removeActiveClass();
                } else {
                    // 새 그룹 열기
                    this.activeGroup = groupName;
                    this.showSubmenu(groupName, headerElement);
                    this.setActiveClass(headerElement);
                }
            },
            
            showSubmenu(groupName, headerElement) {
                // 서브메뉴 패널 표시
                this.submenuPanel.classList.add('open');
                
                // 저장된 메뉴 아이템 사용
                const groupItems = this.groupMenuItems[groupName];
                if (groupItems) {
                    const items = groupItems.cloneNode(true);
                    items.style.display = 'block'; // 2차 뎁스에서는 표시
                    
                    // 타이틀 추가
                    const title = document.createElement('div');
                    title.className = 'submenu-title text-sm font-semibold text-gray-600 dark:text-gray-400';
                    title.textContent = groupName;
                    
                    // 화살표 아이콘 추가
                    const arrow = document.createElement('span');
                    arrow.innerHTML = '→';
                    arrow.className = 'text-gray-400 dark:text-gray-500 ml-2';
                    title.appendChild(arrow);
                    
                    this.submenuPanel.innerHTML = '';
                    this.submenuPanel.appendChild(title);
                    this.submenuPanel.appendChild(items);
                    
                    // 메인 컨텐츠 영역 조정
                    const mainContent = document.querySelector('.fi-main');
                    if (mainContent) {
                        mainContent.style.marginLeft = '470px';
                        mainContent.style.transition = 'margin-left 0.3s ease';
                    }
                }
            },
            
            setActiveClass(headerElement) {
                this.removeActiveClass();
                headerElement.classList.add('fi-active', 'bg-gray-50', 'dark:bg-white/5');
                headerElement.dataset.active = 'true';
            },
            
            removeActiveClass() {
                document.querySelectorAll('.fi-sidebar-group-button[data-active="true"]').forEach(el => {
                    el.classList.remove('fi-active', 'bg-gray-50', 'dark:bg-white/5');
                    el.dataset.active = 'false';
                });
                
                // 메인 컨텐츠 영역 원래대로
                const mainContent = document.querySelector('.fi-main');
                if (mainContent) {
                    mainContent.style.marginLeft = '';
                    mainContent.style.transition = 'margin-left 0.3s ease';
                }
            },
            
            detectCurrentGroup() {
                // 현재 활성 메뉴 항목의 그룹 찾기
                const activeItem = document.querySelector('.fi-sidebar-item.fi-active');
                if (activeItem) {
                    const groupElement = activeItem.closest('.fi-sidebar-group');
                    if (groupElement) {
                        const groupButton = groupElement.querySelector('.fi-sidebar-group-button');
                        const groupLabel = groupButton?.querySelector('.fi-sidebar-group-label')?.textContent?.trim();
                        if (groupLabel && groupLabel !== '대시보드' && this.groupMenuItems[groupLabel]) {
                            // 자동으로 해당 그룹 열기
                            setTimeout(() => {
                                this.toggleGroup(groupLabel, groupButton);
                            }, 100);
                        }
                    }
                }
            }
        }));
        
        // Alpine 컴포넌트 초기화
        Alpine.nextTick(() => {
            const nav = document.querySelector('.fi-sidebar-nav');
            if (nav && !nav.hasAttribute('x-data')) {
                nav.setAttribute('x-data', 'customNavigation');
                Alpine.initTree(nav);
            }
        });
    }
});

// CSS 스타일 추가
const style = document.createElement('style');
style.textContent = `
    .fi-sidebar-submenu {
        position: fixed;
        left: var(--sidebar-width, 270px);
        top: 64px;
        bottom: 0;
        width: 200px;
        background: rgb(255 255 255 / 0.95);
        border-left: 1px solid rgb(229 231 235);
        padding: 1rem;
        overflow-y: auto;
        z-index: 30;
        transition: transform 0.3s ease, opacity 0.3s ease;
        transform: translateX(-100%);
        opacity: 0;
        pointer-events: none;
    }
    
    .dark .fi-sidebar-submenu {
        background: rgb(17 24 39 / 0.95);
        border-left: 1px solid rgb(55 65 81);
    }
    
    .fi-sidebar-submenu.open {
        transform: translateX(0);
        opacity: 1;
        pointer-events: auto;
    }
    
    .fi-sidebar-group-button {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .fi-sidebar-group-button:hover {
        background: rgb(249 250 251) !important;
    }
    
    .dark .fi-sidebar-group-button:hover {
        background: rgb(255 255 255 / 0.05) !important;
    }
    
    .fi-sidebar-group-button.fi-active {
        color: rgb(245 158 11);
    }
    
    .dark .fi-sidebar-group-button.fi-active {
        color: rgb(251 191 36);
    }
    
    .submenu-title {
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgb(229 231 235);
    }
    
    .dark .submenu-title {
        border-bottom: 1px solid rgb(55 65 81);
    }
    
    .submenu-panel .fi-sidebar-group-items {
        display: block !important;
        padding: 0;
    }
    
    .submenu-panel .fi-sidebar-item-button {
        width: 100%;
        padding: 0.5rem 0.75rem;
        margin: 0.25rem 0;
    }
    
    /* 사이드바 열려있을 때 메인 컨텐츠 조정 */
    .fi-main {
        transition: margin-left 0.3s ease;
    }
    
    @media (max-width: 1024px) {
        .fi-sidebar-submenu {
            display: none !important;
        }
    }
`;
document.head.appendChild(style);
</script>