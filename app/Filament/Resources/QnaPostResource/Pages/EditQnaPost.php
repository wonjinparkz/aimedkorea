<?php

namespace App\Filament\Resources\QnaPostResource\Pages;

use App\Filament\Resources\QnaPostResource;
use App\Filament\Resources\PostResource\Pages\EditPost;
use Filament\Actions;

class EditQnaPost extends EditPost
{
    protected static string $resource = QnaPostResource::class;

    // getHeaderActions() is inherited from EditPost
    // getRedirectUrl() is inherited from EditPost

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Call parent method to handle language-based slug generation
        $data = parent::mutateFormDataBeforeSave($data);
        
        // Additional QNA-specific logic can be added here if needed
        
        return $data;
    }
}