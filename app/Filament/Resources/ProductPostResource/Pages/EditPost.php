<?php

namespace App\Filament\Resources\ProductPostResource\Pages;

use App\Filament\Resources\ProductPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPost extends EditRecord
{
    protected static string $resource = ProductPostResource::class;
    
    public function getTitle(): string
    {
        $languageLabels = [
            'kor' => '한국어',
            'eng' => '영어',
            'chn' => '중국어',
            'hin' => '힌디어',
            'arb' => '아랍어'
        ];
        
        $language = $this->record->language ?? 'kor';
        $languageLabel = $languageLabels[$language] ?? $language;
        
        return parent::getTitle() . ' (' . $languageLabel . ')';
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\DeleteAction::make(),
        ];
        
        // Add create translation actions for missing languages
        $missingLanguages = $this->record->getMissingLanguages();
        
        if (!empty($missingLanguages)) {
            $languageLabels = [
                'kor' => '한국어',
                'eng' => 'English',
                'chn' => '中文',
                'hin' => 'हिन्दी',
                'arb' => 'العربية'
            ];
            
            foreach ($missingLanguages as $lang) {
                $actions[] = Actions\Action::make('create_' . $lang)
                    ->label($languageLabels[$lang] . ' 버전 생성')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->action(function () use ($lang, $languageLabels) {
                        // Create new post with same base_slug but different language
                        $newPost = $this->record->replicate();
                        $newPost->language = $lang;
                        $newPost->slug = $this->record->base_slug . '-' . $lang;
                        $newPost->save();
                        
                        $languageLabel = $languageLabels[$lang];
                        
                        Notification::make()
                            ->title($languageLabel . ' 버전이 생성되었습니다')
                            ->success()
                            ->send();
                            
                        // Redirect to edit the new translation
                        return redirect()->to(static::getResource()::getUrl('edit', ['record' => $newPost]));
                    });
            }
        }
        
        return $actions;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update slug when language changes
        if (isset($data['language']) && isset($data['base_slug'])) {
            $data['slug'] = $data['base_slug'] . '-' . $data['language'];
        }
        
        return $data;
    }
}