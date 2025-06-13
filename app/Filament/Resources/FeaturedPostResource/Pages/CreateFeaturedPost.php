<?php

namespace App\Filament\Resources\FeaturedPostResource\Pages;

use App\Filament\Resources\FeaturedPostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;

class CreateFeaturedPost extends CreatePost
{
    protected static string $resource = FeaturedPostResource::class;
}
