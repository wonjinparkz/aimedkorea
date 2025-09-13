@php
    // project_options에서 헤더 메뉴 가져오기
    $headerMenuItems = get_option('header_menu', []);
    
    // 현재 언어 설정 가져오기 (기본값: kor)
    $currentLang = session('locale', 'kor');
    
    // 언어별 필드 매핑
    $langFieldMap = [
        'kor' => 'label',
        'eng' => 'label_eng',
        'chn' => 'label_chn',
        'hin' => 'label_hin',
        'arb' => 'label_arb',
    ];
    
    $langField = $langFieldMap[$currentLang] ?? 'label';
    
    // 메뉴 객체 형태로 변환
    $menuData = new \stdClass();
    $menuData->items = collect($headerMenuItems)->map(function ($item, $index) use ($langField) {
        $menuItem = new \stdClass();
        // 현재 언어의 라벨 사용, 없으면 기본 라벨 사용
        $menuItem->title = $item[$langField] ?? $item['label'] ?? '';
        $menuItem->url = $item['url'] ?? '';
        $menuItem->full_url = $item['url'] ?? '';
        $menuItem->target = '_self';
        $menuItem->css_class = '';
        $menuItem->icon = null;
        $menuItem->is_active = $item['active'] ?? true;
        $menuItem->description = '';
        $menuItem->position = $index + 1; // GTM용 위치 추가
        $menuItem->type = $item['type'] ?? 'simple'; // GTM용 타입 추가
        
        // 메뉴 타입에 따른 처리
        if ($item['type'] === 'mega') {
            $menuItem->is_mega_menu = true;
            $menuItem->mega_menu_content = [
                'columns' => collect($item['groups'] ?? [])->map(function ($group, $groupIndex) use ($langField) {
                    // 그룹 라벨도 현재 언어에 맞게 설정
                    $groupLangField = str_replace('label', 'group_label', $langField);
                    $groupTitle = $group[$groupLangField] ?? $group['group_label'] ?? $group['label'] ?? '';
                    
                    return [
                        'title' => $groupTitle,
                        'position' => $groupIndex + 1,
                        'items' => collect($group['items'] ?? [])->map(function ($groupItem, $itemIndex) use ($langField) {
                            return [
                                'title' => $groupItem[$langField] ?? $groupItem['label'] ?? '',
                                'url' => $groupItem['url'] ?? '',
                                'description' => '',
                                'position' => $itemIndex + 1
                            ];
                        })->toArray()
                    ];
                })->toArray()
            ];
            $menuItem->activeChildren = collect([]);
        } elseif ($item['type'] === 'dropdown' || !empty($item['children'])) {
            $menuItem->is_mega_menu = false;
            $menuItem->activeChildren = collect($item['children'] ?? [])->map(function ($child, $childIndex) use ($langField) {
                $childItem = new \stdClass();
                // 하위 메뉴도 현재 언어의 라벨 사용
                $childItem->title = $child[$langField] ?? $child['label'] ?? '';
                $childItem->url = $child['url'] ?? '';
                $childItem->full_url = $child['url'] ?? '';
                $childItem->target = '_self';
                $childItem->icon = null;
                $childItem->position = $childIndex + 1; // GTM용 위치 추가
                return $childItem;
            });
        } else {
            $menuItem->is_mega_menu = false;
            $menuItem->activeChildren = collect([]);
        }
        
        $menuItem->id = uniqid();
        
        return $menuItem;
    })->filter(function ($item) {
        // 활성화된 메뉴만 표시
        return $item->is_active;
    });
@endphp

