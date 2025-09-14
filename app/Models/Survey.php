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
        'parent_id',
        'is_detailed',
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
        'is_detailed' => 'boolean',
    ];

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /**
     * 부모 설문 (간편 분석)
     */
    public function parent()
    {
        return $this->belongsTo(Survey::class, 'parent_id');
    }

    /**
     * 자식 설문 (심층 분석)
     */
    public function detailedVersion()
    {
        return $this->hasOne(Survey::class, 'parent_id')->where('is_detailed', true);
    }

    /**
     * 간편 분석 버전인지 확인
     */
    public function isSimple()
    {
        return !$this->is_detailed && !$this->parent_id;
    }

    /**
     * 심층 분석 버전인지 확인
     */
    public function isDetailed()
    {
        return $this->is_detailed && $this->parent_id;
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
            // Check if category has translations structure or direct language keys
            if (isset($category['translations'])) {
                // Old structure with translations key
                $name = $category['translations'][$language]['name'] ?? $category['name'] ?? '';
                $description = $category['translations'][$language]['description'] ?? '';
                $resultDescription = $category['translations'][$language]['result_description'] ?? '';
            } else {
                // New structure with direct language keys in name, description, etc.
                $name = isset($category['name'][$language]) ? $category['name'][$language] : (isset($category['name']) && is_string($category['name']) ? $category['name'] : '');
                $description = isset($category['description'][$language]) ? $category['description'][$language] : (isset($category['description']) && is_string($category['description']) ? $category['description'] : '');
                $resultDescription = isset($category['result_description'][$language]) ? $category['result_description'][$language] : (isset($category['result_description']) && is_string($category['result_description']) ? $category['result_description'] : '');
            }
            
            // Ensure all values are strings
            if (is_array($name)) {
                $name = isset($name[$language]) ? $name[$language] : (isset($name['text']) ? $name['text'] : (isset($name[0]) ? $name[0] : ''));
            }
            if (is_array($description)) {
                $description = isset($description[$language]) ? $description[$language] : (isset($description['text']) ? $description['text'] : (isset($description[0]) ? $description[0] : ''));
            }
            if (is_array($resultDescription)) {
                $resultDescription = isset($resultDescription[$language]) ? $resultDescription[$language] : (isset($resultDescription['text']) ? $resultDescription['text'] : (isset($resultDescription[0]) ? $resultDescription[0] : ''));
            }
            
            $translatedCategory = [
                'name' => (string)$name,
                'description' => (string)$description,
                'result_description' => (string)$resultDescription,
                'question_indices' => $category['question_indices'] ?? []
            ];
            $result[] = $translatedCategory;
        }
        
        // 설명만 있는 카테고리 추가
        foreach ($descriptions as $desc) {
            // Check if desc has translations structure or direct language keys
            if (isset($desc['translations'])) {
                // Old structure with translations key
                $descName = $desc['translations'][$language]['name'] ?? $desc['name'] ?? '';
                $descDescription = $desc['translations'][$language]['description'] ?? '';
                $descResultDescription = $desc['translations'][$language]['result_description'] ?? '';
            } else {
                // New structure with direct language keys
                $descName = isset($desc['name'][$language]) ? $desc['name'][$language] : (isset($desc['name']) && is_string($desc['name']) ? $desc['name'] : '');
                $descDescription = isset($desc['description'][$language]) ? $desc['description'][$language] : (isset($desc['description']) && is_string($desc['description']) ? $desc['description'] : '');
                $descResultDescription = isset($desc['result_description'][$language]) ? $desc['result_description'][$language] : (isset($desc['result_description']) && is_string($desc['result_description']) ? $desc['result_description'] : '');
            }
            
            // Ensure name is a string
            if (is_array($descName)) {
                $descName = isset($descName[$language]) ? $descName[$language] : (isset($descName['text']) ? $descName['text'] : (isset($descName[0]) ? $descName[0] : ''));
            }
            $descName = (string)$descName;
            
            // 중복 확인
            $found = false;
            foreach ($result as &$cat) {
                if ($cat['name'] === $descName) {
                    // Update description if provided
                    if (!empty($descDescription)) {
                        if (is_array($descDescription)) {
                            $descDescription = isset($descDescription[$language]) ? $descDescription[$language] : (isset($descDescription['text']) ? $descDescription['text'] : (isset($descDescription[0]) ? $descDescription[0] : ''));
                        }
                        $cat['description'] = (string)$descDescription;
                    }
                    
                    // Update result_description if provided
                    if (!empty($descResultDescription)) {
                        if (is_array($descResultDescription)) {
                            $descResultDescription = isset($descResultDescription[$language]) ? $descResultDescription[$language] : (isset($descResultDescription['text']) ? $descResultDescription['text'] : (isset($descResultDescription[0]) ? $descResultDescription[0] : ''));
                        }
                        $cat['result_description'] = (string)$descResultDescription;
                    }
                    
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                // Ensure all values are strings
                if (is_array($descDescription)) {
                    $descDescription = isset($descDescription[$language]) ? $descDescription[$language] : (isset($descDescription['text']) ? $descDescription['text'] : (isset($descDescription[0]) ? $descDescription[0] : ''));
                }
                if (is_array($descResultDescription)) {
                    $descResultDescription = isset($descResultDescription[$language]) ? $descResultDescription[$language] : (isset($descResultDescription['text']) ? $descResultDescription['text'] : (isset($descResultDescription[0]) ? $descResultDescription[0] : ''));
                }
                
                // 새 카테고리 추가
                $translatedCategory = [
                    'name' => (string)$descName,
                    'description' => (string)$descDescription,
                    'result_description' => (string)$descResultDescription,
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
            $title = $this->title_translations[$language];
            // Ensure we return a string, not an array
            if (is_array($title)) {
                // Check various possible array structures
                if (isset($title['text'])) {
                    return (string)$title['text'];
                } elseif (isset($title[0])) {
                    return (string)$title[0];
                } else {
                    // If it's an unexpected array structure, return the first value
                    $firstValue = reset($title);
                    return is_string($firstValue) ? $firstValue : (string)$this->title;
                }
            }
            return (string)$title;
        }
        
        // 번역이 없으면 기본값 반환
        return (string)$this->title;
    }
    
    /**
     * 특정 언어의 설명 가져오기
     */
    public function getDescription($language = null)
    {
        $language = $language ?: app()->getLocale();
        
        if ($this->description_translations && isset($this->description_translations[$language])) {
            $description = $this->description_translations[$language];
            // Ensure we return a string, not an array
            if (is_array($description)) {
                // Check various possible array structures
                if (isset($description['text'])) {
                    return (string)$description['text'];
                } elseif (isset($description[0])) {
                    return (string)$description[0];
                } else {
                    // If it's an unexpected array structure, return the first value
                    $firstValue = reset($description);
                    return is_string($firstValue) ? $firstValue : (string)$this->description;
                }
            }
            return (string)$description;
        }
        
        return (string)$this->description;
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
        
        // For Korean (kor), always return the original questions
        // as they are already in Korean
        if ($language === 'kor') {
            return $this->questions;
        }
        
        // For other languages, check if translations exist
        if ($this->questions_translations && isset($this->questions_translations[$language])) {
            // If translation exists but has fewer questions than original,
            // use original questions as fallback
            $translatedQuestions = $this->questions_translations[$language];
            if (count($translatedQuestions) < count($this->questions)) {
                return $this->questions;
            }
            return $translatedQuestions;
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
