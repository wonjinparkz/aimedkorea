<?php

namespace App\Filament\Resources;

use App\Models\Post;
use App\Filament\Resources\PromotionPostResource\Pages;

class PromotionPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = '홍보';

    protected static ?string $postType = Post::TYPE_PROMOTION;

    protected static ?string $navigationGroup = '마케팅';

    protected static ?int $navigationSort = 90;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('type', Post::TYPE_PROMOTION)->count();
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