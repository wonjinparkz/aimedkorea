<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_url',
        'background_image',
        'background_video',
        'background_type',
        'hero_settings',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hero_settings' => 'array',
    ];

    protected $attributes = [
        'hero_settings' => '{
            "title": {
                "color": "#FFFFFF",
                "size": "text-5xl"
            },
            "subtitle": {
                "color": "#E5E7EB",
                "size": "text-sm"
            },
            "description": {
                "color": "#D1D5DB",
                "size": "text-lg"
            },
            "button": {
                "text_color": "#FFFFFF",
                "bg_color": "#3B82F6",
                "style": "filled"
            },
            "content_alignment": "left",
            "overlay": {
                "enabled": true,
                "color": "#000000",
                "opacity": 60
            }
        }',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
