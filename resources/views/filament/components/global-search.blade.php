{{-- Global Search Component --}}
<div 
    x-data="globalSearch()"
    x-init="init()"
    style="display: inline-block;"
>
    {{-- Search Trigger Button --}}
    <button 
        @click="openSearch()"
        x-bind:style="`
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: ${isDarkMode ? '#242427' : 'rgba(255, 255, 255, 1)'};
            border: 1px solid ${isDarkMode ? '#18181B' : 'rgba(229, 231, 235, 1)'};
            border-radius: 0.5rem;
            transition: all 0.2s;
            cursor: pointer;
        `"
        @mouseover="$el.style.background = isDarkMode ? '#18181B' : 'rgba(249, 250, 251, 1)'; $el.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.1)';"
        @mouseout="$el.style.background = isDarkMode ? '#242427' : 'rgba(255, 255, 255, 1)'; $el.style.boxShadow = '';"
        aria-label="전역 검색 열기"
    >
        <svg x-bind:style="`width: 1.25rem; height: 1.25rem; color: ${isDarkMode ? 'rgba(156, 163, 175, 1)' : 'rgba(107, 114, 128, 1)'};`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <span x-bind:style="`margin-left: 0.5rem; font-size: 0.875rem; color: ${isDarkMode ? 'rgba(209, 213, 219, 1)' : 'rgba(75, 85, 99, 1)'};`">검색</span>
        <kbd x-bind:style="`
            margin-left: 0.5rem;
            font-size: 0.75rem;
            background: ${isDarkMode ? '#18181B' : 'rgba(243, 244, 246, 1)'};
            padding: 0.125rem 0.5rem;
            border-radius: 0.25rem;
            border: 1px solid ${isDarkMode ? 'rgba(75, 85, 99, 1)' : 'rgba(229, 231, 235, 1)'};
            color: ${isDarkMode ? 'rgba(156, 163, 175, 1)' : 'rgba(107, 114, 128, 1)'};
        `" x-text="shortcutKey"></kbd>
    </button>
    

    {{-- Search Modal --}}
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeSearch()"
        @keydown.escape.window="closeSearch()"
        x-bind:style="`
            display: ${isOpen ? 'block' : 'none'} !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: rgba(0, 0, 0, 0.7) !important;
            z-index: 2147483647 !important;
            isolation: isolate !important;
        `"
        role="dialog"
        aria-modal="true"
        aria-labelledby="search-dialog-title"
    >
        <div 
            @click.stop
            style="
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 90%;
                max-width: 56rem;
                max-height: 80vh;
                z-index: 2147483647;
            "
        >
            <div x-bind:style="`
                background: ${isDarkMode ? '#18181B' : 'white'} !important;
                border-radius: 0.5rem !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
                overflow: hidden !important;
            `">
                {{-- Search Header --}}
                <div x-bind:style="`
                    border-bottom: 1px solid ${isDarkMode ? '#242427' : 'rgb(229, 231, 235)'} !important;
                    padding: 1rem !important;
                `">
                    <div style="position: relative;">
                        <input
                            x-ref="searchInput"
                            x-model="searchQuery"
                            @input="performSearch()"
                            type="text"
                            role="search"
                            aria-label="검색어 입력"
                            placeholder="메뉴, 문서, 설정 검색..."
                            x-bind:style="`
                                width: 100% !important;
                                padding: 0.5rem 1rem 0.5rem 2.5rem !important;
                                border: 1px solid ${isDarkMode ? 'rgb(55, 65, 81)' : 'rgb(209, 213, 219)'} !important;
                                border-radius: 0.5rem !important;
                                font-size: 0.875rem !important;
                                outline: none !important;
                                background: ${isDarkMode ? '#242427' : 'white'} !important;
                                color: ${isDarkMode ? 'white' : 'black'} !important;
                            `"
                            onfocus="this.style.borderColor='rgba(59, 130, 246, 1)'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)';"
                            onblur="this.style.borderColor='rgba(209, 213, 219, 1)'; this.style.boxShadow='';"
                        >
                        <svg style="
                            position: absolute;
                            left: 0.75rem;
                            top: 0.625rem;
                            width: 1.25rem;
                            height: 1.25rem;
                            color: rgba(156, 163, 175, 1);
                        " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button
                            @click="closeSearch()"
                            style="
                                position: absolute;
                                right: 0.75rem;
                                top: 0.625rem;
                                color: rgba(156, 163, 175, 1);
                                cursor: pointer;
                                background: transparent;
                                border: none;
                                padding: 0;
                            "
                            onmouseover="this.style.color='rgba(75, 85, 99, 1)';"
                            onmouseout="this.style.color='rgba(156, 163, 175, 1)';"
                            aria-label="검색 닫기"
                        >
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Search Results --}}
                <div x-bind:style="`
                    max-height: 28rem !important;
                    overflow-y: auto !important;
                    background: ${isDarkMode ? '#18181B' : 'white'} !important;
                `">
                    {{-- Loading State --}}
                    <div x-show="isSearching" style="padding: 2rem; text-align: center;">
                        <svg style="
                            animation: spin 1s linear infinite;
                            height: 2rem;
                            width: 2rem;
                            color: rgba(156, 163, 175, 1);
                            margin: 0 auto;
                        " fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p style="
                            margin-top: 0.5rem;
                            font-size: 0.875rem;
                            color: rgba(107, 114, 128, 1);
                        " :style="isDarkMode ? 'color: rgba(156, 163, 175, 1);' : ''">검색 중...</p>
                    </div>

                    {{-- No Results --}}
                    <div x-show="!isSearching && searchQuery && searchResults.length === 0" style="padding: 2rem; text-align: center;">
                        <svg style="
                            width: 3rem;
                            height: 3rem;
                            color: rgba(209, 213, 219, 1);
                            margin: 0 auto;
                        " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p style="
                            margin-top: 0.5rem;
                            font-size: 0.875rem;
                            color: rgba(107, 114, 128, 1);
                        " :style="isDarkMode ? 'color: rgba(156, 163, 175, 1);' : ''">검색 결과가 없습니다</p>
                    </div>

                    {{-- Initial State - Simplified --}}
                    <div x-show="!isSearching && !searchQuery" style="padding: 2rem; text-align: center;">
                        <p style="
                            font-size: 0.875rem;
                            color: rgba(107, 114, 128, 1);
                        " :style="isDarkMode ? 'color: rgba(156, 163, 175, 1);' : ''">검색어를 입력하세요</p>
                    </div>

                    {{-- Search Results List --}}
                    <div x-show="!isSearching && searchQuery && searchResults.length > 0" class="py-2">
                        <template x-for="(result, index) in searchResults" :key="result.id">
                            <a
                                :href="result.url"
                                @mouseenter="selectedIndex = index"
                                :class="{'bg-blue-50': selectedIndex === index}"
                                class="block px-4 py-3 hover:bg-blue-50 border-b border-gray-100 last:border-b-0"
                            >
                                <div class="flex items-start pt-4">
                                    <div 
                                        class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center mr-3"
                                        x-bind:style="getResultTypeClass(result.type)"
                                    >
                                        <span x-html="getResultIcon(result.type)"></span>
                                    </div>
                                    <div class="flex-1 px-4 pb-4">
                                        <div class="font-medium text-gray-900" x-html="highlightText(result.title, searchQuery)"></div>
                                        <div class="text-sm text-gray-500 mt-1" x-html="highlightText(result.description, searchQuery)"></div>
                                        <div class="flex items-center mt-2 gap-4">
                                            <span 
                                                class="text-xs px-2 py-1 rounded-full"
                                                x-bind:style="getResultTypeTagClass(result.type)"
                                                x-text="result.type"
                                            ></span>
                                            <span class="text-xs text-gray-400 ml-2" x-text="result.path"></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                {{-- Search Footer --}}
                <div x-bind:style="`
                    border-top: 1px solid ${isDarkMode ? '#18181B' : 'rgba(229, 231, 235, 1)'};
                    background: ${isDarkMode ? '#242427' : 'rgba(249, 250, 251, 1)'};
                    padding: 5px 20px;
                `">
                    <div style="
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        font-size: 0.75rem;
                        color: rgba(107, 114, 128, 1);
                    ">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span style="display: flex; align-items: center;">
                                <kbd x-bind:style="`
                                    padding: 0.125rem 0.5rem;
                                    background: ${isDarkMode ? '#18181B' : 'white'};
                                    border: 1px solid ${isDarkMode ? 'rgba(75, 85, 99, 1)' : 'rgba(209, 213, 219, 1)'};
                                    border-radius: 0.25rem;
                                    font-size: 0.75rem;
                                    color: ${isDarkMode ? 'rgba(209, 213, 219, 1)' : 'inherit'};
                                `">↑↓</kbd>
                                <span x-bind:style="`margin-left: 0.25rem; color: ${isDarkMode ? 'rgba(156, 163, 175, 1)' : 'inherit'};`">이동</span>
                            </span>
                            <span style="display: flex; align-items: center;">
                                <kbd x-bind:style="`
                                    padding: 0.125rem 0.5rem;
                                    background: ${isDarkMode ? '#18181B' : 'white'};
                                    border: 1px solid ${isDarkMode ? 'rgba(75, 85, 99, 1)' : 'rgba(209, 213, 219, 1)'};
                                    border-radius: 0.25rem;
                                    font-size: 0.75rem;
                                    color: ${isDarkMode ? 'rgba(209, 213, 219, 1)' : 'inherit'};
                                `">Enter</kbd>
                                <span x-bind:style="`margin-left: 0.25rem; color: ${isDarkMode ? 'rgba(156, 163, 175, 1)' : 'inherit'};`">선택</span>
                            </span>
                            <span style="display: flex; align-items: center;">
                                <kbd x-bind:style="`
                                    padding: 0.125rem 0.5rem;
                                    background: ${isDarkMode ? '#18181B' : 'white'};
                                    border: 1px solid ${isDarkMode ? 'rgba(75, 85, 99, 1)' : 'rgba(209, 213, 219, 1)'};
                                    border-radius: 0.25rem;
                                    font-size: 0.75rem;
                                    color: ${isDarkMode ? 'rgba(209, 213, 219, 1)' : 'inherit'};
                                `">Esc</kbd>
                                <span x-bind:style="`margin-left: 0.25rem; color: ${isDarkMode ? 'rgba(156, 163, 175, 1)' : 'inherit'};`">닫기</span>
                            </span>
                        </div>
                        <div x-show="searchResults.length > 0" x-bind:style="`color: ${isDarkMode ? 'rgba(156, 163, 175, 1)' : 'inherit'};`">
                            <span x-text="searchResults.length"></span>개 결과
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .global-search-trigger {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .global-search-trigger:hover {
            background: #f9fafb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .global-search-trigger:focus {
            outline: none;
            ring: 2px;
            ring-color: #3b82f6;
        }
        
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        /* 검색 모달이 열렸을 때 Filament 사이드바를 가리기 위한 스타일 */
        [x-data="globalSearch()"][x-show="true"] ~ aside,
        [x-data="globalSearch()"][x-show="true"] ~ .fi-sidebar {
            z-index: 1 !important;
        }
    </style>

    <script>
        function globalSearch() {
            return {
                isOpen: false,
                searchQuery: '',
                searchResults: [],
                selectedIndex: 0,
                isSearching: false,
                searchTimeout: null,
                shortcutKey: '',
                searchIndex: [],
                isDarkMode: false,

                init() {
                    // Filament 다크모드 감지
                    // Filament 3.x는 localStorage에 'theme' 값을 저장하고 html 요소에 'dark' 클래스를 추가함
                    const checkDarkMode = () => {
                        // 1. localStorage에서 theme 확인 (Filament 설정)
                        const theme = localStorage.getItem('theme');
                        if (theme === 'dark') {
                            return true;
                        } else if (theme === 'light') {
                            return false;
                        }
                        
                        // 2. HTML 요소의 dark 클래스 확인
                        if (document.documentElement.classList.contains('dark')) {
                            return true;
                        }
                        
                        // 3. 시스템 설정 확인 (theme가 설정되지 않은 경우)
                        if (!theme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                            return true;
                        }
                        
                        return false;
                    };
                    
                    this.isDarkMode = checkDarkMode();
                    
                    // 디버깅용 로그
                    console.log('[GlobalSearch] Dark mode detection:', {
                        isDarkMode: this.isDarkMode,
                        localStorage: localStorage.getItem('theme'),
                        htmlClass: document.documentElement.classList.contains('dark'),
                        systemPreference: window.matchMedia('(prefers-color-scheme: dark)').matches
                    });
                    
                    // 다크모드 변경 감지
                    const observer = new MutationObserver(() => {
                        const newDarkMode = checkDarkMode();
                        if (this.isDarkMode !== newDarkMode) {
                            this.isDarkMode = newDarkMode;
                            console.log('[GlobalSearch] Dark mode changed:', this.isDarkMode);
                        }
                    });
                    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                    
                    // localStorage 변경 감지
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'theme') {
                            this.isDarkMode = checkDarkMode();
                            console.log('[GlobalSearch] Theme changed via localStorage:', this.isDarkMode);
                        }
                    });
                    // OS별 단축키 설정
                    const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
                    this.shortcutKey = isMac ? '⌘/' : 'Ctrl+/';
                    
                    // 단축키 이벤트 리스너
                    document.addEventListener('keydown', (e) => {
                        const isCtrlOrCmd = isMac ? e.metaKey : e.ctrlKey;
                        if (isCtrlOrCmd && e.key === '/') {
                            e.preventDefault();
                            this.openSearch();
                        }
                    });

                    // 검색 모달 내 키보드 네비게이션
                    this.$watch('isOpen', (value) => {
                        if (value) {
                            this.$nextTick(() => {
                                this.$refs.searchInput.focus();
                            });
                            
                            document.addEventListener('keydown', this.handleKeyNavigation.bind(this));
                        } else {
                            document.removeEventListener('keydown', this.handleKeyNavigation.bind(this));
                        }
                    });

                    // 로컬 스토리지에서 최근 검색 가져오기
                    
                    // 검색 인덱스 구축
                    this.buildSearchIndex();
                },

                buildSearchIndex() {
                    // Filament 메뉴 및 리소스 인덱싱
                    this.searchIndex = [
                        // 대시보드
                        { id: 'dashboard', title: '대시보드', description: '관리자 대시보드', type: '페이지', path: '관리자 > 대시보드', url: '/admin' },
                        
                        // 사용자 관리
                        { id: 'users', title: '사용자 목록', description: '전체 사용자 관리', type: '리소스', path: '관리자 > 사용자', url: '/admin/users' },
                        { id: 'users-create', title: '사용자 생성', description: '새 사용자 추가', type: '액션', path: '관리자 > 사용자 > 생성', url: '/admin/users/create' },
                        { id: 'roles', title: '역할 관리', description: '사용자 역할 및 권한', type: '리소스', path: '관리자 > 설정 > 역할', url: '/admin/shield/roles' },
                        
                        // 콘텐츠 관리
                        { id: 'posts', title: '게시물 관리', description: '블로그 및 뉴스 게시물', type: '리소스', path: '관리자 > 콘텐츠 > 게시물', url: '/admin/posts' },
                        { id: 'posts-create', title: '게시물 작성', description: '새 게시물 작성', type: '액션', path: '관리자 > 콘텐츠 > 게시물 > 작성', url: '/admin/posts/create' },
                        { id: 'heroes', title: 'Hero 슬라이더', description: '메인 배너 관리', type: '리소스', path: '관리자 > 홈 구성 > Hero', url: '/admin/heroes' },
                        
                        // 설문조사
                        { id: 'surveys', title: '설문조사', description: '설문조사 관리', type: '리소스', path: '관리자 > 설문', url: '/admin/surveys' },
                        { id: 'surveys-create', title: '설문 생성', description: '새 설문조사 만들기', type: '액션', path: '관리자 > 설문 > 생성', url: '/admin/surveys/create' },
                        { id: 'survey-responses', title: '설문 응답', description: '설문 응답 확인', type: '리소스', path: '관리자 > 설문 > 응답', url: '/admin/survey-responses' },
                        
                        // 미디어
                        { id: 'media', title: '미디어 라이브러리', description: '이미지 및 파일 관리', type: '페이지', path: '관리자 > 미디어', url: '/admin/media' },
                        
                        // 설정
                        { id: 'settings', title: '일반 설정', description: '사이트 설정', type: '페이지', path: '관리자 > 설정', url: '/admin/settings' },
                        { id: 'footer-menus', title: '푸터 메뉴', description: '푸터 메뉴 관리', type: '리소스', path: '관리자 > 사이트 > 푸터', url: '/admin/footer-menus' },
                        
                        // 파트너
                        { id: 'partners', title: '파트너 관리', description: '협력사 정보 관리', type: '리소스', path: '관리자 > 파트너', url: '/admin/partners' },
                        
                        // 루틴
                        { id: 'routines', title: '루틴 관리', description: '일상 루틴 콘텐츠', type: '리소스', path: '관리자 > 루틴', url: '/admin/routines' },
                    ];
                },

                openSearch() {
                    this.isOpen = true;
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.selectedIndex = 0;
                    
                    // Filament 사이드바 z-index 낮추기
                    const sidebar = document.querySelector('.fi-sidebar');
                    const aside = document.querySelector('aside');
                    if (sidebar) {
                        sidebar.style.setProperty('z-index', '1', 'important');
                    }
                    if (aside) {
                        aside.style.setProperty('z-index', '1', 'important');
                    }
                    
                    // body에 overflow hidden 추가하여 스크롤 방지
                    document.body.style.overflow = 'hidden';
                    
                    // GA 이벤트 트리거
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'search_open', {
                            'event_category': 'engagement',
                            'event_label': 'global_search'
                        });
                    }
                    
                    // 콘솔 로그
                    console.log('[GlobalSearch] Search opened', {
                        timestamp: new Date().toISOString(),
                        trigger: 'keyboard_shortcut'
                    });
                },

                closeSearch() {
                    this.isOpen = false;
                    this.searchQuery = '';
                    this.searchResults = [];
                    
                    // Filament 사이드바 z-index 복원
                    const sidebar = document.querySelector('.fi-sidebar');
                    const aside = document.querySelector('aside');
                    if (sidebar) {
                        sidebar.style.removeProperty('z-index');
                    }
                    if (aside) {
                        aside.style.removeProperty('z-index');
                    }
                    
                    // body overflow 복원
                    document.body.style.overflow = '';
                },

                async performSearch() {
                    if (this.searchTimeout) {
                        clearTimeout(this.searchTimeout);
                    }

                    if (!this.searchQuery || this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    this.isSearching = true;

                    // 디바운스 처리
                    this.searchTimeout = setTimeout(async () => {
                        try {
                            console.log('[GlobalSearch] Starting API search for:', this.searchQuery);
                            
                            // API 호출
                            const response = await fetch(`/api/global-search?q=${encodeURIComponent(this.searchQuery)}&limit=20&log_event=true`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                                }
                            });

                            console.log('[GlobalSearch] Response status:', response.status);

                            if (!response.ok) {
                                const errorText = await response.text();
                                console.error('[GlobalSearch] API Error Response:', errorText);
                                throw new Error(`Search request failed: ${response.status}`);
                            }

                            const data = await response.json();
                            console.log('[GlobalSearch] API Response data:', data);
                            
                            this.searchResults = data.results || [];
                            this.selectedIndex = 0;
                            
                            // GA 이벤트
                            if (typeof gtag !== 'undefined') {
                                gtag('event', 'search', {
                                    'search_term': this.searchQuery,
                                    'results_count': this.searchResults.length
                                });
                            }
                            
                            // 로그
                            console.log('[GlobalSearch] Search completed', {
                                query: this.searchQuery,
                                results: this.searchResults.length,
                                resultsData: this.searchResults,
                                timestamp: new Date().toISOString()
                            });
                        } catch (error) {
                            console.error('[GlobalSearch] Search error:', error);
                            console.error('[GlobalSearch] Error details:', error.message);
                            
                            // 에러 시 정적 검색으로 폴백
                            console.log('[GlobalSearch] Falling back to static search');
                            const query = this.searchQuery.toLowerCase();
                            this.searchResults = this.searchIndex.filter(item => {
                                return item.title.toLowerCase().includes(query) ||
                                       item.description.toLowerCase().includes(query) ||
                                       item.type.toLowerCase().includes(query) ||
                                       item.path.toLowerCase().includes(query);
                            });
                            console.log('[GlobalSearch] Static search results:', this.searchResults);
                        } finally {
                            this.isSearching = false;
                        }
                    }, 300);
                },

                handleKeyNavigation(e) {
                    if (!this.isOpen) return;

                    switch(e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            this.selectedIndex = Math.min(this.selectedIndex + 1, this.searchResults.length - 1);
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
                            break;
                        case 'Enter':
                            e.preventDefault();
                            if (this.searchResults[this.selectedIndex]) {
                                window.location.href = this.searchResults[this.selectedIndex].url;
                            }
                            break;
                    }
                },

                getResultTypeClass(type) {
                    // 인라인 스타일로 변경하여 확실한 적용
                    const styles = {
                        '페이지': 'background-color: #dbeafe; color: #2563eb;',
                        '리소스': 'background-color: #d1fae5; color: #059669;',
                        '액션': 'background-color: #e9d5ff; color: #9333ea;',
                        '설정': 'background-color: #f3f4f6; color: #4b5563;',
                        '블로그': 'background-color: #fce7f3; color: #ec4899;',
                        '뉴스': 'background-color: #fed7aa; color: #ea580c;',
                        '루틴': 'background-color: #c7d2fe; color: #6366f1;',
                        '특징': 'background-color: #fef3c7; color: #d97706;',
                        'Hero 슬라이더': 'background-color: #a5f3fc; color: #0891b2;',
                        '탭': 'background-color: #e0e7ff; color: #4f46e5;',
                        '상품': 'background-color: #f0fdf4; color: #16a34a;',
                        '식품': 'background-color: #fdf2f8; color: #db2777;',
                        '서비스': 'background-color: #f0f9ff; color: #0284c7;',
                        '홍보': 'background-color: #f5f3ff; color: #7c3aed;',
                        '영상': 'background-color: #fef2f2; color: #dc2626;',
                        'paper': 'background-color: #f9fafb; color: #1f2937;'
                    };
                    return styles[type] || 'background-color: #f3f4f6; color: #4b5563;';
                },

                getResultTypeTagClass(type) {
                    // 인라인 스타일로 변경하여 확실한 적용
                    const styles = {
                        '페이지': 'background-color: #eff6ff; color: #1e40af;',
                        '리소스': 'background-color: #ecfdf5; color: #047857;',
                        '액션': 'background-color: #f3e8ff; color: #7c3aed;',
                        '설정': 'background-color: #f9fafb; color: #374151;',
                        '블로그': 'background-color: #fdf2f8; color: #be185d;',
                        '뉴스': 'background-color: #fff7ed; color: #c2410c;',
                        '루틴': 'background-color: #eef2ff; color: #4f46e5;',
                        '특징': 'background-color: #fef9c3; color: #a16207;',
                        'Hero 슬라이더': 'background-color: #ecfeff; color: #0e7490;',
                        '탭': 'background-color: #e0e7ff; color: #3730a3;',
                        '상품': 'background-color: #f0fdf4; color: #15803d;',
                        '식품': 'background-color: #fce7f3; color: #a21caf;',
                        '서비스': 'background-color: #f0f9ff; color: #0369a1;',
                        '홍보': 'background-color: #ede9fe; color: #6d28d9;',
                        '영상': 'background-color: #fee2e2; color: #b91c1c;',
                        'paper': 'background-color: #f3f4f6; color: #111827;'
                    };
                    return styles[type] || 'background-color: #f9fafb; color: #374151;';
                },

                getResultIcon(type) {
                    const icons = {
                        '페이지': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                        '리소스': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>',
                        '액션': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                        '설정': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'
                    };
                    return icons[type] || icons['페이지'];
                },

                highlightText(text, query) {
                    if (!text || !query || query.length < 2) {
                        return text;
                    }
                    
                    // HTML 태그 제거
                    const plainText = text.replace(/<[^>]*>/g, '');
                    
                    // 대소문자 구분 없이 검색어 찾기
                    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    
                    // 검색어를 형광펜 스타일로 감싸기
                    return plainText.replace(regex, '<mark style="background-color: #fef08a; color: #713f12; padding: 0 2px; border-radius: 2px; font-weight: 600;">$1</mark>');
                },

            }
        }
    </script>
</div>