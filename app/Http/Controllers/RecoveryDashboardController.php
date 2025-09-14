<?php

namespace App\Http\Controllers;

use App\Models\SurveyTimeline;
use App\Models\TimelineCheckpoint;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecoveryDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // 사용자의 활성 타임라인 가져오기
        $timelines = SurveyTimeline::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['survey', 'checkpoints' => function ($query) {
                $query->orderBy('week_number');
            }, 'checkpoints.response'])
            ->get();

        // 각 타임라인의 체크포인트 상태 업데이트
        foreach ($timelines as $timeline) {
            $timeline->updateCheckpointStatuses();
        }

        // 기존 대시보드용 데이터 (레거시 지원)
        $latestResponse = SurveyResponse::where('user_id', $user->id)
            ->latest()
            ->first();
        
        $previousResponse = null;
        $improvementRate = null;
        
        if ($latestResponse) {
            $previousResponse = SurveyResponse::where('user_id', $user->id)
                ->where('id', '<', $latestResponse->id)
                ->latest()
                ->first();
            
            if ($previousResponse && $previousResponse->total_score > 0) {
                $scoreDiff = $latestResponse->total_score - $previousResponse->total_score;
                $rate = round(($scoreDiff / $previousResponse->total_score) * 100, 1);
                
                $improvementRate = [
                    'rate' => abs($rate),
                    'absolute' => $scoreDiff,
                    'direction' => $scoreDiff < 0 ? 'up' : ($scoreDiff > 0 ? 'down' : 'same')
                ];
            }
        }

        // 카테고리별 분석 데이터
        $categoryAnalysis = [];
        if ($latestResponse) {
            $categoryScores = $latestResponse->getCategoryScores();
            if ($categoryScores && is_array($categoryScores)) {
                foreach ($categoryScores as $category) {
                    // name 키가 있는지 확인
                    $categoryName = '';
                    if (isset($category['name'])) {
                        $categoryName = $category['name'];
                    } elseif (isset($category[0]) && is_string($category[0])) {
                        // 배열의 첫 번째 요소가 이름일 수 있음
                        $categoryName = $category[0];
                    } else {
                        // 기본값
                        $categoryName = '카테고리';
                    }
                    
                    $categoryAnalysis[] = [
                        'name' => $categoryName,
                        'percentage' => 100 - ($category['percentage'] ?? 0), // 역전 (낮을수록 좋음)
                    ];
                }
            }
        }

        // 추가 필요한 변수들 (레거시 뷰 지원)
        $surveyScores = [];
        $recentResponses = SurveyResponse::where('user_id', $user->id)
            ->with('survey')
            ->latest()
            ->take(10)
            ->get();
        
        foreach ($recentResponses as $response) {
            $surveyName = '설문';
            if ($response->survey) {
                $surveyName = $response->survey->getTitle(session('locale', 'kor'));
            }
            
            $totalScore = $response->total_score ?? 0;
            $recoveryScore = 100 - $totalScore; // 회복 점수는 100에서 총점을 뺀 값
            
            $surveyScores[] = [
                'name' => $surveyName,
                'label' => $response->created_at->format('m/d'),
                'response_date' => $response->created_at,
                'score' => $recoveryScore, // 회복 점수 (백분율)
                'total_score' => $totalScore, // 원점수
                'recovery_score' => $recoveryScore // 회복 점수 (중복이지만 레거시 지원)
            ];
        }
        
        $recoveryTips = [
            'surveys' => []
        ];
        
        // 타임라인 데이터 (차트용)
        $timelineData = [
            'labels' => [],
            'scores' => [],
            'surveys' => []
        ];
        
        // 최근 응답들을 시간순으로 정렬하여 차트 데이터 생성
        $chartResponses = SurveyResponse::where('user_id', $user->id)
            ->with('survey')
            ->orderBy('created_at', 'asc')
            ->take(30) // 최근 30개
            ->get();
        
        foreach ($chartResponses as $response) {
            $date = $response->created_at->format('m/d');
            $recoveryScore = 100 - ($response->total_score ?? 0);
            
            if (!in_array($date, $timelineData['labels'])) {
                $timelineData['labels'][] = $date;
            }
            
            $timelineData['scores'][] = $recoveryScore;
            
            // 설문별 점수 추적
            if ($response->survey) {
                $surveyName = $response->survey->getTitle(session('locale', 'kor'));
                if (!isset($timelineData['surveys'][$surveyName])) {
                    $timelineData['surveys'][$surveyName] = [];
                }
                $timelineData['surveys'][$surveyName][] = $recoveryScore;
            }
        }

        return view('recovery-dashboard', compact(
            'timelines', 
            'latestResponse', 
            'previousResponse', 
            'improvementRate',
            'categoryAnalysis',
            'surveyScores',
            'recentResponses',
            'recoveryTips',
            'timelineData'
        ));
    }

    /**
     * 체크포인트 완료 처리
     */
    public function completeCheckpoint(Request $request, $checkpointId)
    {
        $checkpoint = TimelineCheckpoint::findOrFail($checkpointId);
        
        // 권한 확인
        if ($checkpoint->timeline->user_id !== Auth::id()) {
            abort(403);
        }

        // 최근 응답 가져오기
        $response = SurveyResponse::where('user_id', Auth::id())
            ->where('survey_id', $checkpoint->timeline->survey_id)
            ->latest()
            ->first();

        if (!$response) {
            return redirect()->route('surveys.show', [
                'survey' => $checkpoint->timeline->survey_id,
                'analysis_type' => 'detailed'
            ]);
        }

        // 체크포인트 완료 처리
        $checkpoint->markAsCompleted($response);

        return redirect()->route('recovery.dashboard')
            ->with('success', '체크포인트가 완료되었습니다.');
    }

    /**
     * 12주 체크 페이지 - 타임라인 일정 확인 및 관리
     */
    public function check()
    {
        $user = Auth::user();
        
        // 사용자의 모든 타임라인 가져오기
        $timelines = SurveyTimeline::where('user_id', $user->id)
            ->with(['survey', 'checkpoints' => function ($query) {
                $query->orderBy('week_number');
            }, 'checkpoints.response'])
            ->orderBy('status', 'asc') // active 먼저
            ->orderBy('created_at', 'desc')
            ->get();
        
        // 각 타임라인의 체크포인트 상태 업데이트
        foreach ($timelines as $timeline) {
            $timeline->updateCheckpointStatuses();
            
            // 다음 체크포인트 찾기
            $timeline->nextCheckpoint = $timeline->checkpoints
                ->whereIn('status', ['scheduled', 'ongoing'])
                ->first();
        }
        
        // 활성 타임라인과 완료/중단된 타임라인 분리
        $activeTimelines = $timelines->where('status', 'active');
        $inactiveTimelines = $timelines->whereIn('status', ['completed', 'abandoned']);
        
        // 사용 가능한 설문 목록 (새 타임라인 생성용)
        $availableSurveys = \App\Models\Survey::all();
        
        return view('recovery-check', compact(
            'activeTimelines', 
            'inactiveTimelines', 
            'availableSurveys'
        ));
    }

    /**
     * 새 타임라인 생성
     */
    public function createTimeline(Request $request)
    {
        $request->validate([
            'survey_id' => 'required|exists:surveys,id'
        ]);
        
        $user = Auth::user();
        
        // 동일한 설문의 활성 타임라인이 있는지 확인
        $existingTimeline = SurveyTimeline::where('user_id', $user->id)
            ->where('survey_id', $request->survey_id)
            ->where('status', 'active')
            ->first();
        
        if ($existingTimeline) {
            return redirect()->route('recovery.check')
                ->with('error', '이미 진행 중인 타임라인이 있습니다.');
        }
        
        // 초기 응답 생성 또는 가져오기
        $initialResponse = SurveyResponse::where('user_id', $user->id)
            ->where('survey_id', $request->survey_id)
            ->latest()
            ->first();
        
        if (!$initialResponse) {
            return redirect()->route('surveys.show', [
                'survey' => $request->survey_id,
                'start_timeline' => true
            ])->with('info', '타임라인을 시작하기 위해 먼저 설문을 완료해주세요.');
        }
        
        // 타임라인 생성
        $timeline = SurveyTimeline::create([
            'user_id' => $user->id,
            'survey_id' => $request->survey_id,
            'initial_response_id' => $initialResponse->id,
            'start_date' => now(),
            'end_date' => now()->addWeeks(12),
            'status' => 'active',
            'notes' => $request->notes
        ]);
        
        // 체크포인트 자동 생성
        $timeline->createCheckpoints();
        
        return redirect()->route('recovery.check')
            ->with('success', '12주 웰니스 프로그램이 시작되었습니다!');
    }

    /**
     * 타임라인 상태 변경
     */
    public function updateTimelineStatus(Request $request, $timelineId)
    {
        $timeline = SurveyTimeline::findOrFail($timelineId);
        
        // 권한 확인
        if ($timeline->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:active,completed,abandoned'
        ]);
        
        $timeline->update(['status' => $request->status]);
        
        $message = match($request->status) {
            'completed' => '타임라인이 완료되었습니다.',
            'abandoned' => '타임라인이 중단되었습니다.',
            'active' => '타임라인이 재활성화되었습니다.',
        };
        
        return redirect()->route('recovery.check')
            ->with('success', $message);
    }

    /**
     * 회복 이력 페이지
     */
    public function history()
    {
        $user = Auth::user();
        
        // 모든 타임라인 가져오기 (활성 및 완료)
        $timelines = SurveyTimeline::where('user_id', $user->id)
            ->with(['survey', 'checkpoints' => function ($query) {
                $query->orderBy('week_number');
            }, 'checkpoints.response'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // 모든 설문 응답 이력
        $responses = SurveyResponse::where('user_id', $user->id)
            ->with('survey')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('recovery-history', compact('timelines', 'responses'));
    }

    /**
     * 회복 점수 비교 페이지
     */
    public function compare(Request $request)
    {
        $user = Auth::user();
        
        if ($request->isMethod('post')) {
            $request->validate([
                'response_ids' => 'required|array|min:2|max:5',
                'response_ids.*' => 'exists:survey_responses,id'
            ]);
            
            $responses = SurveyResponse::whereIn('id', $request->response_ids)
                ->where('user_id', $user->id)
                ->with('survey')
                ->orderBy('created_at')
                ->get();
            
            // 비교 데이터 준비
            $comparisonData = [];
            foreach ($responses as $response) {
                $surveyName = $response->survey ? 
                    $response->survey->getTitle(session('locale', 'kor')) : 
                    '설문';
                
                $comparisonData[] = [
                    'id' => $response->id,
                    'survey_name' => $surveyName,
                    'date' => $response->created_at->format('Y-m-d'),
                    'total_score' => $response->total_score ?? 0,
                    'recovery_score' => 100 - ($response->total_score ?? 0),
                    'category_scores' => $response->getCategoryScores() ?? []
                ];
            }
            
            return view('recovery-compare', compact('comparisonData', 'responses'));
        }
        
        // GET 요청: 비교할 응답 선택 페이지
        $responses = SurveyResponse::where('user_id', $user->id)
            ->with('survey')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('recovery-compare-select', compact('responses'));
    }
}