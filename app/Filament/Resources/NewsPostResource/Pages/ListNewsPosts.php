<?php

namespace App\Filament\Resources\NewsPostResource\Pages;

use App\Filament\Resources\NewsPostResource;
use App\Filament\Resources\PostResource\Pages\ListPosts;

class ListNewsPosts extends ListPosts
{
    protected static string $resource = NewsPostResource::class;
}
