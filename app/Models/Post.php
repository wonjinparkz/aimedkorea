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

    protected $fillable = [
        'title',
        'slug',
        'type',
        'summary',
        'read_more_text',
        'content',
        'content_sections',
        'related_articles',
        'featured',
        'is_published',
        'published_at',
        'author_id',
        'image',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'is_published' => 'boolean',
        'content_sections' => 'json',
        'related_articles' => 'json',
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
}
