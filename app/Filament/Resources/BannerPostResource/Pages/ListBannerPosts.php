<?php

namespace App\Filament\Resources\BannerPostResource\Pages;

use App\Filament\Resources\BannerPostResource;
use App\Filament\Resources\PostResource\Pages\ListPosts;

class ListBannerPosts extends ListPosts
{
    protected static string $resource = BannerPostResource::class;
}
