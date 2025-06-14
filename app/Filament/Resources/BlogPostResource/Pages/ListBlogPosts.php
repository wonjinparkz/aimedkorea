<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use Illuminate\Contracts\View\View;

class ListBlogPosts extends ListPosts
{
    protected static string $resource = BlogPostResource::class;
    
    protected static string $view = 'filament.resources.post.card-list';
    
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [12, 24, 36, 48];
    }
}
