<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\Role;
use App\Models\Permission;
use App\Helpers\PermissionHelper;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Component;

class PermissionMatrix extends Page
{
    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament.resources.role-resource.pages.permission-matrix';
    
    protected static ?string $title = '권한 배치표';
    
    protected static ?string $navigationLabel = '권한 배치표';
    
    public $permissions = [];
    public $roles = [];
    public $permissionMatrix = [];
    
    public function mount(): void
    {
        $this->roles = Role::orderBy('level', 'desc')->get();
        $this->permissions = Permission::all()->keyBy('slug');
        $this->loadPermissionMatrix();
    }
    
    protected function loadPermissionMatrix(): void
    {
        $matrix = [];
        
        foreach ($this->roles as $role) {
            $rolePermissions = $role->permissions->pluck('slug')->toArray();
            $matrix[$role->id] = $rolePermissions;
        }
        
        $this->permissionMatrix = $matrix;
    }
    
    public function togglePermission($roleId, $permissionSlug)
    {
        // 권한 확인
        PermissionHelper::requirePermission('roles-edit');
        
        $role = Role::find($roleId);
        $permission = Permission::where('slug', $permissionSlug)->first();
        
        if (!$role || !$permission) {
            return;
        }
        
        // 관리자 역할에서 권한을 제거하는 것을 방지
        if ($role->slug === 'admin') {
            Notification::make()
                ->title('경고')
                ->body('관리자 역할의 권한은 변경할 수 없습니다.')
                ->warning()
                ->send();
            return;
        }
        
        if ($role->permissions()->where('slug', $permissionSlug)->exists()) {
            // 권한 제거
            $role->permissions()->detach($permission->id);
            $this->permissionMatrix[$roleId] = array_diff($this->permissionMatrix[$roleId], [$permissionSlug]);
            $action = '제거';
        } else {
            // 권한 추가
            $role->permissions()->attach($permission->id);
            $this->permissionMatrix[$roleId][] = $permissionSlug;
            $action = '추가';
        }
        
        Notification::make()
            ->title('성공')
            ->body("{$role->display_name}에 {$permission->display_name} 권한이 {$action}되었습니다.")
            ->success()
            ->send();
    }
    
    public function getPermissionHierarchy(): array
    {
        return Permission::getPermissionHierarchy();
    }
    
    public function hasPermission($roleId, $permissionSlug): bool
    {
        return in_array($permissionSlug, $this->permissionMatrix[$roleId] ?? []);
    }
    
    public function toggleSectionPermissions($roleId, $sectionKey)
    {
        // 권한 확인
        PermissionHelper::requirePermission('roles-edit');
        
        $role = Role::find($roleId);
        
        if (!$role) {
            return;
        }
        
        // 관리자 역할에서 권한을 제거하는 것을 방지
        if ($role->slug === 'admin') {
            Notification::make()
                ->title('경고')
                ->body('관리자 역할의 권한은 변경할 수 없습니다.')
                ->warning()
                ->send();
            return;
        }
        
        $hierarchy = $this->getPermissionHierarchy();
        $section = $hierarchy[$sectionKey] ?? null;
        
        if (!$section) {
            return;
        }
        
        // 섹션의 모든 권한 수집
        $sectionPermissions = [];
        foreach ($section['children'] as $child) {
            $sectionPermissions = array_merge($sectionPermissions, $child['permissions']);
        }
        
        // 현재 상태 확인 (모든 권한이 있는지)
        $hasAllPermissions = true;
        foreach ($sectionPermissions as $permSlug) {
            if (!$this->hasPermission($roleId, $permSlug)) {
                $hasAllPermissions = false;
                break;
            }
        }
        
        // 토글 동작: 모든 권한이 있으면 제거, 없으면 추가
        $permissions = Permission::whereIn('slug', $sectionPermissions)->get();
        
        if ($hasAllPermissions) {
            // 모든 권한 제거
            foreach ($permissions as $permission) {
                if ($role->permissions()->where('slug', $permission->slug)->exists()) {
                    $role->permissions()->detach($permission->id);
                    $this->permissionMatrix[$roleId] = array_diff($this->permissionMatrix[$roleId] ?? [], [$permission->slug]);
                }
            }
            $action = '제거';
        } else {
            // 모든 권한 추가
            foreach ($permissions as $permission) {
                if (!$role->permissions()->where('slug', $permission->slug)->exists()) {
                    $role->permissions()->attach($permission->id);
                    $this->permissionMatrix[$roleId][] = $permission->slug;
                }
            }
            $action = '추가';
        }
        
        Notification::make()
            ->title('성공')
            ->body("{$role->display_name}에 [{$sectionKey}] {$section['name']} 섹션의 모든 권한이 {$action}되었습니다.")
            ->success()
            ->send();
    }
}
