<?php

namespace App\Filament\Resources\ServicePostResource\Pages;

use App\Filament\Resources\ServicePostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = ServicePostResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['author_id'] = auth()->check() ? auth()->id() : 1;
        
        return $data;
    }
}