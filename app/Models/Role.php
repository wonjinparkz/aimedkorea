<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'display_name_ko',
        'display_name_en',
        'description',
        'level',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    // 사용자와의 관계
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    // 권한과의 관계
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    // 특정 권한이 있는지 확인
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    // 특정 모듈에 대한 모든 권한 확인
    public function hasModulePermission(string $module, string $action = null): bool
    {
        $query = $this->permissions()->where('module', $module);
        
        if ($action) {
            $query->where('action', $action);
        }
        
        return $query->exists();
    }

    // 현재 언어에 따른 표시 이름 반환
    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();
        $field = "display_name_{$locale}";
        
        return $this->$field ?? $this->display_name_en ?? $this->name;
    }

    // 기본 역할 생성
    public static function getDefaultRoles(): array
    {
        return [
            [
                'name' => 'admin',
                'slug' => 'admin',
                'display_name_ko' => '관리자',
                'display_name_en' => 'Administrator',
                'description' => 'Full system access',
                'level' => 100,
            ],
            [
                'name' => 'site_manager',
                'slug' => 'site-manager',
                'display_name_ko' => '사이트 관리자',
                'display_name_en' => 'Site Manager',
                'description' => 'Manage site settings and configurations',
                'level' => 75,
            ],
            [
                'name' => 'content_manager',
                'slug' => 'content-manager',
                'display_name_ko' => '콘텐츠 관리자',
                'display_name_en' => 'Content Manager',
                'description' => 'Manage content and posts',
                'level' => 50,
            ],
            [
                'name' => 'user',
                'slug' => 'user',
                'display_name_ko' => '유저',
                'display_name_en' => 'User',
                'description' => 'Basic user access',
                'level' => 10,
            ],
        ];
    }
}
