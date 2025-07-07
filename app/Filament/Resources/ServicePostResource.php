<?php

namespace App\Filament\Resources;

use App\Models\Post;
use App\Filament\Resources\ServicePostResource\Pages;

class ServicePostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = '서비스';

    protected static ?string $postType = Post::TYPE_SERVICE;

    protected static ?int $navigationSort = 9;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('type', Post::TYPE_SERVICE)->count();
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