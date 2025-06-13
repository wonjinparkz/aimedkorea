<?php

namespace App\Filament\Resources\FeaturedPostResource\Pages;

use App\Filament\Resources\FeaturedPostResource;
use App\Filament\Resources\PostResource\Pages\ListPosts;

class ListFeaturedPosts extends ListPosts
{
    protected static string $resource = FeaturedPostResource::class;
}
