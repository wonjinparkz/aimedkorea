<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeaturedPostResource\Pages;
use App\Models\Post;

class FeaturedPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationLabel = '특징';
    
    protected static ?string $modelLabel = '특징';
    
    protected static ?string $pluralModelLabel = '특징';
    
    protected static ?string $postType = Post::TYPE_FEATURED;
    
    protected static ?string $navigationGroup = '콘텐츠';
    
    protected static ?int $navigationSort = 33;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeaturedPosts::route('/'),
            'create' => Pages\CreateFeaturedPost::route('/create'),
            'edit' => Pages\EditFeaturedPost::route('/{record}/edit'),
        ];
    }
}
