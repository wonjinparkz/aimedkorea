<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class AddNavPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 추가 권한들
        $additionalPermissions = [
            // 메뉴 관리
            ['slug' => 'menus-view', 'name' => '메뉴 보기', 'display_name_ko' => '메뉴 보기', 'display_name_en' => 'View Menus', 'module' => 'menus', 'action' => 'view'],
            ['slug' => 'menus-create', 'name' => '메뉴 생성', 'display_name_ko' => '메뉴 생성', 'display_name_en' => 'Create Menus', 'module' => 'menus', 'action' => 'create'],
            ['slug' => 'menus-edit', 'name' => '메뉴 수정', 'display_name_ko' => '메뉴 수정', 'display_name_en' => 'Edit Menus', 'module' => 'menus', 'action' => 'edit'],
            ['slug' => 'menus-delete', 'name' => '메뉴 삭제', 'display_name_ko' => '메뉴 삭제', 'display_name_en' => 'Delete Menus', 'module' => 'menus', 'action' => 'delete'],
            
            // Footer 메뉴
            ['slug' => 'footer_menus-view', 'name' => 'Footer 메뉴 보기', 'display_name_ko' => 'Footer 메뉴 보기', 'display_name_en' => 'View Footer Menus', 'module' => 'footer_menus', 'action' => 'view'],
            ['slug' => 'footer_menus-create', 'name' => 'Footer 메뉴 생성', 'display_name_ko' => 'Footer 메뉴 생성', 'display_name_en' => 'Create Footer Menus', 'module' => 'footer_menus', 'action' => 'create'],
            ['slug' => 'footer_menus-edit', 'name' => 'Footer 메뉴 수정', 'display_name_ko' => 'Footer 메뉴 수정', 'display_name_en' => 'Edit Footer Menus', 'module' => 'footer_menus', 'action' => 'edit'],
            ['slug' => 'footer_menus-delete', 'name' => 'Footer 메뉴 삭제', 'display_name_ko' => 'Footer 메뉴 삭제', 'display_name_en' => 'Delete Footer Menus', 'module' => 'footer_menus', 'action' => 'delete'],
            
            // 파트너사
            ['slug' => 'partners-view', 'name' => '파트너사 보기', 'display_name_ko' => '파트너사 보기', 'display_name_en' => 'View Partners', 'module' => 'partners', 'action' => 'view'],
            ['slug' => 'partners-create', 'name' => '파트너사 생성', 'display_name_ko' => '파트너사 생성', 'display_name_en' => 'Create Partners', 'module' => 'partners', 'action' => 'create'],
            ['slug' => 'partners-edit', 'name' => '파트너사 수정', 'display_name_ko' => '파트너사 수정', 'display_name_en' => 'Edit Partners', 'module' => 'partners', 'action' => 'edit'],
            ['slug' => 'partners-delete', 'name' => '파트너사 삭제', 'display_name_ko' => '파트너사 삭제', 'display_name_en' => 'Delete Partners', 'module' => 'partners', 'action' => 'delete'],
            
            // 권한 관리
            ['slug' => 'permissions-view', 'name' => '권한 보기', 'display_name_ko' => '권한 보기', 'display_name_en' => 'View Permissions', 'module' => 'permissions', 'action' => 'view'],
            ['slug' => 'permissions-create', 'name' => '권한 생성', 'display_name_ko' => '권한 생성', 'display_name_en' => 'Create Permissions', 'module' => 'permissions', 'action' => 'create'],
            ['slug' => 'permissions-edit', 'name' => '권한 수정', 'display_name_ko' => '권한 수정', 'display_name_en' => 'Edit Permissions', 'module' => 'permissions', 'action' => 'edit'],
            ['slug' => 'permissions-delete', 'name' => '권한 삭제', 'display_name_ko' => '권한 삭제', 'display_name_en' => 'Delete Permissions', 'module' => 'permissions', 'action' => 'delete'],
            
            // 일반 설정 권한
            ['slug' => 'settings-view', 'name' => '설정 보기', 'display_name_ko' => '설정 보기', 'display_name_en' => 'View Settings', 'module' => 'settings', 'action' => 'view'],
            
            // 리서치 관련
            ['slug' => 'research-view', 'name' => '리서치 보기', 'display_name_ko' => '리서치 보기', 'display_name_en' => 'View Research', 'module' => 'research', 'action' => 'view'],
            
            // 페이지 관리 통합
            ['slug' => 'pages-view', 'name' => '페이지 보기', 'display_name_ko' => '페이지 보기', 'display_name_en' => 'View Pages', 'module' => 'pages', 'action' => 'view'],
        ];
        
        foreach ($additionalPermissions as $permissionData) {
            Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }
        
        // 관리자에게 모든 새 권한 부여
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $newPermissions = Permission::whereIn('slug', array_column($additionalPermissions, 'slug'))->get();
            foreach ($newPermissions as $permission) {
                if (!$adminRole->permissions()->where('slug', $permission->slug)->exists()) {
                    $adminRole->permissions()->attach($permission->id);
                }
            }
        }
        
        $this->command->info('네비게이션 권한들이 추가되었습니다.');
    }
}