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
                "position": "left",
                "color": "#FFFFFF"
            },
            "subtitle": {
                "position": "left",
                "color": "#E5E7EB"
            },
            "description": {
                "position": "left",
                "color": "#D1D5DB"
            },
            "button": {
                "position": "left",
                "text_color": "#FFFFFF",
                "border_color": "#FFFFFF",
                "bg_color": "transparent",
                "hover_text_color": "#000000",
                "hover_bg_color": "#FFFFFF"
            },
            "content_alignment": "center-left"
        }',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
