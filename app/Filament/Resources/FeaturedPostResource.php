<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeaturedPostResource\Pages;
use App\Models\Post;

class FeaturedPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationLabel = 'The Science Card';
    
    protected static ?string $modelLabel = 'The Science Card';
    
    protected static ?string $pluralModelLabel = 'The Science Card';
    
    protected static ?string $postType = Post::TYPE_FEATURED;
    
    protected static ?string $navigationGroup = '홈 구성';
    
    protected static ?int $navigationSort = 24;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeaturedPosts::route('/'),
            'create' => Pages\CreateFeaturedPost::route('/create'),
            'edit' => Pages\EditFeaturedPost::route('/{record}/edit'),
        ];
    }
}
