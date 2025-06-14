<?php

namespace App\Filament\Resources\RoutinePostResource\Pages;

use App\Filament\Resources\RoutinePostResource;
use App\Filament\Resources\PostResource\Pages\ListPosts;

class ListRoutinePosts extends ListPosts
{
    protected static string $resource = RoutinePostResource::class;
}
