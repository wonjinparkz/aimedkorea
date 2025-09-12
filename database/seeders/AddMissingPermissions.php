<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class AddMissingPermissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 누락된 권한 추가
        $missingPermissions = [
            [
                'name' => 'dashboard.view',
                'slug' => 'dashboard-view',
                'module' => 'dashboard',
                'action' => 'view',
                'display_name_ko' => '대시보드 보기',
                'display_name_en' => 'Dashboard View',
                'description' => 'Allow view action on dashboard',
            ],
            [
                'name' => 'permissions.view',
                'slug' => 'permissions-view',
                'module' => 'permissions',
                'action' => 'view',
                'display_name_ko' => '권한 보기',
                'display_name_en' => 'Permissions View',
                'description' => 'Allow view action on permissions',
            ],
            [
                'name' => 'permissions.edit',
                'slug' => 'permissions-edit',
                'module' => 'permissions',
                'action' => 'edit',
                'display_name_ko' => '권한 수정',
                'display_name_en' => 'Permissions Edit',
                'description' => 'Allow edit action on permissions',
            ]
        ];
        
        foreach ($missingPermissions as $permissionData) {
            Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }
        
        // 관리자에게 모든 권한 추가
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
        }
        
        $this->command->info('Missing permissions added successfully!');
    }
}
