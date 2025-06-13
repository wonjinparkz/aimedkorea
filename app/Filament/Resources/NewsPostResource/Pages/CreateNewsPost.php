<?php

namespace App\Filament\Resources\NewsPostResource\Pages;

use App\Filament\Resources\NewsPostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;

class CreateNewsPost extends CreatePost
{
    protected static string $resource = NewsPostResource::class;
}
