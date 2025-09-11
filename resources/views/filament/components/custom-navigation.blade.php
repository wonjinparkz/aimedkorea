<div 
    x-data="customNavigation()"
    class="custom-navigation-wrapper"
>
    <style>
        .custom-navigation-wrapper {
            display: flex;
            height: 100%;
            width: 100%;
        }
        
        .main-navigation {
            flex: 1;
            padding: 1rem;
            border-right: 1px solid rgba(229, 231, 235, 1);
            overflow-y: auto;
            min-width: 200px;
        }
        
        .submenu-panel {
            background: rgba(249, 250, 251, 0.5);
            overflow-y: auto;
        }
        
        .submenu-panel.open {
            width: 200px;
            padding: 1rem;
        }
        
        .submenu-panel.closed {
            width: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .nav-group {
            margin-bottom: 0.5rem;
        }
        
        .nav-group-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: rgba(55, 65, 81, 1);
            cursor: pointer;
            border-radius: 0.5rem;
            transition: all 0.2s;
            user-select: none;
        }
        
        .nav-group-label:hover {
            background: rgba(243, 244, 246, 1);
        }
        
        .nav-group-label.active {
            background: rgba(251, 191, 36, 0.1);
            color: rgba(217, 119, 6, 1);
        }
        
        .nav-group-icon {
            transition: transform 0.2s;
        }
        
        .nav-group-label.active .nav-group-icon {
            transform: rotate(90deg);
        }
        
        .nav-item {
            display: block;
            padding: 0.5rem 1rem;
            margin: 0.25rem 0;
            color: rgba(107, 114, 128, 1);
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        
        .nav-item:hover {
            background: rgba(243, 244, 246, 1);
            color: rgba(31, 41, 55, 1);
        }
        
        .nav-item.active {
            background: rgba(251, 191, 36, 0.1);
            color: rgba(217, 119, 6, 1);
            font-weight: 500;
        }
        
        .submenu-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: rgba(107, 114, 128, 1);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(229, 231, 235, 1);
        }
    </style>

    <div class="main-navigation">
        @php
            $navigation = \Filament\Facades\Filament::getNavigation();
            $groupedNavigation = [];
            $groups = [
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
            
            // 대시보드 항목 추가
            $dashboardItem = [
                'label' => '대시보드',
                'url' => \Filament\Facades\Filament::getUrl(),
                'icon' => 'heroicon-o-home',
                'isActive' => request()->routeIs('filament.admin.pages.dashboard'),
                'badge' => null,
            ];
            
            foreach ($navigation as $item) {
                $group = $item->getGroup() ?? '기타';
                if (!isset($groupedNavigation[$group])) {
                    $groupedNavigation[$group] = [];
                }
                $groupedNavigation[$group][] = $item;
            }
        @endphp

        {{-- 대시보드 (그룹 없이 단독 표시) --}}
        <div class="nav-group">
            <a 
                href="{{ $dashboardItem['url'] }}" 
                class="nav-group-label {{ $dashboardItem['isActive'] ? 'active' : '' }}"
                style="padding-left: 0.5rem;"
            >
                <span class="flex items-center gap-3">
                    <x-filament::icon 
                        :icon="$dashboardItem['icon']" 
                        class="h-5 w-5"
                    />
                    {{ $dashboardItem['label'] }}
                </span>
            </a>
        </div>

        {{-- 그룹별 네비게이션 --}}
        @foreach ($groups as $groupName)
            @if (isset($groupedNavigation[$groupName]) && count($groupedNavigation[$groupName]) > 0)
                <div class="nav-group">
                    <div 
                        class="nav-group-label"
                        :class="{ 'active': activeGroup === '{{ $groupName }}' }"
                        @click="toggleGroup('{{ $groupName }}')"
                    >
                        <span>{{ $groupName }}</span>
                        <svg class="nav-group-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- 서브메뉴 패널 --}}
    <div 
        class="submenu-panel"
        :class="activeGroup ? 'open' : 'closed'"
        x-ref="submenuPanel"
        :style="panelStyle"
    >
        <template x-if="activeGroup">
            <div>
                <div class="submenu-title" x-text="activeGroup"></div>
                @foreach ($groups as $groupName)
                    @if (isset($groupedNavigation[$groupName]))
                        <div x-show="activeGroup === '{{ $groupName }}'">
                            @foreach ($groupedNavigation[$groupName] as $item)
                                <a 
                                    href="{{ $item->getUrl() }}"
                                    class="nav-item {{ $item->isActive() ? 'active' : '' }}"
                                >
                                    <span class="flex items-center gap-2">
                                        @if ($item->getIcon())
                                            <x-filament::icon 
                                                :icon="$item->getIcon()" 
                                                class="h-4 w-4"
                                            />
                                        @endif
                                        {{ $item->getLabel() }}
                                        @if ($item->getBadge())
                                            <span class="ml-auto text-xs bg-gray-200 px-2 py-0.5 rounded-full">
                                                {{ $item->getBadge() }}
                                            </span>
                                        @endif
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
        </template>
    </div>

    <script>
        function customNavigation() {
            return {
                activeGroup: null,
                isInitialLoad: true,
                panelStyle: '',
                
                init() {
                    // 현재 페이지에 해당하는 그룹 자동 열기 (애니메이션 없이)
                    this.detectCurrentGroup();
                    
                    // 초기 로드 시 transition 없음
                    this.updatePanelStyle();
                    
                    // 100ms 후 transition 활성화
                    setTimeout(() => {
                        this.isInitialLoad = false;
                        this.updatePanelStyle();
                    }, 100);
                },
                
                updatePanelStyle() {
                    if (this.isInitialLoad) {
                        // 초기 로드: transition 없음
                        this.panelStyle = 'transition: none;';
                    } else {
                        // 이후: transition 적용
                        this.panelStyle = 'transition: width 0.3s ease, padding 0.3s ease;';
                    }
                },
                
                toggleGroup(group) {
                    // 클릭 시 transition 보장
                    if (this.isInitialLoad) {
                        this.isInitialLoad = false;
                        this.updatePanelStyle();
                    }
                    
                    if (this.activeGroup === group) {
                        this.activeGroup = null;
                    } else {
                        this.activeGroup = group;
                    }
                },
                
                detectCurrentGroup() {
                    // 현재 URL을 기반으로 활성 그룹 감지
                    const currentPath = window.location.pathname;
                    
                    @foreach ($groups as $groupName)
                        @if (isset($groupedNavigation[$groupName]))
                            @foreach ($groupedNavigation[$groupName] as $item)
                                @if ($item->isActive())
                                    this.activeGroup = '{{ $groupName }}';
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                }
            }
        }
    </script>
</div>