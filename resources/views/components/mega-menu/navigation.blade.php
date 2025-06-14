@php
    // project_options에서 헤더 메뉴 가져오기
    $headerMenuItems = get_option('header_menu', []);
    
    // 메뉴 객체 형태로 변환
    $menuData = new \stdClass();
    $menuData->items = collect($headerMenuItems)->map(function ($item) {
        $menuItem = new \stdClass();
        $menuItem->title = $item['label'] ?? '';
        $menuItem->url = $item['url'] ?? '';
        $menuItem->full_url = $item['url'] ?? '';
        $menuItem->target = '_self';
        $menuItem->css_class = '';
        $menuItem->icon = null;
        $menuItem->is_active = true;
        $menuItem->description = '';
        
        // 메뉴 타입에 따른 처리
        if ($item['type'] === 'mega') {
            $menuItem->is_mega_menu = true;
            $menuItem->mega_menu_content = [
                'columns' => collect($item['groups'] ?? [])->map(function ($group) {
                    return [
                        'title' => $group['group_label'] ?? '',
                        'items' => collect($group['items'] ?? [])->map(function ($groupItem) {
                            return [
                                'title' => $groupItem['label'] ?? '',
                                'url' => $groupItem['url'] ?? '',
                                'description' => ''
                            ];
                        })->toArray()
                    ];
                })->toArray()
            ];
            $menuItem->activeChildren = collect([]);
        } elseif ($item['type'] === 'dropdown') {
            $menuItem->is_mega_menu = false;
            $menuItem->activeChildren = collect($item['children'] ?? [])->map(function ($child) {
                $childItem = new \stdClass();
                $childItem->title = $child['label'] ?? '';
                $childItem->url = $child['url'] ?? '';
                $childItem->full_url = $child['url'] ?? '';
                $childItem->target = '_self';
                $childItem->icon = null;
                return $childItem;
            });
        } else {
            $menuItem->is_mega_menu = false;
            $menuItem->activeChildren = collect([]);
        }
        
        $menuItem->id = uniqid();
        
        return $menuItem;
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
    }
}" 
@click.away="closeDropdowns()"
class="bg-white border-b border-gray-200 shadow-sm relative z-50">
    <!-- Primary Navigation Menu -->
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-0">
        <div class="flex justify-between h-20">
            <div class="flex items-center w-full">
                <!-- Logo -->
                <div class="shrink-0 flex items-center mr-8">
                    <a href="/" class="flex items-center">
                        <span class="ml-3 text-2xl font-bold text-gray-900">{{ get_option('site_title', 'AIMED KOREA') }}</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden lg:flex lg:items-center lg:justify-between w-full">
                    <div class="flex items-center space-x-1">
                        @foreach($menuData->items as $item)
                            @if($item->is_active)
                                <div class="relative">
                                    @if($item->activeChildren->count() > 0 || $item->is_mega_menu)
                                        <!-- Dropdown Menu Item -->
                                        <button
                                            @mouseenter="openDropdown('{{ $item->id }}')"
                                            @mouseleave="closeDropdown()"
                                            @click="openDropdown('{{ $item->id }}')"
                                            class="inline-flex items-center px-5 py-2 h-20 text-base font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:text-blue-600 transition duration-150 ease-in-out relative group"
                                            :class="{ 'text-blue-600': activeDropdown === '{{ $item->id }}' }"
                                        >
                                            @if($item->icon)
                                                <x-dynamic-component :component="$item->icon" class="w-5 h-5 mr-2" />
                                            @endif
                                            {{ $item->title }}
                                            <svg class="ml-2 h-4 w-4 transition-transform duration-200" 
                                                 :class="{ 'rotate-180': activeDropdown === '{{ $item->id }}' }"
                                                 xmlns="http://www.w3.org/2000/svg" 
                                                 viewBox="0 0 20 20" 
                                                 fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="absolute bottom-0 left-0 w-full h-1 bg-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out"
                                                  :class="{ 'scale-x-100': activeDropdown === '{{ $item->id }}' }"></span>
                                        </button>

                                        <!-- Mega Menu Dropdown -->
                                        @if($item->is_mega_menu && $item->mega_menu_content)
                                            <div
                                                x-show="activeDropdown === '{{ $item->id }}'"
                                                @mouseenter="openDropdown('{{ $item->id }}')"
                                                @mouseleave="closeDropdown()"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform translate-y-0"
                                                x-transition:leave-end="opacity-0 transform -translate-y-2"
                                                class="absolute left-0 w-screen max-w-screen-xl -ml-8"
                                                style="display: none;"
                                            >
                                                <div class="mt-0 bg-white shadow-2xl overflow-hidden">
                                                    <div class="relative">
                                                        <!-- Triangle pointer -->
                                                        <div class="absolute top-0 left-8 w-0 h-0 -mt-2" 
                                                             style="border-left: 10px solid transparent; border-right: 10px solid transparent; border-bottom: 10px solid white;">
                                                        </div>
                                                        
                                                        <div class="grid gap-0 lg:grid-cols-{{ count($item->mega_menu_content['columns'] ?? []) }} divide-x divide-gray-100">
                                                            @foreach($item->mega_menu_content['columns'] ?? [] as $column)
                                                                <div class="p-8">
                                                                    @if(!empty($column['title']))
                                                                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b-2 border-blue-100">
                                                                            {{ $column['title'] }}
                                                                        </h3>
                                                                    @endif
                                                                    <ul class="space-y-1">
                                                                        @foreach($column['items'] ?? [] as $subItem)
                                                                            <li>
                                                                                <a href="{{ $subItem['url'] }}" 
                                                                                   class="group block hover:bg-blue-50 rounded-lg p-3 transition duration-150 ease-in-out">
                                                                                    <p class="text-base font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                                                                        {{ $subItem['title'] }}
                                                                                    </p>
                                                                                    @if(!empty($subItem['description']))
                                                                                        <p class="mt-1 text-sm text-gray-500 group-hover:text-gray-700">
                                                                                            {{ $subItem['description'] }}
                                                                                        </p>
                                                                                    @endif
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @if($item->description)
                                                            <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-t border-gray-100">
                                                                <p class="text-sm text-gray-600 max-w-4xl">
                                                                    <span class="font-semibold text-blue-700">{{ $item->title }}:</span> {{ $item->description }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Regular Dropdown -->
                                            <div
                                                x-show="activeDropdown === '{{ $item->id }}'"
                                                @mouseenter="openDropdown('{{ $item->id }}')"
                                                @mouseleave="closeDropdown()"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform translate-y-0"
                                                x-transition:leave-end="opacity-0 transform -translate-y-2"
                                                class="absolute left-0 mt-0 w-64 shadow-2xl bg-white overflow-hidden"
                                                style="display: none;"
                                            >
                                                <div class="py-2">
                                                    @foreach($item->activeChildren as $child)
                                                        <a href="{{ $child->full_url }}" 
                                                           target="{{ $child->target }}"
                                                           class="block px-6 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-150">
                                                            @if($child->icon)
                                                                <x-dynamic-component :component="$child->icon" class="inline w-4 h-4 mr-2" />
                                                            @endif
                                                            {{ $child->title }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <!-- Single Link -->
                                        <a href="{{ $item->full_url }}" 
                                           target="{{ $item->target }}"
                                           class="inline-flex items-center px-5 py-2 h-20 text-base font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:text-blue-600 transition duration-150 ease-in-out relative group {{ $item->css_class }}">
                                            @if($item->icon)
                                                <x-dynamic-component :component="$item->icon" class="w-5 h-5 mr-2" />
                                            @endif
                                            {{ $item->title }}
                                            <span class="absolute bottom-0 left-0 w-full h-1 bg-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 ease-out"></span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Right side navigation -->
                    <div class="flex items-center space-x-4">
                        <!-- Search Button -->
                        <button class="p-2 text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>

                        <!-- Language Selector -->
                        <select class="text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500">
                            <option>KOR</option>
                            <option>ENG</option>
                        </select>

                        <!-- Desktop Mega Menu Button -->
                        <button @click="toggleMegaMenu()" class="p-2 text-gray-700 hover:text-gray-900 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        @auth
                            <!-- User dropdown -->
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                        <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                        </button>
                                    @else
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                                                {{ Auth::user()->name }}
                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    @endif
                                </x-slot>

                                <x-slot name="content">
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        계정 관리
                                    </div>

                                    <x-dropdown-link href="{{ route('profile.show') }}">
                                        프로필
                                    </x-dropdown-link>

                                    <div class="border-t border-gray-100"></div>

                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                            로그아웃
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @else
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">로그인</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded-lg transition-colors">회원가입</a>
                                @endif
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center lg:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Desktop Mega Menu Overlay -->
    <div x-show="megaMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.away="megaMenuOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto bg-white top-20"
         style="display: none;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Close button -->
            <div class="flex justify-end mb-6">
                <button @click="megaMenuOpen = false" class="p-2 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mega Menu Content Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($menuData->items as $item)
                    @if($item->is_active)
                        <div>
                            <!-- Main Category -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                @if($item->icon)
                                    <x-dynamic-component :component="$item->icon" class="inline w-5 h-5 mr-2" />
                                @endif
                                @if($item->activeChildren->count() > 0 || $item->is_mega_menu)
                                    {{ $item->title }}
                                @else
                                    <a href="{{ $item->full_url }}" 
                                       target="{{ $item->target }}"
                                       class="hover:text-blue-600 transition-colors"
                                       @click="megaMenuOpen = false">
                                        {{ $item->title }}
                                    </a>
                                @endif
                            </h3>
                            
                            <!-- Sub Items -->
                            @if($item->is_mega_menu && $item->mega_menu_content)
                                @foreach($item->mega_menu_content['columns'] ?? [] as $column)
                                    @if(!empty($column['title']))
                                        <div class="mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ $column['title'] }}</h4>
                                            <ul class="space-y-2">
                                                @foreach($column['items'] ?? [] as $subItem)
                                                    <li>
                                                        <a href="{{ $subItem['url'] }}" 
                                                           class="text-sm text-gray-600 hover:text-blue-600 transition-colors"
                                                           @click="megaMenuOpen = false">
                                                            {{ $subItem['title'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <ul class="space-y-2">
                                            @foreach($column['items'] ?? [] as $subItem)
                                                <li>
                                                    <a href="{{ $subItem['url'] }}" 
                                                       class="text-sm text-gray-600 hover:text-blue-600 transition-colors"
                                                       @click="megaMenuOpen = false">
                                                        {{ $subItem['title'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                @endforeach
                            @elseif($item->activeChildren->count() > 0)
                                <ul class="space-y-2">
                                    @foreach($item->activeChildren as $child)
                                        <li>
                                            <a href="{{ $child->full_url }}" 
                                               target="{{ $child->target }}"
                                               class="text-sm text-gray-600 hover:text-blue-600 transition-colors"
                                               @click="megaMenuOpen = false">
                                                @if($child->icon)
                                                    <x-dynamic-component :component="$child->icon" class="inline w-4 h-4 mr-2" />
                                                @endif
                                                {{ $child->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            
                            @if($item->description)
                                <p class="mt-3 text-xs text-gray-500">{{ $item->description }}</p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': !open}" class="lg:hidden">
        <div class="pt-2 pb-3 space-y-1 bg-gray-50">
            @foreach($menuData->items as $item)
                @if($item->is_active)
                    @if($item->activeChildren->count() > 0)
                        <div x-data="{ mobileOpen: false }">
                            <button @click="mobileOpen = !mobileOpen" class="w-full text-left flex items-center justify-between px-4 py-3 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                <span class="flex items-center">
                                    @if($item->icon)
                                        <x-dynamic-component :component="$item->icon" class="w-5 h-5 mr-2" />
                                    @endif
                                    {{ $item->title }}
                                </span>
                                <svg class="h-5 w-5 transition-transform duration-200" :class="{ 'rotate-180': mobileOpen }" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="mobileOpen" x-transition class="bg-white">
                                @foreach($item->activeChildren as $child)
                                    <a href="{{ $child->full_url }}" target="{{ $child->target }}" class="block pl-12 pr-4 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                                        {{ $child->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->full_url }}" target="{{ $item->target }}" class="block px-4 py-3 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                            @if($item->icon)
                                <x-dynamic-component :component="$item->icon" class="inline w-5 h-5 mr-2" />
                            @endif
                            {{ $item->title }}
                        </a>
                    @endif
                @endif
            @endforeach
        </div>

        @auth
            <div class="pt-4 pb-3 border-t border-gray-200 bg-white">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 mr-3">
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        </div>
                    @endif
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                        프로필
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            로그아웃
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-3 border-t border-gray-200 bg-white">
                <div class="space-y-1">
                    <x-responsive-nav-link href="{{ route('login') }}">
                        로그인
                    </x-responsive-nav-link>
                    @if (Route::has('register'))
                        <x-responsive-nav-link href="{{ route('register') }}">
                            회원가입
                        </x-responsive-nav-link>
                    @endif
                </div>
            </div>
        @endauth
    </div>
</nav>
