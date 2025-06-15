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
                $responsesData[] = [
                    'checklist_type' => 'specific',
                    'question_label' => $question['label'],
                    'selected_label' => $selectedItem['label'],
                    'selected_score' => $selectedItem['score'],
                    'question_original_index' => $index,
                ];
            } else {
                // 기본 체크리스트 사용
                $selectedItem = $survey->checklist_items[$response];
                $responsesData[] = [
                    'checklist_type' => 'default',
                    'question_label' => $question['label'],
                    'selected_label' => $selectedItem['label'],
                    'selected_score' => $selectedItem['score'],
                    'question_original_index' => $index,
                ];
            }
            
            $totalScore += (int)$responsesData[count($responsesData) - 1]['selected_score'];
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
        // 점수 계산 및 분석
        $maxScore = count($survey->questions) * 4; // 각 문항 최대 4점 (매우 그렇다)
        $rawPercentage = round(($response->total_score / $maxScore) * 100);
        
        // 계기판 표시를 위해 역전 (낮은 점수가 좋은 상태)
        $percentage = 100 - $rawPercentage;
        
        // 레벨 판정
        $level = $this->getLevel($percentage);
        
        // 카테고리별 분석 (설문별로 커스터마이징 필요)
        $categoryAnalysis = $this->analyzeCategoriesForSurvey($survey, $response);
        
        return view('surveys.results', compact('survey', 'response', 'percentage', 'level', 'categoryAnalysis'));
    }
    
    private function getLevel($percentage)
    {
        // 6단계 시스템 - 역전된 값 기준 (높을수록 좋음)
        if ($percentage >= 83.33) {
            return ['name' => '최적', 'color' => 'darkgreen'];
        } elseif ($percentage >= 66.67) {
            return ['name' => '우수', 'color' => 'darkgreen'];
        } elseif ($percentage >= 50) {
            return ['name' => '양호', 'color' => 'green'];
        } elseif ($percentage >= 33.33) {
            return ['name' => '주의', 'color' => 'orange'];
        } elseif ($percentage >= 16.67) {
            return ['name' => '위험', 'color' => 'red'];
        } else {
            return ['name' => '붕괴', 'color' => 'darkred'];
        }
    }
    
    private function analyzeCategoriesForSurvey($survey, $response)
    {
        // 이 부분은 각 설문별로 카테고리를 다르게 분석해야 합니다.
        // 예시로 기본 구조만 제공
        $categories = [];
        
        // 설문 ID에 따른 카테고리 분석
        switch ($survey->id) {
            case 1: // 눈 노화 컨디션 셀프 테스트
                $categories = $this->analyzeEyeCategories($response);
                break;
            case 2: // 뇌신경 노화 셀프 테스트
                $categories = $this->analyzeBrainCategories($response);
                break;
            case 3: // 디지털 수면 패턴 자가분석
                $categories = $this->analyzeSleepCategories($response);
                break;
            default:
                $categories = $this->analyzeDefaultCategories($response);
        }
        
        return $categories;
    }
    
    private function analyzeEyeCategories($response)
    {
        // 눈 관련 카테고리 분석 - 역전된 값 사용
        return [
            ['name' => '눈 건강 상태', 'percentage' => 100 - rand(20, 60)],
            ['name' => '시각적 편안함', 'percentage' => 100 - rand(20, 60)],
            ['name' => '집중력 유지', 'percentage' => 100 - rand(20, 60)],
            ['name' => '디지털 기기 사용 균형', 'percentage' => 100 - rand(20, 60)],
            ['name' => '휴식 충분도', 'percentage' => 100 - rand(20, 60)],
        ];
    }
    
    private function analyzeBrainCategories($response)
    {
        // 뇌신경 관련 카테고리 분석 - 역전된 값 사용
        return [
            ['name' => '기억력 상태', 'percentage' => 100 - rand(20, 60)],
            ['name' => '집중력 상태', 'percentage' => 100 - rand(20, 60)],
            ['name' => '정신적 활력', 'percentage' => 100 - rand(20, 60)],
            ['name' => '인지 능력', 'percentage' => 100 - rand(20, 60)],
            ['name' => '회복력 상태', 'percentage' => 100 - rand(20, 60)],
        ];
    }
    
    private function analyzeSleepCategories($response)
    {
        // 수면 관련 카테고리 분석 - 역전된 값 사용
        return [
            ['name' => '수면 패턴 건강도', 'percentage' => 100 - rand(20, 60)],
            ['name' => '수면 진입 용이성', 'percentage' => 100 - rand(20, 60)],
            ['name' => '수면 지속성', 'percentage' => 100 - rand(20, 60)],
            ['name' => '디지털 기기 관리', 'percentage' => 100 - rand(20, 60)],
            ['name' => '수면 회복력', 'percentage' => 100 - rand(20, 60)],
        ];
    }
    
    private function analyzeDefaultCategories($response)
    {
        // 기본 카테고리 분석 - 역전된 값 사용
        return [
            ['name' => '전반적 상태', 'percentage' => 100 - rand(20, 60)],
            ['name' => '주의 필요도', 'percentage' => 100 - rand(20, 60)],
            ['name' => '개선 가능성', 'percentage' => 100 - rand(20, 60)],
        ];
    }
}
