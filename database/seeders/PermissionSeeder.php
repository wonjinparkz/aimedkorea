<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== 권한 시딩 시작 ===\n";
        
        // 기본 권한들 생성
        $defaultPermissions = Permission::getDefaultPermissions();
        
        foreach ($defaultPermissions as $permissionData) {
            $permission = Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
            echo "✅ {$permission->slug} 권한 생성/업데이트\n";
        }
        
        // 관리자 역할에 모든 권한 부여
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
            echo "✅ 관리자 역할에 모든 권한 부여 완료\n";
        }
        
        echo "=== 권한 시딩 완료 ===\n";
    }
}