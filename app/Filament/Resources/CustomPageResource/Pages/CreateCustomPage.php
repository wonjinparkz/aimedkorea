<?php

namespace App\Filament\Resources\CustomPageResource\Pages;

use App\Filament\Resources\CustomPageResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateCustomPage extends CreateRecord
{
    protected static string $resource = CustomPageResource::class;
    
    public function getTitle(): string
    {
        return '새 페이지 만들기';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return '페이지가 생성되었습니다';
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['author_id'] = auth()->id();
        
        // Generate base_slug if not set
        if (empty($data['base_slug'])) {
            $data['base_slug'] = Str::slug($data['title']);
        }
        
        // Generate slug with language suffix
        $data['slug'] = $data['base_slug'] . '-' . $data['language'];
        
        return $data;
    }
}