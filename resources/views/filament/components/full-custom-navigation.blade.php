@php
    use Filament\Facades\Filament;
    
    $navigation = Filament::getNavigation();
    $groupedNavigation = [];
    
    // 네비게이션 구조 확인 및 그룹별로 정리
    foreach ($navigation as $element) {
        if ($element instanceof \Filament\Navigation\NavigationGroup) {
            $groupName = $element->getLabel();
            $items = $element->getItems();
            
            if (!isset($groupedNavigation[$groupName])) {
                $groupedNavigation[$groupName] = [];
            }
            
            foreach ($items as $item) {
                if ($item instanceof \Filament\Navigation\NavigationItem) {
                    $groupedNavigation[$groupName][] = $item;
                }
            }
        } elseif ($element instanceof \Filament\Navigation\NavigationItem) {
            // 그룹이 없는 단독 아이템
            $groupName = '기타';
            if (!isset($groupedNavigation[$groupName])) {
                $groupedNavigation[$groupName] = [];
            }
            $groupedNavigation[$groupName][] = $element;
        }
    }
    
    // 그룹 순서 정의
    $groupOrder = [
        '대시보드',
        '홈 구성',
        '콘텐츠',
        '리서치 허브',
        '루틴',
        '파트너',
        '설문',
        '미디어',
        '마케팅',
        '사이트',
        '설정',
    ];
@endphp

<div x-data="customFullNavigation()" class="custom-full-navigation h-full">
    {{-- 메인 네비게이션 (1차 뎁스) --}}
    <nav class="fi-sidebar-nav flex h-full">
        <div class="main-nav-container flex-1 overflow-y-auto py-1">
            <ul class="fi-sidebar-nav-groups">
                {{-- 대시보드 링크 --}}
                <li class="fi-sidebar-group mt-0">
                    <div class="fi-sidebar-group-items">
                        <ul>
                            <li class="fi-sidebar-item">
                                <a 
                                    href="{{ filament()->getHomeUrl() ?? filament()->getUrl() }}"
                                    class="fi-sidebar-item-button flex items-center gap-x-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('filament.*.pages.dashboard') ? 'bg-gray-50 dark:bg-white/5 text-primary-600' : 'hover:bg-gray-50 dark:hover:bg-white/5' }}"
                                >
                                    <span class="fi-sidebar-item-number flex h-6 w-6 items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        1
                                    </span>
                                    <span class="fi-sidebar-item-label flex-1 text-sm font-medium">
                                        대시보드
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- 그룹별 네비게이션 --}}
                @php
                    $groupIndex = 2;
                @endphp
                @foreach ($groupOrder as $groupName)
                    @if (isset($groupedNavigation[$groupName]) && count($groupedNavigation[$groupName]) > 0)
                        <li class="fi-sidebar-group mt-2">
                            <div class="fi-sidebar-group-button-container">
                                <button 
                                    type="button"
                                    @click="toggleGroup('{{ $groupName }}')"
                                    class="fi-sidebar-group-button flex items-center gap-x-3 px-3 py-2 w-full rounded-lg transition"
                                    :class="{ 
                                        'bg-gray-50 dark:bg-white/5 text-primary-600': activeGroup === '{{ $groupName }}',
                                        'hover:bg-gray-50 dark:hover:bg-white/5': activeGroup !== '{{ $groupName }}'
                                    }"
                                >
                                    <span class="fi-sidebar-item-number flex h-6 w-6 items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        {{ $groupIndex++ }}
                                    </span>
                                    <span class="fi-sidebar-group-label flex-1 text-sm font-medium text-left">
                                        {{ $groupName }}
                                    </span>
                                </button>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        {{-- 서브메뉴 패널 (2차 뎁스) --}}
        <div 
            class="submenu-panel"
            x-show="activeGroup"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform -translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform -translate-x-full"
            @click.away="handleClickAway($event)"
            style="display: none;"
            x-init="$el.style.display = ''"
        >
            <div class="submenu-header px-3 py-3 mb-3">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center">
                    <span x-text="activeGroup"></span>
                    <svg class="h-3 w-3 ml-1 text-gray-400" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </h3>
            </div>
            
            <div class="px-2">
                <ul class="space-y-1">
                    <template x-for="(item, index) in activeGroupItems" :key="item.url">
                        <li class="fi-sidebar-item">
                            <a 
                                :href="item.url"
                                class="fi-sidebar-item-button relative flex items-center gap-x-3 px-3 py-2 text-sm font-medium rounded-lg transition duration-75"
                                :class="{
                                    'bg-gray-50 dark:bg-white/5 text-primary-600 dark:text-primary-400': item.isActive,
                                    'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5': !item.isActive
                                }"
                            >
                                {{-- 활성 상태 인디케이터 --}}
                                <div 
                                    x-show="item.isActive"
                                    class="absolute inset-y-2 left-0.5 w-1 rounded-full bg-primary-600 dark:bg-primary-400"
                                ></div>
                                
                                {{-- 숫자 표시 --}}
                                <span class="fi-sidebar-item-number flex h-6 w-6 items-center justify-center text-xs font-semibold text-gray-500 dark:text-gray-400"
                                      :class="{ 'text-primary-600 dark:text-primary-400': item.isActive }">
                                    <span x-text="item.groupNumber + '.' + (index + 1)"></span>
                                </span>
                                
                                {{-- 라벨 --}}
                                <span class="flex-1 truncate" x-text="item.label"></span>
                                
                                {{-- 배지 --}}
                                <span 
                                    x-show="item.badge"
                                    class="fi-sidebar-item-badge ml-auto inline-flex items-center justify-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium tabular-nums text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                                    x-text="item.badge"
                                ></span>
                            </a>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </nav>
