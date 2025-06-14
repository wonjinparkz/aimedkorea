<?php

namespace App\Filament\Resources\RoutinePostResource\Pages;

use App\Filament\Resources\RoutinePostResource;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use Illuminate\Contracts\View\View;

class ListRoutinePosts extends ListPosts
{
    protected static string $resource = RoutinePostResource::class;
    
    protected static string $view = 'filament.resources.post.card-list';
    
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [12, 24, 36, 48];
    }
}
