<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'checklist_items',
        'frequency_items',
        'questions',
        'title_translations',
        'description_translations',
        'checklist_items_translations',
        'frequency_items_translations',
        'questions_translations',
        'survey_image',
        'result_commentary',
        'category_analysis_description',
    ];

    protected $casts = [
        'checklist_items' => 'array',
        'frequency_items' => 'array',
        'questions' => 'array',
        'title_translations' => 'array',
        'description_translations' => 'array',
        'checklist_items_translations' => 'array',
        'frequency_items_translations' => 'array',
        'questions_translations' => 'array',
        'result_commentary' => 'array',
        'category_analysis_description' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }
    
    /**
     * 카테고리 정보 가져오기
     */
    public function getCategories($language = null)
    {
        $language = $language ?: session('locale', 'kor');
        
        // 문항 카테고리 데이터
        $categoriesData = get_option('survey_categories_' . $this->id);
        $categories = $categoriesData['categories'] ?? [];
        
        // 카테고리 설명 데이터
        $descriptionsData = get_option('survey_category_descriptions_' . $this->id);
        $descriptions = $descriptionsData['categories'] ?? [];
        
        // 데이터 병합
        $result = [];
        
        // 문항 카테고리 처리
        foreach ($categories as $category) {
            $translatedCategory = [
                'name' => $category['translations'][$language]['name'] ?? $category['name'] ?? '',
                'description' => $category['translations'][$language]['description'] ?? '',
                'result_description' => $category['translations'][$language]['result_description'] ?? '',
                'question_indices' => $category['question_indices'] ?? []
            ];
            $result[] = $translatedCategory;
        }
        
        // 설명만 있는 카테고리 추가
        foreach ($descriptions as $desc) {
            // 중복 확인
            $found = false;
            foreach ($result as &$cat) {
                if ($cat['name'] === ($desc['translations'][$language]['name'] ?? $desc['name'] ?? '')) {
                    // 설명 업데이트
                    $cat['description'] = $desc['translations'][$language]['description'] ?? $cat['description'];
                    $cat['result_description'] = $desc['translations'][$language]['result_description'] ?? $cat['result_description'];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                // 새 카테고리 추가
                $translatedCategory = [
                    'name' => $desc['translations'][$language]['name'] ?? $desc['name'] ?? '',
                    'description' => $desc['translations'][$language]['description'] ?? '',
                    'result_description' => $desc['translations'][$language]['result_description'] ?? '',
                    'question_indices' => []
                ];
                $result[] = $translatedCategory;
            }
        }
        
        return $result;
    }
    
    
    /**
     * 카테고리별 문항 가져오기
     */
    public function getQuestionsByCategory($language = null)
    {
        $categories = $this->getCategories($language);
        $orderedQuestions = $this->getOrderedQuestions($language);
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
                'description' => $category['description'] ?? '',
                'result_description' => $category['result_description'] ?? '',
                'questions' => $categoryQuestions
            ];
        }
        
        return $result;
    }
    
    /**
     * 다국어 관련 메소드들
     */
    
    /**
     * 특정 언어의 제목 가져오기
     */
    public function getTitle($language = null)
    {
        $language = $language ?: app()->getLocale();
        
        if ($this->title_translations && isset($this->title_translations[$language])) {
            return $this->title_translations[$language];
        }
        
        // 번역이 없으면 기본값 반환
        return $this->title;
    }
    
    /**
     * 특정 언어의 설명 가져오기
     */
    public function getDescription($language = null)
    {
        $language = $language ?: app()->getLocale();
        
        if ($this->description_translations && isset($this->description_translations[$language])) {
            return $this->description_translations[$language];
        }
        
        return $this->description;
    }
    
    /**
     * 특정 언어의 체크리스트 항목 가져오기
     */
    public function getChecklistItems($language = null)
    {
        $language = $language ?: app()->getLocale();
        
        if ($this->checklist_items_translations && isset($this->checklist_items_translations[$language])) {
            return $this->checklist_items_translations[$language];
        }
        
        return $this->checklist_items;
    }
    
    /**
     * 특정 언어의 빈도 평가 항목 가져오기
     */
    public function getFrequencyItems($language = null)
    {
        $language = $language ?: app()->getLocale();
        
        if ($this->frequency_items_translations && isset($this->frequency_items_translations[$language])) {
            return $this->frequency_items_translations[$language];
        }
        
        return $this->frequency_items;
    }
    
    /**
     * 특정 언어의 질문 가져오기
     */
    public function getQuestions($language = null)
    {
        $language = $language ?: app()->getLocale();
        
        if ($this->questions_translations && isset($this->questions_translations[$language])) {
            return $this->questions_translations[$language];
        }
        
        return $this->questions;
    }
    
    /**
     * 특정 언어의 번역이 존재하는지 확인
     */
    public function hasTranslation($language)
    {
        return ($this->title_translations && isset($this->title_translations[$language])) ||
               ($this->description_translations && isset($this->description_translations[$language])) ||
               ($this->checklist_items_translations && isset($this->checklist_items_translations[$language])) ||
               ($this->questions_translations && isset($this->questions_translations[$language]));
    }
    
    /**
     * 사용 가능한 언어 목록
     */
    public function getAvailableLanguages()
    {
        $languages = [];
        
        if ($this->title_translations) {
            $languages = array_merge($languages, array_keys($this->title_translations));
        }
        
        return collect($languages)->unique()->values();
    }
    
    /**
     * 번역이 누락된 언어 목록
     */
    public function getMissingLanguages()
    {
        $availableLanguages = $this->getAvailableLanguages();
        $allLanguages = ['kor', 'eng', 'chn', 'hin', 'arb'];
        
        return collect($allLanguages)->diff($availableLanguages);
    }
    
    /**
     * 특정 언어의 번역 설정
     */
    public function setTranslation($language, $field, $value)
    {
        $translationField = $field . '_translations';
        $translations = $this->$translationField ?? [];
        $translations[$language] = $value;
        $this->$translationField = $translations;
        
        return $this;
    }
    
    /**
     * 정렬된 문항 목록 가져오기 (언어별)
     */
    public function getOrderedQuestions($language = null)
    {
        $orderedQuestions = [];
        $index = 0;
        $questions = $this->getQuestions($language);
        
        if ($questions) {
            foreach ($questions as $question) {
                if (!empty($question['label'])) {
                    $orderedQuestions[$index] = $question;
                    $index++;
                }
            }
        }
        
        return $orderedQuestions;
    }
    
    /**
     * 특정 언어의 결과 해설 가져오기 (노화지수에 따라)
     */
    public function getResultCommentary($language = null, $agingPercentage = null)
    {
        $language = $language ?: app()->getLocale();
        
        if (!$this->result_commentary || !isset($this->result_commentary[$language])) {
            return null;
        }
        
        // 노화지수가 제공되지 않은 경우 일반 해설 반환 (하위 호환성)
        if ($agingPercentage === null) {
            return $this->result_commentary[$language] ?? null;
        }
        
        // 노화지수에 따른 구간별 해설 반환
        $commentaries = $this->result_commentary[$language];
        
        if ($agingPercentage <= 15) {
            return $commentaries['excellent'] ?? null;  // 0~15% 최적
        } elseif ($agingPercentage <= 30) {
            return $commentaries['good'] ?? null;       // 16~30% 우수
        } elseif ($agingPercentage <= 50) {
            return $commentaries['fair'] ?? null;       // 31~50% 양호
        } elseif ($agingPercentage <= 70) {
            return $commentaries['caution'] ?? null;    // 51~70% 주의
        } elseif ($agingPercentage <= 85) {
            return $commentaries['danger'] ?? null;     // 71~85% 위험
        } else {
            return $commentaries['critical'] ?? null;   // 86~100% 붕괴
        }
    }
    
    /**
     * 특정 언어의 카테고리 분석 설명 가져오기
     */
    public function getCategoryAnalysisDescription($language = null)
    {
        $language = $language ?: app()->getLocale();
        
        if ($this->category_analysis_description && isset($this->category_analysis_description[$language])) {
            return $this->category_analysis_description[$language];
        }
        
        return null;
    }
}
