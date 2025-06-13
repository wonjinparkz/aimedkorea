<?php

namespace App\Filament\Resources\BannerPostResource\Pages;

use App\Filament\Resources\BannerPostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;

class CreateBannerPost extends CreatePost
{
    protected static string $resource = BannerPostResource::class;
}
