<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoutinePostResource\Pages;
use App\Models\Post;

class RoutinePostResource extends PostResource
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = '루틴';
    
    protected static ?string $modelLabel = '루틴';
    
    protected static ?string $pluralModelLabel = '루틴';
    
    protected static ?string $postType = Post::TYPE_ROUTINE;
    
    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoutinePosts::route('/'),
            'create' => Pages\CreateRoutinePost::route('/create'),
            'edit' => Pages\EditRoutinePost::route('/{record}/edit'),
        ];
    }
}