</div>

<style>
    .custom-full-navigation {
        width: 100%;
        height: 100%;
        overflow: visible !important;
    }
    
    .custom-full-navigation .fi-sidebar-nav {
        width: 100%;
        position: relative;
        overflow: visible !important;
    }
    
    .main-nav-container {
        padding-left: 0.25rem;
        padding-right: 1rem;
    }
    
    /* 사이드바 접기 버튼 숨기기 */
    .fi-sidebar-toggle {
        display: none !important;
    }
    
    button[x-on\:click="$store.sidebar.toggleCollapsedState()"] {
        display: none !important;
    }
    
    /* 네비게이션이 헤더 아래에 위치하도록 조정 */
    .custom-full-navigation {
        margin-top: 0;
    }
    
    /* 부모 요소들의 overflow 설정 */
    .fi-sidebar {
        overflow: visible !important;
        width: 220px !important;
        min-width: 220px !important;
        max-width: 220px !important;
    }
    
    .fi-sidebar-nav {
        overflow: visible !important;
        padding: 0 !important;
        padding-top: 5px !important;
    }
    
    .submenu-panel {
        position: fixed;
        left: 220px; /* 1차 사이드바 너비와 동일 */
        top: 65px;
        bottom: 0;
        width: 220px;
        background: rgba(255, 255, 255, 0.1);
        border-left: 1px solid rgba(55, 65, 81, 0.1);
        overflow-y: auto;
        z-index: 40;
        border-right: 1px solid rgba(55, 65, 81, 0.1);
        padding-top: 10px;
    }
    
    .dark .submenu-panel {
        background: rgba(17, 24, 39, 0.1);
        border-left: 1px solid rgba(55, 65, 81, 0.1);
        border-right: 1px solid rgba(55, 65, 81, 0.1);
    }
    
    /* 메인 컨텐츠 영역 조정 제거 - 밀려나지 않음 */
    /* body.submenu-open .fi-main {
        margin-inline-start: calc(var(--fi-sidebar-width, 270px) + 200px) !important;
        transition: margin-inline-start 0.3s ease;
    } */
    
    @media (max-width: 1024px) {
        .submenu-panel {
            display: none !important;
        }
    }
</style>

@php
    $groupItemsData = [];
    foreach ($groupOrder as $groupName) {
        if (isset($groupedNavigation[$groupName])) {
            $groupItemsData[$groupName] = [];
            foreach ($groupedNavigation[$groupName] as $item) {
                $groupItemsData[$groupName][] = [
                    'label' => $item->getLabel(),
                    'url' => $item->getUrl(),
                    'icon' => $item->getIcon(),
                    'badge' => $item->getBadge(),
                    'isActive' => $item->isActive(),
                ];
            }
        } else {
            $groupItemsData[$groupName] = [];
        }
    }
@endphp

<script>
function customFullNavigation() {
    return {
        activeGroup: null,
        activeGroupItems: [],
        activeGroupNumber: 0,
        groupItems: {!! json_encode($groupItemsData) !!},
        groupNumbers: {
            '대시보드': 1,
            '홈 구성': 2,
            '콘텐츠': 3,
            '리서치 허브': 4,
            '루틴': 5,
            '파트너': 6,
            '설문': 7,
            '미디어': 8,
            '마케팅': 9,
            '사이트': 10,
            '설정': 11
        },
        
        init() {
            // 현재 활성 그룹 자동 열기
            this.detectCurrentGroup();
        },
        
        toggleGroup(groupName) {
            if (this.activeGroup === groupName) {
                // 같은 그룹 클릭 시에만 닫기
                this.closeSubmenu();
            } else {
                // 다른 그룹 클릭 시 바로 전환
                this.activeGroup = groupName;
                this.activeGroupNumber = this.groupNumbers[groupName] || 0;
                this.activeGroupItems = (this.groupItems[groupName] || []).map(item => ({
                    ...item,
                    groupNumber: this.activeGroupNumber
                }));
                // document.body.classList.add('submenu-open'); // 제거 - 본문 밀기 비활성화
            }
        },
        
        closeSubmenu() {
            this.activeGroup = null;
            this.activeGroupItems = [];
            this.activeGroupNumber = 0;
            // document.body.classList.remove('submenu-open'); // 제거 - 본문 밀기 비활성화
        },
        
        handleClickAway(event) {
            // 1차 사이드바 그룹 버튼 클릭이 아닌 경우에만 닫기
            const isGroupButton = event.target.closest('.fi-sidebar-group-button');
            if (!isGroupButton) {
                this.closeSubmenu();
            }
        },
        
        detectCurrentGroup() {
            // 현재 URL과 매칭되는 그룹 찾기
            Object.entries(this.groupItems).forEach(([groupName, items]) => {
                if (items && items.length > 0 && items.some(item => item.isActive)) {
                    setTimeout(() => {
                        this.toggleGroup(groupName);
                    }, 100);
                    return;
                }
            });
        }
    }
}
</script>