<?php

namespace App\Filament\Resources\TabPostResource\Pages;

use App\Filament\Resources\TabPostResource;
use App\Filament\Resources\PostResource\Pages\ListPosts;

class ListTabPosts extends ListPosts
{
    protected static string $resource = TabPostResource::class;
}
