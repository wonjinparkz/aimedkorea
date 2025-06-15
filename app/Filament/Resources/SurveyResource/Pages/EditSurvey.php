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
        // 저장된 카테고리 정보 불러오기
        if ($this->record && $this->record->id) {
            $optionName = 'survey_categories_' . $this->record->id;
            $savedData = get_option($optionName);
            
            if ($savedData && isset($savedData['categories'])) {
                $categories = [];
                foreach ($savedData['categories'] as $category) {
                    $categories[] = [
                        'category_name' => $category['name'],
                        'question_indices' => array_map('strval', $category['question_indices'] ?? [])
                    ];
                }
                $data['question_categories'] = $categories;
            }
        }
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // 카테고리 정보 저장
        $formData = $this->form->getState();
        
        if ($this->record && $this->record->id && isset($formData['question_categories'])) {
            $categories = $formData['question_categories'];
            
            // 카테고리 데이터 구성
            $categoryData = [];
            foreach ($categories as $category) {
                if (!empty($category['category_name']) && !empty($category['question_indices'])) {
                    // 인덱스를 정수로 변환하여 저장
                    $indices = array_map(function($idx) {
                        return intval($idx);
                    }, $category['question_indices']);
                    
                    $categoryData[] = [
                        'name' => $category['category_name'],
                        'question_indices' => $indices
                    ];
                }
            }
            
            // 옵션으로 저장
            $optionName = 'survey_categories_' . $this->record->id;
            update_option($optionName, [
                'survey_id' => $this->record->id,
                'categories' => $categoryData
            ]);
        }
    }
}
