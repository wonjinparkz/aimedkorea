<?php

namespace App\Filament\Resources\QnaPostResource\Pages;

use App\Filament\Resources\QnaPostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;

class CreateQnaPost extends CreatePost
{
    protected static string $resource = QnaPostResource::class;

    // getRedirectUrl() is inherited from CreatePost

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Call parent method to handle language-based slug generation
        $data = parent::mutateFormDataBeforeCreate($data);
        
        // QNA-specific data
        $data['type'] = \App\Models\Post::TYPE_QNA;
        $data['author_id'] = auth()->id();
        
        return $data;
    }
}