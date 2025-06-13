<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'type',
        'summary',
        'read_more_text',
        'content',
        'featured',
        'image',
    ];

    protected $casts = [
        'featured' => 'boolean',
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
}
