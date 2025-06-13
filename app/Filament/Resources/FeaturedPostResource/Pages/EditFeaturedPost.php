<?php

namespace App\Filament\Resources\FeaturedPostResource\Pages;

use App\Filament\Resources\FeaturedPostResource;
use App\Filament\Resources\PostResource\Pages\EditPost;

class EditFeaturedPost extends EditPost
{
    protected static string $resource = FeaturedPostResource::class;
}
