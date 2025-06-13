<?php

namespace App\Filament\Resources\BannerPostResource\Pages;

use App\Filament\Resources\BannerPostResource;
use App\Filament\Resources\PostResource\Pages\EditPost;

class EditBannerPost extends EditPost
{
    protected static string $resource = BannerPostResource::class;
}
