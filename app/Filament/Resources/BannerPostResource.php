<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerPostResource\Pages;
use App\Models\Post;

class BannerPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = '배너';
    
    protected static ?string $modelLabel = '배너';
    
    protected static ?string $pluralModelLabel = '배너';
    
    protected static ?string $postType = Post::TYPE_BANNER;
    
    protected static ?string $navigationGroup = '홈 구성';
    
    protected static ?int $navigationSort = 22;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBannerPosts::route('/'),
            'create' => Pages\CreateBannerPost::route('/create'),
            'edit' => Pages\EditBannerPost::route('/{record}/edit'),
        ];
    }
}
