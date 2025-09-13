<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $currentLang = session('locale', 'kor');
        
        // 설문들을 가져오기
        $surveys = Survey::all();
        
        // 각 설문의 데이터를 현재 언어에 맞게 변환
        $surveys = $surveys->map(function($survey) use ($currentLang) {
            // 기존 객체의 속성들을 현재 언어에 맞게 업데이트
            $survey->title = $survey->getTitle($currentLang);
            $survey->description = $survey->getDescription($currentLang);
            $survey->questions = $survey->getQuestions($currentLang);
            $survey->checklist_items = $survey->getChecklistItems($currentLang);
            $survey->frequency_items = $survey->getFrequencyItems($currentLang);
            // survey_image는 그대로 유지 (언어별 변환 불필요)
            
            return $survey;
        });
                        
        return view('surveys.index', compact('surveys'));
    }

    public function show(Survey $survey)
    {
        $currentLang = session('locale', 'kor');
        
        // 설문 데이터를 현재 언어에 맞게 변환
        $survey->title = $survey->getTitle($currentLang);
        $survey->description = $survey->getDescription($currentLang);
        $survey->questions = $survey->getQuestions($currentLang);
        $survey->checklist_items = $survey->getChecklistItems($currentLang);
        $survey->frequency_items = $survey->getFrequencyItems($currentLang);
        
        return view('surveys.show', compact('survey'));
    }

    public function store(Request $request, Survey $survey)
    {
        $responses = $request->input('responses');
        $frequencyResponses = $request->input('frequency_responses', []);
        $analysisType = $request->input('analysis_type', 'simple');
        $currentLang = session('locale', 'kor');
        $totalScore = 0;
        
        // 응답 데이터 구성
        $responsesData = [];
        $questions = $survey->getQuestions($currentLang);
        
        foreach ($responses as $index => $response) {
            $question = $questions[$index];
            
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
                // 현재 언어에 맞는 체크리스트 항목 사용
                $options = $survey->getChecklistItems($currentLang);
                $selectedItem = $options[$response];
                $responsesData[$index] = [
                    'checklist_type' => 'default',
                    'question_label' => $question['label'],
                    'selected_label' => $selectedItem['label'],
                    'selected_score' => $selectedItem['score'],
                    'question_original_index' => $index,
                ];
            }
            
            // 심층 분석인 경우 빈도 평가 응답도 저장
            if ($analysisType === 'detailed' && isset($frequencyResponses[$index])) {
                $frequencyOptions = $survey->getFrequencyItems($currentLang);
                $frequencyItem = $frequencyOptions[$frequencyResponses[$index]];
                $responsesData[$index]['frequency_response'] = [
                    'selected_label' => $frequencyItem['label'],
                    'selected_score' => $frequencyItem['score'],
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
            'analysis_type' => $analysisType,
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
        
        // 현재 언어에 맞는 카테고리 정보 가져오기
        $currentLang = session('locale', 'kor');
        $categoryData = $survey->getCategories($currentLang);
        
        // 카테고리별 점수를 역전하여 반환 (높은 점수가 나쁜 상태)
        $categories = [];
        foreach ($categoryScores as $index => $categoryScore) {
            $categoryInfo = $categoryData[$index] ?? null;
            
            // 응답한 문항이 있는 경우만 백분율 계산
            if ($categoryScore['answered_count'] > 0) {
                // 역전: 높은 점수가 나쁜 상태이므로, 낮은 백분율이 좋은 상태
                $percentage = 100 - $categoryScore['percentage'];
            } else {
                // 응답한 문항이 없으면 백분율을 표시하지 않음 (null 또는 0)
                $percentage = null;
            }
            
            $categories[] = [
                'name' => $categoryScore['name'],
                'percentage' => $percentage,
                'score' => $categoryScore['score'],
                'max_score' => $categoryScore['max_score'],
                'question_count' => $categoryScore['question_count'],
                'answered_count' => $categoryScore['answered_count'],
                'description' => $categoryInfo['description'] ?? '',
                'result_description' => $categoryInfo['result_description'] ?? ''
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
        
        // 현재 언어 가져오기
        $currentLang = session('locale', 'kor');
        
        // 다국어 기본 카테고리명
        $defaultCategoryNames = [
            'early' => [
                'kor' => '초기 증상',
                'eng' => 'Early Symptoms',
                'chn' => '早期症状',
                'hin' => 'प्रारंभिक लक्षण',
                'arb' => 'الأعراض المبكرة'
            ],
            'main' => [
                'kor' => '주요 증상',
                'eng' => 'Main Symptoms',
                'chn' => '主要症状',
                'hin' => 'मुख्य लक्षण',
                'arb' => 'الأعراض الرئيسية'
            ],
            'advanced' => [
                'kor' => '심화 증상',
                'eng' => 'Advanced Symptoms',
                'chn' => '晚期症状',
                'hin' => 'उन्नत लक्षण',
                'arb' => 'الأعراض المتقدمة'
            ]
        ];
        
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
            'name' => $defaultCategoryNames['early'][$currentLang] ?? $defaultCategoryNames['early']['kor'],
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
            'name' => $defaultCategoryNames['main'][$currentLang] ?? $defaultCategoryNames['main']['kor'],
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
            'name' => $defaultCategoryNames['advanced'][$currentLang] ?? $defaultCategoryNames['advanced']['kor'],
            'percentage' => $category3Count > 0 
                ? 100 - round(($category3Score / ($category3Count * 4)) * 100) 
                : 100
        ];
        
        return $categories;
    }
}