<nav x-data="{ 
    open: false,
    megaMenuOpen: false,
    activeDropdown: null,
    hoverTimeout: null,
    openDropdown(id) {
        clearTimeout(this.hoverTimeout);
        this.activeDropdown = id;
    },
    closeDropdown() {
        this.hoverTimeout = setTimeout(() => {
            this.activeDropdown = null;
        }, 300);
    },
    closeDropdowns() {
        clearTimeout(this.hoverTimeout);
        this.activeDropdown = null;
    },
    toggleMegaMenu() {
        this.megaMenuOpen = !this.megaMenuOpen;
    },
    trackMenuClick(menuType, category, label, position, url) {
        // GTM 데이터 레이어에 이벤트 푸시
        if (window.dataLayer) {
            window.dataLayer.push({
                'event': 'menu_click',
                'menuType': menuType,
                'menuCategory': category,
                'menuLabel': label,
                'menuPosition': position,
                'menuURL': url,
                'pageURL': window.location.href,
                'timestamp': new Date().toISOString()
            });
        }
    }
}" 
@click.away="closeDropdowns()"
class="bg-white shadow-sm sticky top-0 z-40">
    
    {{-- Mobile menu button --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- Logo --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-2xl font-bold text-gray-900">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                {{-- Desktop Navigation --}}
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    @foreach ($menuData->items as $item)
                        @if ($item->is_mega_menu)
                            {{-- Mega Menu Item --}}
                            <div class="relative"
                                 @mouseenter="openDropdown('{{ $item->id }}')"
                                 @mouseleave="closeDropdown()">
                                <a href="{{ $item->url }}"
                                   {{-- GTM 추적 속성 추가 --}}
                                   data-gtm-menu-type="mega"
                                   data-gtm-menu-category="{{ $item->title }}"
                                   data-gtm-menu-label="{{ $item->title }}"
                                   data-gtm-menu-position="{{ $item->position }}"
                                   @click="trackMenuClick('mega', '{{ $item->title }}', '{{ $item->title }}', {{ $item->position }}, '{{ $item->url }}')"
                                   class="gtm-menu-item inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-gray-900 hover:border-gray-300"
                                   :class="{ 'border-b-2 border-indigo-500 text-gray-900': activeDropdown === '{{ $item->id }}' }">
                                    {{ $item->title }}
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </a>

                                {{-- Mega Menu Content --}}
                                <div x-show="activeDropdown === '{{ $item->id }}'"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-1"
                                     @mouseenter="openDropdown('{{ $item->id }}')"
                                     @mouseleave="closeDropdown()"
                                     class="absolute left-0 w-screen max-w-7xl -ml-8 mt-1 bg-white shadow-lg rounded-lg"
                                     style="display: none;">
                                    <div class="px-8 py-6">
                                        <div class="grid grid-cols-{{ count($item->mega_menu_content['columns']) }} gap-8">
                                            @foreach ($item->mega_menu_content['columns'] as $column)
                                                <div>
                                                    <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ $column['title'] }}</h3>
                                                    <ul class="space-y-2">
                                                        @foreach ($column['items'] as $subItem)
                                                            <li>
                                                                <a href="{{ $subItem['url'] }}"
                                                                   {{-- GTM 추적 속성 추가 (서브메뉴) --}}
                                                                   data-gtm-menu-type="mega-submenu"
                                                                   data-gtm-menu-category="{{ $item->title }}"
                                                                   data-gtm-menu-label="{{ $subItem['title'] }}"
                                                                   data-gtm-menu-position="{{ $column['position'] }}-{{ $subItem['position'] }}"
                                                                   data-gtm-menu-parent="{{ $column['title'] }}"
                                                                   @click="trackMenuClick('mega-submenu', '{{ $item->title }}', '{{ $subItem['title'] }}', '{{ $column['position'] }}-{{ $subItem['position'] }}', '{{ $subItem['url'] }}')"
                                                                   class="gtm-menu-item text-sm text-gray-600 hover:text-gray-900">
                                                                    {{ $subItem['title'] }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($item->activeChildren->count() > 0)
                            {{-- Dropdown Menu Item --}}
                            <div class="relative"
                                 @mouseenter="openDropdown('{{ $item->id }}')"
                                 @mouseleave="closeDropdown()">
                                <a href="{{ $item->url }}"
                                   {{-- GTM 추적 속성 추가 --}}
                                   data-gtm-menu-type="dropdown"
                                   data-gtm-menu-category="{{ $item->title }}"
                                   data-gtm-menu-label="{{ $item->title }}"
                                   data-gtm-menu-position="{{ $item->position }}"
                                   @click="trackMenuClick('dropdown', '{{ $item->title }}', '{{ $item->title }}', {{ $item->position }}, '{{ $item->url }}')"
                                   class="gtm-menu-item inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-gray-900 hover:border-gray-300"
                                   :class="{ 'border-b-2 border-indigo-500 text-gray-900': activeDropdown === '{{ $item->id }}' }">
                                    {{ $item->title }}
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </a>

                                {{-- Dropdown Content --}}
                                <div x-show="activeDropdown === '{{ $item->id }}'"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-1"
                                     @mouseenter="openDropdown('{{ $item->id }}')"
                                     @mouseleave="closeDropdown()"
                                     class="absolute left-0 mt-1 w-56 bg-white shadow-lg rounded-lg"
                                     style="display: none;">
                                    <div class="py-1">
                                        @foreach ($item->activeChildren as $child)
                                            <a href="{{ $child->url }}"
                                               {{-- GTM 추적 속성 추가 (드롭다운 서브메뉴) --}}
                                               data-gtm-menu-type="dropdown-submenu"
                                               data-gtm-menu-category="{{ $item->title }}"
                                               data-gtm-menu-label="{{ $child->title }}"
                                               data-gtm-menu-position="{{ $item->position }}-{{ $child->position }}"
                                               data-gtm-menu-parent="{{ $item->title }}"
                                               @click="trackMenuClick('dropdown-submenu', '{{ $item->title }}', '{{ $child->title }}', '{{ $item->position }}-{{ $child->position }}', '{{ $child->url }}')"
                                               class="gtm-menu-item block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                {{ $child->title }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Simple Menu Item --}}
                            <a href="{{ $item->url }}"
                               {{-- GTM 추적 속성 추가 --}}
                               data-gtm-menu-type="simple"
                               data-gtm-menu-category="{{ $item->title }}"
                               data-gtm-menu-label="{{ $item->title }}"
                               data-gtm-menu-position="{{ $item->position }}"
                               @click="trackMenuClick('simple', '{{ $item->title }}', '{{ $item->title }}', {{ $item->position }}, '{{ $item->url }}')"
                               class="gtm-menu-item inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 hover:text-gray-900 hover:border-gray-300">
                                {{ $item->title }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Mobile menu button --}}
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Navigation --}}
    <div x-show="open" x-transition class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($menuData->items as $item)
                <a href="{{ $item->url }}"
                   {{-- GTM 추적 속성 추가 (모바일) --}}
                   data-gtm-menu-type="mobile"
                   data-gtm-menu-category="{{ $item->title }}"
                   data-gtm-menu-label="{{ $item->title }}"
                   data-gtm-menu-position="{{ $item->position }}"
                   @click="trackMenuClick('mobile', '{{ $item->title }}', '{{ $item->title }}', {{ $item->position }}, '{{ $item->url }}')"
                   class="gtm-menu-item block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                    {{ $item->title }}
                </a>
                
                @if ($item->activeChildren->count() > 0)
                    <div class="pl-6">
                        @foreach ($item->activeChildren as $child)
                            <a href="{{ $child->url }}"
                               {{-- GTM 추적 속성 추가 (모바일 서브메뉴) --}}
                               data-gtm-menu-type="mobile-submenu"
                               data-gtm-menu-category="{{ $item->title }}"
                               data-gtm-menu-label="{{ $child->title }}"
                               data-gtm-menu-position="{{ $item->position }}-{{ $child->position }}"
                               data-gtm-menu-parent="{{ $item->title }}"
                               @click="trackMenuClick('mobile-submenu', '{{ $item->title }}', '{{ $child->title }}', '{{ $item->position }}-{{ $child->position }}', '{{ $child->url }}')"
                               class="gtm-menu-item block pl-3 pr-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                                {{ $child->title }}
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</nav>