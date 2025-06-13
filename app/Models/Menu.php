<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        // 메뉴가 생성, 수정, 삭제될 때 캐시 클리어
        static::saved(function ($menu) {
            static::clearCache();
        });

        static::deleted(function ($menu) {
            static::clearCache();
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->with('children');
    }

    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->orderBy('order');
    }

    public static function getBySlug(string $slug)
    {
        return static::where('slug', $slug)
            ->where('is_active', true)
            ->with(['items' => function ($query) {
                $query->where('is_active', true);
            }])
            ->first();
    }

    public static function clearCache()
    {
        Cache::forget('main-menu');
    }
}
