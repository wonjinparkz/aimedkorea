<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'icon',
        'description',
        'is_mega_menu',
        'mega_menu_content',
        'order',
        'is_active',
        'target',
        'css_class',
    ];

    protected $casts = [
        'is_mega_menu' => 'boolean',
        'is_active' => 'boolean',
        'mega_menu_content' => 'array',
    ];

    protected static function booted()
    {
        // 메뉴 항목이 생성, 수정, 삭제될 때 캐시 클리어
        static::saved(function ($menuItem) {
            Menu::clearCache();
        });

        static::deleted(function ($menuItem) {
            Menu::clearCache();
        });
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->orderBy('order')
            ->with('children');
    }

    public function activeChildren(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->with('activeChildren');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootItems($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getFullUrlAttribute()
    {
        if (filter_var($this->url, FILTER_VALIDATE_URL)) {
            return $this->url;
        }

        return url($this->url ?? '/');
    }
}
