<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'user_id',
        'responses_data',
        'total_score',
        'recovery_score',
        'recovery_data',
        'last_viewed_at',
        'ip_address',
        'user_agent',
        'analysis_type',
    ];

    protected $casts = [
        'responses_data' => 'array',
        'recovery_data' => 'array',
        'last_viewed_at' => 'datetime',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * 카테고리별 점수 분석
     */
    public function getCategoryScores()
    {
        $survey = $this->survey;
        $categories = $survey->getCategories();
        $responses = $this->responses_data ?? [];
        
        $categoryScores = [];
        
        foreach ($categories as $category) {
            $categoryScore = 0;
            $categoryMaxScore = 0;
            $questionCount = 0;
            $answeredCount = 0;
            
            foreach ($category['question_indices'] as $index) {
                // 인덱스를 정수로 변환 (문자열로 저장된 경우 처리)
                $intIndex = (int)$index;
                $strIndex = (string)$index;
                $questionCount++;
                
                // 해당 인덱스의 응답 찾기 (문자열과 정수 인덱스 모두 확인)
                $response = null;
                if (isset($responses[$strIndex])) {
                    $response = $responses[$strIndex];
                } elseif (isset($responses[$intIndex])) {
                    $response = $responses[$intIndex];
                } elseif (isset($responses[$index])) {
                    $response = $responses[$index];
                }
                
                if ($response !== null) {
                    $score = (int)($response['selected_score'] ?? 0);
                    $categoryScore += $score;
                    $answeredCount++;
                }
                
                // 최대 점수는 항상 계산
                $maxScore = $this->getMaxScoreForQuestion($survey, $intIndex);
                $categoryMaxScore += $maxScore;
            }
            
            $percentage = $categoryMaxScore > 0 ? round(($categoryScore / $categoryMaxScore) * 100, 1) : 0;
            
            // 최대 점수가 0인 경우 (모든 문항이 없거나 잘못된 경우) 기본값 설정
            if ($categoryMaxScore == 0 && $questionCount > 0) {
                // 각 문항의 기본 최대 점수를 4로 가정
                $categoryMaxScore = $questionCount * 4;
            }
            
            // 백분율 계산 (점수가 높을수록 노화가 심하므로)
            $percentage = $categoryMaxScore > 0 ? round(($categoryScore / $categoryMaxScore) * 100, 1) : 0;
            
            $categoryScores[] = [
                'name' => $category['name'],
                'score' => $categoryScore,
                'max_score' => $categoryMaxScore,
                'percentage' => $percentage,
                'question_count' => $questionCount,
                'answered_count' => $answeredCount,
                'question_indices' => $category['question_indices']
            ];
        }
        
        return $categoryScores;
    }
    
    /**
     * 특정 문항의 최대 점수 계산
     */
    private function getMaxScoreForQuestion($survey, $questionIndex)
    {
        $questions = $survey->questions ?? [];
        
        // 인덱스를 정수로 변환
        $questionIndex = (int)$questionIndex;
        
        // 문항 배열을 순서대로 정리
        $orderedQuestions = [];
        $index = 0;
        foreach ($questions as $question) {
            if (!empty($question['label'])) {
                $orderedQuestions[$index] = $question;
                $index++;
            }
        }
        
        if (!isset($orderedQuestions[$questionIndex])) {
            return 4; // 기본 최대 점수
        }
        
        $question = $orderedQuestions[$questionIndex];
        
        // 개별 체크리스트가 있는 경우
        if ($question['has_specific_checklist'] ?? false) {
            $checklistItems = $question['specific_checklist_items'] ?? [];
        } else {
            // 전체 체크리스트 사용
            $checklistItems = $survey->checklist_items ?? [];
        }
        
        // 체크리스트에서 최대 점수 찾기
        $maxScore = 0;
        foreach ($checklistItems as $item) {
            $score = intval($item['score'] ?? 0);
            if ($score > $maxScore) {
                $maxScore = $score;
            }
        }
        
        return $maxScore;
    }
    
    /**
     * 전체 분석 데이터
     */
    public function getAnalysisData()
    {
        $categoryScores = $this->getCategoryScores();
        $responses = $this->responses_data ?? [];
        
        // 카테고리에 포함되지 않은 문항들의 점수
        $categorizedIndices = [];
        foreach ($categoryScores as $category) {
            $categorizedIndices = array_merge($categorizedIndices, $category['question_indices']);
        }
        
        $uncategorizedScore = 0;
        $uncategorizedCount = 0;
        
        foreach ($responses as $index => $response) {
            if (!in_array($index, $categorizedIndices)) {
                $uncategorizedScore += $response['selected_score'] ?? 0;
                $uncategorizedCount++;
            }
        }
        
        return [
            'total_score' => $this->total_score,
            'category_scores' => $categoryScores,
            'uncategorized_score' => $uncategorizedScore,
            'uncategorized_count' => $uncategorizedCount,
            'response_count' => count($responses),
            'question_count' => count($this->survey->questions ?? [])
        ];
    }
}
