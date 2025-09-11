@props([
    'navigation' => null,
])

<div class="fi-topbar sticky top-0 z-20 overflow-x-clip">
    <nav class="flex h-16 items-center gap-x-4 bg-white px-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:px-6 lg:px-8">
        {{-- Sidebar Toggle --}}
        @if (filament()->isSidebarCollapsibleOnDesktop())
            <x-filament::icon-button
                color="gray"
                icon="heroicon-o-bars-3"
                icon-alias="panels::sidebar.expand"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                x-cloak
                x-data="{}"
                x-on:click="$store.sidebar.open()"
                x-show="! $store.sidebar.isOpen"
                @class([
                    'fi-sidebar-open-btn',
                ])
            />
        @endif

        {{-- Breadcrumbs --}}
        <div class="flex flex-1 items-center gap-x-4">
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE) }}

            {{-- Global Search Component --}}
            <div class="flex-shrink-0">
                @include('filament.components.global-search')
            </div>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_AFTER) }}

            {{-- Breadcrumbs (if available) --}}
            @if ($breadcrumbs = filament()->getBreadcrumbs())
                <x-filament::breadcrumbs
                    :breadcrumbs="$breadcrumbs"
                    class="hidden flex-1 md:block"
                />
            @endif
        </div>

        {{-- User Menu & Notifications --}}
        <div class="flex items-center gap-x-4">
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::USER_MENU_BEFORE) }}

            {{-- Notifications --}}
            @if (filament()->isNotificationsEnabled())
                @livewire(\Filament\Livewire\Notifications::class)
            @endif

            {{-- User Menu --}}
            <x-filament-panels::user-menu />

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::USER_MENU_AFTER) }}
        </div>
    </nav>
</div>