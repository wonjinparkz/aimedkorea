@props([
    'navigation',
])

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'hasTopbar' => true,
        'sidebarWidth' => 'custom',
    ])

    <div class="fi-layout flex min-h-screen w-full flex-row-reverse overflow-x-clip">
        <div
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-data="{}"
                x-cloak
                x-show="$store.sidebar.isOpen"
                x-transition.opacity.300ms
                x-on:click="$store.sidebar.close()"
                class="fi-sidebar-close-overlay fixed inset-0 z-20 h-full w-full bg-gray-950/50 dark:bg-gray-950/75 lg:hidden"
            @endif
        ></div>

        <main
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-data="{}"
                x-cloak
                x-bind:class="{
                    'fi-main-ctn-sidebar-open': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex; min-height: 100vh;' + ($store.sidebar.isOpen ? 'margin-inline-start: 450px;' : '')"
            @else
                style="display: flex; min-height: 100vh; margin-inline-start: 450px;"
            @endif
            @class([
                'fi-main w-full transition-all',
            ])
        >
            <div class="fi-main-ctn w-full">
                @if ($hasTopbar ?? true)
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_BEFORE) }}

                    <x-filament-panels::topbar :navigation="$navigation" />

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_AFTER) }}
                @endif

                <div class="fi-main-ctn-content flex-1 w-full">
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_START) }}

                    {{ $slot }}

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_END) }}
                </div>

                <x-filament-panels::footer />
            </div>
        </main>

        <aside
            @if (filament()->isSidebarCollapsibleOnDesktop())
                x-cloak
                x-data="{}"
                x-bind:class="{
                    'fi-sidebar-open': $store.sidebar.isOpen,
                    'fi-sidebar-closed': ! $store.sidebar.isOpen,
                }"
            @endif
            @class([
                'fi-sidebar fixed inset-y-0 start-0 z-20 flex h-screen w-[450px] flex-col bg-white transition-all dark:bg-gray-900 lg:z-0',
                'lg:translate-x-0 rtl:lg:-translate-x-0',
                '-translate-x-full rtl:translate-x-full' => filament()->isSidebarCollapsibleOnDesktop(),
            ])
        >
            <header class="fi-sidebar-header flex h-16 items-center bg-white px-6 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="flex flex-1">
                    @if ($homeUrl = filament()->getHomeUrl())
                        <a href="{{ $homeUrl }}">
                            <x-filament-panels::logo />
                        </a>
                    @else
                        <x-filament-panels::logo />
                    @endif
                </div>

                @if (filament()->isSidebarCollapsibleOnDesktop())
                    <x-filament::icon-button
                        color="gray"
                        icon="heroicon-o-x-mark"
                        icon-alias="panels::sidebar.button"
                        size="lg"
                        x-cloak
                        x-data="{}"
                        x-on:click="$store.sidebar.close()"
                        x-show="$store.sidebar.isOpen"
                        class="lg:hidden"
                    />
                @endif
            </header>

            <nav class="fi-sidebar-nav flex-1 overflow-auto">
                @include('filament.components.custom-navigation')
            </nav>

            <x-filament-panels::sidebar.footer />
        </aside>
    </div>
</x-filament-panels::layout.base>