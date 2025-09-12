<?php

namespace App\Filament\Resources\PaperPostResource\Pages;

use App\Filament\Resources\PaperPostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Models\Post;

class CreatePaperPost extends CreatePost
{
    protected static string $resource = PaperPostResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Call parent to handle language and slug generation
        $data = parent::mutateFormDataBeforeCreate($data);
        
        // Add paper-specific data
        $data['type'] = Post::TYPE_PAPER;
        $data['author_id'] = auth()->check() ? auth()->id() : 1;
        
        return $data;
    }
}