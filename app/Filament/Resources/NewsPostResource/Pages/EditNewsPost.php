<?php

namespace App\Filament\Resources\NewsPostResource\Pages;

use App\Filament\Resources\NewsPostResource;
use App\Filament\Resources\PostResource\Pages\EditPost;

class EditNewsPost extends EditPost
{
    protected static string $resource = NewsPostResource::class;
}
