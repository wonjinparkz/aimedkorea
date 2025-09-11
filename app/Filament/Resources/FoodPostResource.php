<?php

namespace App\Filament\Resources;

use App\Models\Post;
use App\Filament\Resources\FoodPostResource\Pages;

class FoodPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationLabel = '식품';

    protected static ?string $postType = Post::TYPE_FOOD;

    protected static ?string $navigationGroup = '콘텐츠';

    protected static ?int $navigationSort = 34;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('type', Post::TYPE_FOOD)->count();
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}