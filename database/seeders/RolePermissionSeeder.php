<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 기본 권한 생성
        $permissions = [];
        foreach (Permission::getDefaultPermissions() as $permissionData) {
            $permissions[$permissionData['slug']] = Permission::create($permissionData);
        }

        // 기본 역할 생성
        $roles = [];
        foreach (Role::getDefaultRoles() as $roleData) {
            $roles[$roleData['slug']] = Role::create($roleData);
        }

        // 역할별 권한 할당
        // 관리자 - 모든 권한
        $permissionIds = array_map(function($p) { return $p->id; }, $permissions);
        $roles['admin']->permissions()->sync($permissionIds);

        // 사이트 관리자 - 설정, 역할, 사용자 관리
        $siteManagerPermissions = [];
        foreach ($permissions as $slug => $permission) {
            if (in_array($permission->module, ['settings', 'roles', 'users', 'heroes', 'footer_menus'])) {
                $siteManagerPermissions[] = $permission->id;
            }
        }
        $roles['site-manager']->permissions()->sync($siteManagerPermissions);

        // 콘텐츠 관리자 - 게시물, 페이지, 설문조사, Hero 관리
        $contentManagerPermissions = [];
        foreach ($permissions as $slug => $permission) {
            if (in_array($permission->module, ['posts', 'pages', 'surveys', 'heroes'])) {
                $contentManagerPermissions[] = $permission->id;
            }
        }
        $roles['content-manager']->permissions()->sync($contentManagerPermissions);

        // 일반 사용자 - 보기 권한만
        $userPermissions = [];
        foreach ($permissions as $slug => $permission) {
            if ($permission->action === 'view' && !in_array($permission->module, ['settings', 'roles'])) {
                $userPermissions[] = $permission->id;
            }
        }
        $roles['user']->permissions()->sync($userPermissions);

        // 기존 사용자들에게 기본 역할 할당
        $users = User::all();
        foreach ($users as $user) {
            // 첫 번째 사용자를 관리자로
            if ($user->id === 1) {
                $user->roles()->attach($roles['admin']->id);
            } else {
                // 나머지는 일반 사용자로
                $user->roles()->attach($roles['user']->id);
            }
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
