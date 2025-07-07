<?php

namespace App\Filament\Resources;

use App\Models\Post;
use App\Filament\Resources\ProductPostResource\Pages;

class ProductPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = '상품';

    protected static ?string $postType = Post::TYPE_PRODUCT;

    protected static ?int $navigationSort = 7;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('type', Post::TYPE_PRODUCT)->count();
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