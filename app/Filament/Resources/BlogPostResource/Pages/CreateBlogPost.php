<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;

class CreateBlogPost extends CreatePost
{
    protected static string $resource = BlogPostResource::class;
}
