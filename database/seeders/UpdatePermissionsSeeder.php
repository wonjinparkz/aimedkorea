<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class UpdatePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 기존 권한 삭제 (관리자/사용자 역할 제외)
        Permission::whereNotIn('slug', ['dashboard-view'])->delete();
        
        // 새로운 권한 추가
        $permissions = [];
        foreach (Permission::getDefaultPermissions() as $permissionData) {
            $permissions[$permissionData['slug']] = Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }
        
        // 역할별 권한 재설정
        $roles = Role::all()->keyBy('slug');
        
        // 관리자 - 모든 권한
        if (isset($roles['admin'])) {
            $allPermissions = Permission::all();
            $roles['admin']->permissions()->sync($allPermissions->pluck('id'));
        }
        
        // 사이트 관리자 - 설정, 사이트 관리
        if (isset($roles['site-manager'])) {
            $siteManagerPermissions = Permission::whereIn('module', [
                'users', 'roles', 'custom_pages', 'header_menus', 'footer_menus'
            ])->pluck('id');
            $roles['site-manager']->permissions()->sync($siteManagerPermissions);
        }
        
        // 콘텐츠 관리자 - 콘텐츠 관련 권한
        if (isset($roles['content-manager'])) {
            $contentManagerPermissions = Permission::whereIn('module', [
                'heroes', 'featured_posts', 'banner_posts',
                'blog_posts', 'food_posts', 'news_posts', 'product_posts', 'qna_posts', 'service_posts',
                'paper_posts', 'tab_posts',
                'routine_posts',
                'video_posts',
                'promotion_posts',
                'surveys', 'survey_responses'
            ])->pluck('id');
            $roles['content-manager']->permissions()->sync($contentManagerPermissions);
        }
        
        // 일반 사용자 - 보기 권한만
        if (isset($roles['user'])) {
            $userPermissions = Permission::where('action', 'view')
                ->whereNotIn('module', ['users', 'roles'])
                ->pluck('id');
            $roles['user']->permissions()->sync($userPermissions);
        }
        
        $this->command->info('Permissions updated successfully!');
        $this->command->info('Total permissions: ' . Permission::count());
    }
}
