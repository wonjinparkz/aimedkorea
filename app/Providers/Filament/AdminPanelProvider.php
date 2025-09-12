<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Helpers\PermissionHelper;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\SurveyStatsOverview::class,
                \App\Filament\Widgets\SurveyResponseChart::class,
                \App\Filament\Widgets\SurveyResponseCategoryChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups($this->getVisibleNavigationGroups())
            ->sidebarWidth('220px')
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => view('filament.components.navigation-override')
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn () => Blade::render('@include("filament.components.global-search")')
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => Blade::render('@include("filament.components.search-analytics")')
            );
    }

    /**
     * Get navigation groups that should be visible based on user permissions
     */
    private function getVisibleNavigationGroups(): array
    {
        $allGroups = [
            '대시보드' => 'section_dashboard-view',
            '홈 구성' => 'section_home-view',
            '콘텐츠' => 'section_content-view',
            '리서치 허브' => 'section_research-view',
            '루틴' => 'section_routine-view',
            '파트너' => 'section_partner-view',
            '설문' => 'section_survey-view',
            '미디어' => 'section_media-view',
            '마케팅' => 'section_marketing-view',
            '사이트' => 'section_site-view',
            '설정' => 'section_settings-view',
        ];

        $visibleGroups = [];
        
        foreach ($allGroups as $groupName => $sectionPermission) {
            // Check if user has section view permission
            if (PermissionHelper::hasPermission($sectionPermission) || PermissionHelper::isAdmin()) {
                $visibleGroups[] = $groupName;
            }
        }
        
        return $visibleGroups;
    }
}
