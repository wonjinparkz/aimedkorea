<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate base_slug if not set
        if (empty($data['base_slug'])) {
            $data['base_slug'] = Str::slug($data['title']);
        }
        
        // Generate slug with language suffix
        $data['slug'] = $data['base_slug'] . '-' . $data['language'];
        
        return $data;
    }
}
