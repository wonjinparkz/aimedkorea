<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\PermissionHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 글로벌 헬퍼 함수 등록
        if (!function_exists('hasPermission')) {
            function hasPermission(string $permission): bool {
                return PermissionHelper::hasPermission($permission);
            }
        }
        
        if (!function_exists('hasModulePermission')) {
            function hasModulePermission(string $module, string $action = null): bool {
                return PermissionHelper::hasModulePermission($module, $action);
            }
        }
        
        if (!function_exists('requirePermission')) {
            function requirePermission(string $permission): void {
                PermissionHelper::requirePermission($permission);
            }
        }
    }
}
