<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TabPostResource\Pages;
use App\Models\Post;

class TabPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = '탭';
    
    protected static ?string $modelLabel = '탭';
    
    protected static ?string $pluralModelLabel = '탭';
    
    protected static ?string $postType = Post::TYPE_TAB;
    
    protected static ?int $navigationSort = 5;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTabPosts::route('/'),
            'create' => Pages\CreateTabPost::route('/create'),
            'edit' => Pages\EditTabPost::route('/{record}/edit'),
        ];
    }
}
