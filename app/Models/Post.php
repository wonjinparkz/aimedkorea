<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Post extends Model
{
    const TYPE_FEATURED = 'featured';
    const TYPE_ROUTINE = 'routine';
    const TYPE_BLOG = 'blog';
    const TYPE_NEWS = 'news';
    const TYPE_TAB = 'tab';
    const TYPE_BANNER = 'banner';
    const TYPE_PAPER = 'paper';
    const TYPE_PAGE = 'page';
    const TYPE_PRODUCT = 'product';
    const TYPE_FOOD = 'food';
    const TYPE_SERVICE = 'service';
    const TYPE_PROMOTION = 'promotion';
    const TYPE_QNA = 'qna';
    const TYPE_VIDEO = 'video';

    protected $fillable = [
        'title',
        'slug',
        'base_slug',
        'content_group_id',
        'is_primary',
        'type',
        'language',
        'summary',
        'read_more_text',
        'content',
        'content_sections',
        'related_articles',
        'is_featured',
        'is_published',
        'published_at',
        'author_id',
        'authors',
        'publisher',
        'link',
        'image',
        'video_type',
        'youtube_url',
        'video_file',
        'video_thumbnail',
        'video_duration',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'is_primary' => 'boolean',
        'content_sections' => 'json',
        'related_articles' => 'json',
        'authors' => 'json',
        'published_at' => 'datetime',
    ];

    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_FEATURED => '특징',
            self::TYPE_ROUTINE => '루틴',
            self::TYPE_BLOG => '블로그',
            self::TYPE_NEWS => '관련기사',
            self::TYPE_TAB => '탭',
            self::TYPE_BANNER => '배너',
            self::TYPE_PAPER => '논문요약',
            self::TYPE_PAGE => '페이지',
            self::TYPE_PRODUCT => '상품',
            self::TYPE_FOOD => '식품',
            self::TYPE_SERVICE => '서비스',
            self::TYPE_PROMOTION => '홍보',
            self::TYPE_QNA => 'Q&A',
            self::TYPE_VIDEO => '영상 미디어',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function relatedArticles()
    {
        $ids = $this->related_articles ?? [];
        return static::whereIn('id', $ids)->get();
    }

    public function getRelatedArticlesAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }

    public function setRelatedArticlesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['related_articles'] = json_encode($value);
        } else {
            $this->attributes['related_articles'] = $value;
        }
    }

    // Image mutator to handle Filament file uploads
    public function getImageAttribute($value)
    {
        // If it's already a string, return it
        if (is_string($value)) {
            return $value;
        }
        
        // If it's an array (from Filament), get the first item
        if (is_array($value) && count($value) > 0) {
            return $value[0];
        }
        
        return $value;
    }

    public function setImageAttribute($value)
    {
        // If it's an array (from Filament), store the first item
        if (is_array($value) && count($value) > 0) {
            $this->attributes['image'] = $value[0];
        } else {
            $this->attributes['image'] = $value;
        }
    }

    // Video file mutators to handle Filament file uploads
    public function getVideoFileAttribute($value)
    {
        // If it's already a string, return it
        if (is_string($value)) {
            return $value;
        }
        
        // If it's an array (from Filament), get the first item
        if (is_array($value) && count($value) > 0) {
            return $value[0];
        }
        
        return $value;
    }

    public function setVideoFileAttribute($value)
    {
        // If it's an array (from Filament), store the first item
        if (is_array($value) && count($value) > 0) {
            $this->attributes['video_file'] = $value[0];
        } else {
            $this->attributes['video_file'] = $value;
        }
    }

    public function getVideoThumbnailAttribute($value)
    {
        // If it's already a string, return it
        if (is_string($value)) {
            return $value;
        }
        
        // If it's an array (from Filament), get the first item
        if (is_array($value) && count($value) > 0) {
            return $value[0];
        }
        
        return $value;
    }

    public function setVideoThumbnailAttribute($value)
    {
        // If it's an array (from Filament), store the first item
        if (is_array($value) && count($value) > 0) {
            $this->attributes['video_thumbnail'] = $value[0];
        } else {
            $this->attributes['video_thumbnail'] = $value;
        }
    }

    // Content grouping and multilingual support methods
    public function contentGroup()
    {
        return static::where('content_group_id', $this->content_group_id)
                    ->where('id', '!=', $this->id);
    }

    public function translations()
    {
        return $this->contentGroup();
    }

    public function getTranslation($language)
    {
        return static::where('content_group_id', $this->content_group_id)
                    ->where('language', $language)
                    ->first();
    }

    public function hasTranslation($language)
    {
        return static::where('content_group_id', $this->content_group_id)
                    ->where('language', $language)
                    ->exists();
    }

    public function getAvailableLanguages()
    {
        return static::where('content_group_id', $this->content_group_id)
                    ->pluck('language')
                    ->toArray();
    }

    public function getMissingLanguages()
    {
        $availableLanguages = $this->getAvailableLanguages();
        $allLanguages = ['kor', 'eng', 'chn', 'hin', 'arb'];
        return array_diff($allLanguages, $availableLanguages);
    }

    public function getPrimaryPost()
    {
        if ($this->is_primary) {
            return $this;
        }
        
        return static::where('content_group_id', $this->content_group_id)
                    ->where('is_primary', true)
                    ->first();
    }

    public function getAllVersions()
    {
        return static::where('content_group_id', $this->content_group_id)
                    ->orderBy('language')
                    ->get();
    }

    // Scope for homepage to show only primary posts
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // Scope for specific language
    public function scopeInLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    // Generate content group ID
    public static function generateContentGroupId($type, $baseSlug = null)
    {
        $timestamp = now()->format('Ymd_His');
        $random = substr(md5(uniqid()), 0, 6);
        return $type . '_' . ($baseSlug ?: $timestamp . '_' . $random);
    }
}
