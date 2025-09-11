<?php

namespace App\Filament\Resources\SurveyResource\Pages;

use App\Filament\Resources\SurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSurvey extends CreateRecord
{
    protected static string $resource = SurveyResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 기본 언어 설정
        if (!isset($data['is_primary'])) {
            $data['is_primary'] = true;
        }
        
        return $data;
    }
    
    protected function afterCreate(): void
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
