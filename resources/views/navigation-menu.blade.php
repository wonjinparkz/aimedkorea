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

@if(count($headerMenuItems) > 0)
    <x-mega-menu.navigation :menu="$menuData" />
@else
    <!-- 기본 네비게이션 (메뉴가 없을 때) -->
    <nav x-data="{ open: false }" class="bg-white max-w-7xl border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            <x-application-mark class="block h-9 w-auto" />
                        </a>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </div>
                </div>
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    @auth
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm text-gray-700 underline">Register</a>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
@endif
