<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\PostResource\Pages\EditPost;

class EditBlogPost extends EditPost
{
    protected static string $resource = BlogPostResource::class;
}
