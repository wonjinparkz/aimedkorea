<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\Post;

class BlogPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    
    protected static ?string $navigationLabel = '블로그';
    
    protected static ?string $modelLabel = '블로그';
    
    protected static ?string $pluralModelLabel = '블로그';
    
    protected static ?string $postType = Post::TYPE_BLOG;
    
    protected static ?string $navigationGroup = '콘텐츠';
    
    protected static ?int $navigationSort = 31;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
