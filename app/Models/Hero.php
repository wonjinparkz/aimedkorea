<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hero extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_url',
        'button_post_id',
        'background_image',
        'background_video',
        'background_type',
        'hero_settings',
        'is_active',
        'order',
        'title_translations',
        'subtitle_translations',
        'description_translations',
        'button_text_translations',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hero_settings' => 'array',
        'title_translations' => 'array',
        'subtitle_translations' => 'array',
        'description_translations' => 'array',
        'button_text_translations' => 'array',
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

    public function buttonPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'button_post_id');
    }

    /**
     * 특정 언어의 제목 가져오기
     */
    public function getTitle($language = null)
    {
        // 언어가 지정되지 않으면 세션에서 가져오기
        $language = $language ?: session('locale', 'kor');
        
        if ($this->title_translations && isset($this->title_translations[$language])) {
            return $this->title_translations[$language];
        }
        
        // 한국어 번역이 있으면 반환
        if ($this->title_translations && isset($this->title_translations['kor'])) {
            return $this->title_translations['kor'];
        }
        
        // 기존 title 필드 반환
        return $this->title;
    }

    /**
     * 특정 언어의 부제목 가져오기
     */
    public function getSubtitle($language = null)
    {
        // 언어가 지정되지 않으면 세션에서 가져오기
        $language = $language ?: session('locale', 'kor');
        
        if ($this->subtitle_translations && isset($this->subtitle_translations[$language])) {
            return $this->subtitle_translations[$language];
        }
        
        // 한국어 번역이 있으면 반환
        if ($this->subtitle_translations && isset($this->subtitle_translations['kor'])) {
            return $this->subtitle_translations['kor'];
        }
        
        // 기존 subtitle 필드 반환
        return $this->subtitle;
    }

    /**
     * 특정 언어의 설명 가져오기
     */
    public function getDescription($language = null)
    {
        // 언어가 지정되지 않으면 세션에서 가져오기
        $language = $language ?: session('locale', 'kor');
        
        if ($this->description_translations && isset($this->description_translations[$language])) {
            return $this->description_translations[$language];
        }
        
        // 한국어 번역이 있으면 반환
        if ($this->description_translations && isset($this->description_translations['kor'])) {
            return $this->description_translations['kor'];
        }
        
        // 기존 description 필드 반환
        return $this->description;
    }

    /**
     * 특정 언어의 버튼 텍스트 가져오기
     */
    public function getButtonText($language = null)
    {
        // 언어가 지정되지 않으면 세션에서 가져오기
        $language = $language ?: session('locale', 'kor');
        
        if ($this->button_text_translations && isset($this->button_text_translations[$language])) {
            return $this->button_text_translations[$language];
        }
        
        // 한국어 번역이 있으면 반환
        if ($this->button_text_translations && isset($this->button_text_translations['kor'])) {
            return $this->button_text_translations['kor'];
        }
        
        // 기존 button_text 필드 반환
        return $this->button_text;
    }

    /**
     * 특정 언어의 번역이 존재하는지 확인
     */
    public function hasTranslation($language)
    {
        return ($this->title_translations && isset($this->title_translations[$language])) ||
               ($this->subtitle_translations && isset($this->subtitle_translations[$language])) ||
               ($this->description_translations && isset($this->description_translations[$language])) ||
               ($this->button_text_translations && isset($this->button_text_translations[$language]));
    }

    /**
     * 사용 가능한 언어 목록
     */
    public function getAvailableLanguages()
    {
        $languages = [];
        
        if ($this->title_translations) {
            $languages = array_merge($languages, array_keys($this->title_translations));
        }
        if ($this->subtitle_translations) {
            $languages = array_merge($languages, array_keys($this->subtitle_translations));
        }
        if ($this->description_translations) {
            $languages = array_merge($languages, array_keys($this->description_translations));
        }
        if ($this->button_text_translations) {
            $languages = array_merge($languages, array_keys($this->button_text_translations));
        }
        
        return collect($languages)->unique()->values();
    }
}
