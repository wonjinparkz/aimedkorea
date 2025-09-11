<?php

namespace App\Filament\Resources\SurveyResource\Pages;

use App\Filament\Resources\SurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSurvey extends EditRecord
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 번역 데이터 처리
        if ($this->record) {
            // title_translations 처리 - 각 언어별로 분리
            if ($this->record->title_translations) {
                foreach ($this->record->title_translations as $lang => $title) {
                    $data['title_translations'][$lang] = $title;
                }
            }
            
            // description_translations 처리 - 각 언어별로 분리
            if ($this->record->description_translations) {
                foreach ($this->record->description_translations as $lang => $description) {
                    $data['description_translations'][$lang] = $description;
                }
            }
            
            // afterStateHydrated에서 카테고리 데이터를 처리하므로 여기서는 처리하지 않음
        }
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // 카테고리 정보 저장
        $formData = $this->form->getState();
        
        if ($this->record && $this->record->id) {
            // 문항 카테고리 저장
            if (isset($formData['question_categories'])) {
                $categoryData = $formData['question_categories'];
                
                if (!empty($categoryData['categories'])) {
                    $optionName = 'survey_categories_' . $this->record->id;
                    update_option($optionName, [
                        'survey_id' => $this->record->id,
                        'categories' => $categoryData['categories']
                    ]);
                }
            }
            
        }
    }
}
