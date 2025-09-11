<?php

namespace App\Filament\Resources\HeroResource\Pages;

use App\Filament\Resources\HeroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHero extends EditRecord
{
    protected static string $resource = HeroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 번역 데이터 처리
        if ($this->record) {
            // title_translations 처리
            if ($this->record->title_translations) {
                foreach ($this->record->title_translations as $lang => $title) {
                    $data['title_translations'][$lang] = $title;
                }
            } elseif ($this->record->title) {
                // 기존 title이 있지만 번역이 없는 경우 한국어로 설정
                $data['title_translations']['kor'] = $this->record->title;
            }
            
            // subtitle_translations 처리
            if ($this->record->subtitle_translations) {
                foreach ($this->record->subtitle_translations as $lang => $subtitle) {
                    $data['subtitle_translations'][$lang] = $subtitle;
                }
            } elseif ($this->record->subtitle) {
                // 기존 subtitle이 있지만 번역이 없는 경우 한국어로 설정
                $data['subtitle_translations']['kor'] = $this->record->subtitle;
            }
            
            // description_translations 처리
            if ($this->record->description_translations) {
                foreach ($this->record->description_translations as $lang => $description) {
                    $data['description_translations'][$lang] = $description;
                }
            } elseif ($this->record->description) {
                // 기존 description이 있지만 번역이 없는 경우 한국어로 설정
                $data['description_translations']['kor'] = $this->record->description;
            }
            
            // button_text_translations 처리
            if ($this->record->button_text_translations) {
                foreach ($this->record->button_text_translations as $lang => $buttonText) {
                    $data['button_text_translations'][$lang] = $buttonText;
                }
            } elseif ($this->record->button_text) {
                // 기존 button_text가 있지만 번역이 없는 경우 한국어로 설정
                $data['button_text_translations']['kor'] = $this->record->button_text;
            }
        }
        
        return $data;
    }
}
