<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::all();
        return view('surveys.index', compact('surveys'));
    }

    public function show(Survey $survey)
    {
        return view('surveys.show', compact('survey'));
    }

    public function store(Request $request, Survey $survey)
    {
        $responses = $request->input('responses');
        $totalScore = 0;
        
        // 응답 데이터 구성
        $responsesData = [];
        foreach ($responses as $index => $response) {
            $question = $survey->questions[$index];
            
            if (isset($question['has_specific_checklist']) && $question['has_specific_checklist']) {
                // 개별 체크리스트가 있는 경우
                $selectedItem = $question['specific_checklist_items'][$response];
                $responsesData[$index] = [
                    'checklist_type' => 'specific',
                    'question_label' => $question['label'],
                    'selected_label' => $selectedItem['label'],
                    'selected_score' => $selectedItem['score'],
                    'question_original_index' => $index,
                ];
            } else {
                // 기본 체크리스트 사용
                $selectedItem = $survey->checklist_items[$response];
                $responsesData[$index] = [
                    'checklist_type' => 'default',
                    'question_label' => $question['label'],
                    'selected_label' => $selectedItem['label'],
                    'selected_score' => $selectedItem['score'],
                    'question_original_index' => $index,
                ];
            }
            
            $totalScore += (int)$responsesData[$index]['selected_score'];
        }
        
        // 응답 저장
        $surveyResponse = SurveyResponse::create([
            'survey_id' => $survey->id,
            'user_id' => auth()->id(),
            'responses_data' => $responsesData,
            'total_score' => $totalScore,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        return redirect()->route('surveys.results', [
            'survey' => $survey->id,
            'response' => $surveyResponse->id
        ]);
    }
    
    public function results(Survey $survey, SurveyResponse $response)
    {
        // 실제 카테고리별 분석 데이터 사용
        $categoryAnalysis = $this->analyzeCategoriesForSurvey($survey, $response);
        
        // 뷰로 전달 (percentage는 뷰에서 직접 계산)
        return view('surveys.results', compact('survey', 'response', 'categoryAnalysis'));
    }
    
    private function analyzeCategoriesForSurvey($survey, $response)
    {
        // 설문에 설정된 카테고리 정보 가져오기
        $categoryScores = $response->getCategoryScores();
        
        if (empty($categoryScores)) {
            // 카테고리가 설정되지 않은 경우 기본 분석 제공
            return $this->analyzeDefaultCategories($survey, $response);
        }
        
        // 카테고리별 점수를 역전하여 반환 (높은 점수가 좋은 상태)
        $categories = [];
        foreach ($categoryScores as $categoryScore) {
            $categories[] = [
                'name' => $categoryScore['name'],
                'percentage' => 100 - $categoryScore['percentage'], // 역전: 낮은 점수가 좋은 상태
                'score' => $categoryScore['score'],
                'max_score' => $categoryScore['max_score'],
                'question_count' => $categoryScore['question_count'],
                'answered_count' => $categoryScore['answered_count']
            ];
        }
        
        return $categories;
    }
    
    private function analyzeDefaultCategories($survey, $response)
    {
        // 카테고리가 설정되지 않은 경우의 기본 분석
        $responses = $response->responses_data ?? [];
        $totalQuestions = count($survey->questions);
        $answeredQuestions = count($responses);
        $totalScore = $response->total_score;
        $maxPossibleScore = $answeredQuestions * 4;
        
        // 전체 점수의 백분율 계산 후 역전
        $overallPercentage = $maxPossibleScore > 0 
            ? 100 - round(($totalScore / $maxPossibleScore) * 100) 
            : 100;
        
        // 문항 수를 3등분하여 가상의 카테고리 생성
        $questionsPerCategory = ceil($totalQuestions / 3);
        $categories = [];
        
        // 첫 번째 카테고리: 초기 문항들
        $category1Score = 0;
        $category1Count = 0;
        for ($i = 0; $i < min($questionsPerCategory, $answeredQuestions); $i++) {
            if (isset($responses[$i])) {
                $category1Score += $responses[$i]['selected_score'] ?? 0;
                $category1Count++;
            }
        }
        $categories[] = [
            'name' => '초기 증상',
            'percentage' => $category1Count > 0 
                ? 100 - round(($category1Score / ($category1Count * 4)) * 100) 
                : 100
        ];
        
        // 두 번째 카테고리: 중간 문항들
        $category2Score = 0;
        $category2Count = 0;
        for ($i = $questionsPerCategory; $i < min($questionsPerCategory * 2, $answeredQuestions); $i++) {
            if (isset($responses[$i])) {
                $category2Score += $responses[$i]['selected_score'] ?? 0;
                $category2Count++;
            }
        }
        $categories[] = [
            'name' => '주요 증상',
            'percentage' => $category2Count > 0 
                ? 100 - round(($category2Score / ($category2Count * 4)) * 100) 
                : 100
        ];
        
        // 세 번째 카테고리: 후반 문항들
        $category3Score = 0;
        $category3Count = 0;
        for ($i = $questionsPerCategory * 2; $i < $answeredQuestions; $i++) {
            if (isset($responses[$i])) {
                $category3Score += $responses[$i]['selected_score'] ?? 0;
                $category3Count++;
            }
        }
        $categories[] = [
            'name' => '종합 상태',
            'percentage' => $category3Count > 0 
                ? 100 - round(($category3Score / ($category3Count * 4)) * 100) 
                : 100
        ];
        
        return $categories;
    }
}
