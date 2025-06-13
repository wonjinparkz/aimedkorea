<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsPostResource\Pages;
use App\Models\Post;

class NewsPostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    
    protected static ?string $navigationLabel = '관련기사';
    
    protected static ?string $modelLabel = '관련기사';
    
    protected static ?string $pluralModelLabel = '관련기사';
    
    protected static ?string $postType = Post::TYPE_NEWS;
    
    protected static ?int $navigationSort = 4;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsPosts::route('/'),
            'create' => Pages\CreateNewsPost::route('/create'),
            'edit' => Pages\EditNewsPost::route('/{record}/edit'),
        ];
    }
}
