<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 모든 뷰에서 메뉴 사용 가능하도록 설정
        View::composer('*', function ($view) {
            $mainMenu = Cache::remember('main-menu', 60 * 60, function () {
                return Menu::getBySlug('main-menu');
            });
            
            $view->with('mainMenu', $mainMenu);
        });
    }
}
