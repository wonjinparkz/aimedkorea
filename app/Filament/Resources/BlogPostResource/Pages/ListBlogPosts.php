<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\PostResource\Pages\ListPosts;

class ListBlogPosts extends ListPosts
{
    protected static string $resource = BlogPostResource::class;
}
