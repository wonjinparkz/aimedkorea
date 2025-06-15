<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'checklist_items',
        'questions',
    ];

    protected $casts = [
        'checklist_items' => 'array',
        'questions' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }
    
    /**
     * 카테고리 정보 가져오기
     */
    public function getCategories()
    {
        $savedData = get_option('survey_categories_' . $this->id);
        return $savedData['categories'] ?? [];
    }
    
    /**
     * 정렬된 문항 목록 가져오기 (순서대로 인덱스 부여)
     */
    public function getOrderedQuestions()
    {
        $orderedQuestions = [];
        $index = 0;
        
        if ($this->questions) {
            foreach ($this->questions as $question) {
                if (!empty($question['label'])) {
                    $orderedQuestions[$index] = $question;
                    $index++;
                }
            }
        }
        
        return $orderedQuestions;
    }
    
    /**
     * 카테고리별 문항 가져오기
     */
    public function getQuestionsByCategory()
    {
        $categories = $this->getCategories();
        $orderedQuestions = $this->getOrderedQuestions();
        $result = [];
        
        foreach ($categories as $category) {
            $categoryQuestions = [];
            foreach ($category['question_indices'] as $index) {
                if (isset($orderedQuestions[$index])) {
                    $categoryQuestions[] = [
                        'index' => $index,
                        'label' => $orderedQuestions[$index]['label'],
                        'has_specific_checklist' => $orderedQuestions[$index]['has_specific_checklist'] ?? false,
                        'specific_checklist_items' => $orderedQuestions[$index]['specific_checklist_items'] ?? [],
                    ];
                }
            }
            
            $result[] = [
                'name' => $category['name'],
                'questions' => $categoryQuestions
            ];
        }
        
        return $result;
    }
}
