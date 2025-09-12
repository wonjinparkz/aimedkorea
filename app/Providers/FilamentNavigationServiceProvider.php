<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use App\Helpers\PermissionHelper;

class FilamentNavigationServiceProvider extends ServiceProvider
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
        // 네비게이션 그룹별 권한 매핑
        $this->configureNavigationGroups();
    }
    
    /**
     * 네비게이션 그룹별 권한 설정
     */
    protected function configureNavigationGroups(): void
    {
        // 네비게이션 그룹별 필요 권한 정의
        $navigationPermissions = [
            '홈 구성' => 'heroes-view',
            '콘텐츠' => 'posts-view',
            '리서치 허브' => 'research-view',
            '참여형 콘텐츠' => 'surveys-view',
            '정보 관리' => 'pages-view',
            '사용자 관리' => 'users-view',
            '설정' => 'settings-view',
        ];
        
        // Filament Admin 패널 후킹
        Filament::serving(function () use ($navigationPermissions) {
            // 각 네비게이션 그룹의 가시성을 권한에 따라 제어
            foreach ($navigationPermissions as $groupName => $requiredPermission) {
                if (!PermissionHelper::hasPermission($requiredPermission) && !PermissionHelper::isAdmin()) {
                    $this->hideNavigationGroup($groupName);
                }
            }
        });
    }
    
    /**
     * 특정 네비게이션 그룹 숨기기
     */
    protected function hideNavigationGroup(string $groupName): void
    {
        // 현재 Filament에서는 직접적인 그룹 숨기기가 제한적이므로
        // 각 리소스의 shouldRegisterNavigation을 오버라이드하여 처리
        // 이는 각 리소스에서 개별적으로 처리해야 함
    }
    
    /**
     * 네비게이션 그룹의 모든 리소스가 권한이 없는지 확인
     */
    public static function shouldHideGroup(string $groupName): bool
    {
        $navigationPermissions = [
            '홈 구성' => ['heroes-view'],
            '콘텐츠' => ['posts-view', 'blog_posts-view', 'news_posts-view', 'routine_posts-view', 'featured_posts-view', 'product_posts-view', 'food_posts-view', 'service_posts-view', 'promotion_posts-view'],
            '리서치 허브' => ['research-view', 'papers-view', 'tab_posts-view'],
            '참여형 콘텐츠' => ['surveys-view'],
            '정보 관리' => ['pages-view', 'qna_posts-view', 'video_posts-view', 'custom_pages-view'],
            '사용자 관리' => ['users-view'],
            '설정' => ['settings-view', 'roles-view', 'users-view'],
        ];
        
        $permissions = $navigationPermissions[$groupName] ?? [];
        
        // 관리자는 모든 그룹 볼 수 있음
        if (PermissionHelper::isAdmin()) {
            return false;
        }
        
        // 그룹 내 권한 중 하나라도 있으면 그룹 표시
        foreach ($permissions as $permission) {
            if (PermissionHelper::hasPermission($permission)) {
                return false;
            }
        }
        
        return true; // 모든 권한이 없으면 그룹 숨김
    }
}