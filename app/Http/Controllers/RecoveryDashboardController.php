<?php

namespace App\Http\Controllers;

use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecoveryDashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        
        // 최신 응답 가져오기 (survey 관계 포함)
        $latestResponse = SurveyResponse::where('user_id', $user->id)
            ->with('survey')
            ->latest()
            ->first();

        if (!$latestResponse) {
            return redirect()->route('surveys.index')
                ->with('info', '회복 점수를 확인하려면 먼저 자가 진단을 완료해주세요.');
        }

        // 모든 응답 데이터 가져오기 (survey 관계 포함) - 테스트를 위해 30일 제한 제거
        $recentResponses = SurveyResponse::where('user_id', $user->id)
            ->with('survey')
            ->orderBy('created_at')
            ->get();

        // 이전 응답 가져오기 (비교용)
        $previousResponse = SurveyResponse::where('user_id', $user->id)
            ->where('id', '<', $latestResponse->id)
            ->latest()
            ->first();

        // 회복 점수 계산 및 업데이트
        $this->calculateRecoveryScore($latestResponse);

        // survey 관계 로드 확인
        $latestResponse->load('survey');

        // 설문별 점수 분석 데이터
        $surveyScores = $this->getSurveyScores($user->id);
        
        // 디버깅: 설문별 데이터 확인
        \Log::info('Survey scores: ' . json_encode($surveyScores));

        // 시간별 추이 데이터 준비
        $timelineData = $this->prepareTimelineData($recentResponses);
        
        // 디버깅: 응답 수 확인
        \Log::info('Recent responses count: ' . $recentResponses->count());
        \Log::info('Timeline data: ' . json_encode($timelineData));

        // 개선율 계산
        $improvementRate = $this->calculateImprovementRate($latestResponse, $previousResponse);

        // 회복 제안 생성
        $recoveryTips = $this->generateRecoveryTips($latestResponse, $surveyScores);

        // 마지막 조회 시간 업데이트
        $latestResponse->update(['last_viewed_at' => now()]);

        return view('recovery-dashboard', compact(
            'latestResponse',
            'previousResponse',
            'surveyScores',
            'timelineData',
            'improvementRate',
            'recoveryTips',
            'recentResponses'
        ));
    }

    private function calculateRecoveryScore(SurveyResponse $response)
    {
        // survey 관계 로드 확인
        if (!$response->relationLoaded('survey')) {
            $response->load('survey');
        }
        
        // 기존 total_score를 역전하여 회복 점수 계산
        // 낮은 점수가 좋은 상태이므로, 높은 회복 점수로 변환
        $questions = $response->survey->questions ?? [];
        $maxPossibleScore = count($questions) * 4;
        
        if ($maxPossibleScore == 0) {
            $recoveryScore = 0;
        } else {
            $recoveryScore = 100 - round(($response->total_score / $maxPossibleScore) * 100);
        }
        
        $response->update([
            'recovery_score' => $recoveryScore,
            'recovery_data' => [
                'calculated_at' => now()->toDateTimeString(),
                'max_possible_score' => $maxPossibleScore,
                'raw_score' => $response->total_score
            ]
        ]);

        return $recoveryScore;
    }

    private function getSurveyScores($userId)
    {
        // 사용자의 모든 고유한 설문 가져오기
        $surveyIds = SurveyResponse::where('user_id', $userId)
            ->distinct()
            ->pluck('survey_id');
        
        $surveyScores = [];
        
        foreach ($surveyIds as $surveyId) {
            // 각 설문의 최신 응답 가져오기
            $latestResponse = SurveyResponse::where('user_id', $userId)
                ->where('survey_id', $surveyId)
                ->with('survey')
                ->latest()
                ->first();
            
            if ($latestResponse && $latestResponse->survey) {
                // 제목 정리 (뒤의 불필요한 텍스트 제거)
                $title = $latestResponse->survey->title;
                // 괄호와 그 안의 내용 제거
                $title = preg_replace('/\([^)]*\)/', '', $title);
                // 셀프 테스트, 자가분석, 셀프 체크 제거
                $title = preg_replace('/(셀프\s*테스트|자가분석|셀프\s*체크)/u', '', $title);
                $title = trim($title);
                
                // 회복 점수 계산
                if (!$latestResponse->recovery_score) {
                    $this->calculateRecoveryScore($latestResponse);
                }
                
                $surveyScores[] = [
                    'id' => $surveyId,
                    'name' => $title,
                    'full_name' => $latestResponse->survey->title,
                    'score' => $latestResponse->recovery_score ?? 0,
                    'total_score' => $latestResponse->total_score,
                    'response_date' => $latestResponse->created_at,
                    'response_id' => $latestResponse->id
                ];
            }
        }
        
        // 점수 순으로 정렬 (높은 점수가 먼저)
        usort($surveyScores, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        return $surveyScores;
    }

    private function prepareTimelineData($responses)
    {
        $timelineData = [
            'labels' => [],
            'scores' => [],
            'surveys' => []
        ];

        // 설문별로 그룹화
        $groupedBySurvey = [];
        
        foreach ($responses as $response) {
            // survey 관계 로드
            $response->load('survey');
            
            // 회복 점수가 없으면 계산
            if (!$response->recovery_score) {
                $this->calculateRecoveryScore($response);
            }

            $surveyId = $response->survey_id;
            if (!isset($groupedBySurvey[$surveyId])) {
                $groupedBySurvey[$surveyId] = [
                    'name' => $response->survey->title,
                    'dates' => [],
                    'scores' => []
                ];
            }
            
            $groupedBySurvey[$surveyId]['dates'][] = $response->created_at->format('m/d');
            $groupedBySurvey[$surveyId]['scores'][] = $response->recovery_score;
        }

        // 모든 날짜 수집 (중복 제거)
        $allDates = [];
        foreach ($responses as $response) {
            $date = $response->created_at->format('m/d');
            if (!in_array($date, $allDates)) {
                $allDates[] = $date;
            }
        }
        
        $timelineData['labels'] = $allDates;
        
        // 종합 점수 계산
        foreach ($responses as $response) {
            $timelineData['scores'][] = $response->recovery_score;
        }
        
        // 설문별 데이터 정리
        foreach ($groupedBySurvey as $surveyId => $data) {
            // 제목 정리
            $title = $data['name'];
            // 괄호와 그 안의 내용 제거
            $title = preg_replace('/\([^)]*\)/', '', $title);
            // 셀프 테스트, 자가분석, 셀프 체크 제거
            $title = preg_replace('/(셀프\s*테스트|자가분석|셀프\s*체크)/u', '', $title);
            $title = trim($title);
            
            $timelineData['surveys'][$title] = $data['scores'];
        }

        return $timelineData;
    }

    private function calculateImprovementRate($current, $previous)
    {
        if (!$previous || !$current->recovery_score || !$previous->recovery_score) {
            return null;
        }

        $improvement = $current->recovery_score - $previous->recovery_score;
        $rate = round(($improvement / $previous->recovery_score) * 100, 1);

        return [
            'rate' => $rate,
            'absolute' => $improvement,
            'direction' => $improvement > 0 ? 'up' : ($improvement < 0 ? 'down' : 'same')
        ];
    }

    private function generateRecoveryTips($response, $surveyScores)
    {
        $tips = [];
        
        // 전체 점수 기반 팁
        $recoveryScore = $response->recovery_score ?? 0;
        
        if ($recoveryScore < 30) {
            $tips['overall'] = [
                'level' => 'critical',
                'message' => '즉각적인 전문가 상담이 필요합니다.',
                'actions' => [
                    '의료 전문가와 상담 예약하기',
                    '긴급 지원 서비스 연락처 확인하기',
                    '가족이나 친구에게 도움 요청하기'
                ]
            ];
        } elseif ($recoveryScore < 50) {
            $tips['overall'] = [
                'level' => 'warning',
                'message' => '주의가 필요한 상태입니다.',
                'actions' => [
                    '규칙적인 생활 패턴 만들기',
                    '스트레스 관리 기법 학습하기',
                    '전문가 상담 고려하기'
                ]
            ];
        } elseif ($recoveryScore < 70) {
            $tips['overall'] = [
                'level' => 'moderate',
                'message' => '양호한 상태이나 개선의 여지가 있습니다.',
                'actions' => [
                    '현재 좋은 습관 유지하기',
                    '약한 부분 집중 개선하기',
                    '정기적인 자가 진단 실시하기'
                ]
            ];
        } else {
            $tips['overall'] = [
                'level' => 'good',
                'message' => '우수한 회복 상태를 유지하고 있습니다.',
                'actions' => [
                    '현재 라이프스타일 유지하기',
                    '다른 사람들과 경험 공유하기',
                    '지속적인 모니터링 계속하기'
                ]
            ];
        }

        // 설문별 팁
        foreach ($surveyScores as $survey) {
            if ($survey['score'] < 50) {
                $tips['surveys'][$survey['name']] = [
                    'score' => $survey['score'],
                    'status' => 'needs_improvement',
                    'tip' => $this->getSurveySpecificTip($survey['name'], $survey['score'])
                ];
            }
        }

        return $tips;
    }

    private function getSurveySpecificTip($surveyName, $score)
    {
        // 설문 이름에 기반한 팁 제공
        $level = $score < 30 ? 'low' : 'medium';
        
        // 기본 팁
        $defaultTips = [
            'low' => '이 영역에서 집중적인 개선이 필요합니다. 전문가 상담을 고려해보세요.',
            'medium' => '꾸준한 관리와 노력으로 개선할 수 있는 상태입니다.'
        ];
        
        // 키워드 기반 맞춤 팁
        if (stripos($surveyName, '우울') !== false || stripos($surveyName, '기분') !== false) {
            return $level === 'low' 
                ? '정서적 지원이 필요합니다. 상담 전문가와 대화를 나누어보세요.'
                : '규칙적인 운동과 충분한 휴식으로 기분을 개선해보세요.';
        } elseif (stripos($surveyName, '불안') !== false || stripos($surveyName, '스트레스') !== false) {
            return $level === 'low'
                ? '스트레스 관리가 시급합니다. 명상이나 요가를 시작해보세요.'
                : '심호흡 운동이나 가벼운 산책으로 마음을 진정시켜보세요.';
        } elseif (stripos($surveyName, '수면') !== false || stripos($surveyName, '불면') !== false) {
            return $level === 'low'
                ? '수면 패턴 개선이 필요합니다. 수면 위생을 점검해보세요.'
                : '규칙적인 수면 시간을 유지하고 카페인 섭취를 줄여보세요.';
        } elseif (stripos($surveyName, '인지') !== false || stripos($surveyName, '기억') !== false) {
            return $level === 'low'
                ? '인지 기능 향상을 위한 두뇌 훈련을 시작해보세요.'
                : '독서나 퍼즐 같은 두뇌 활동을 일상에 추가해보세요.';
        }
        
        return $defaultTips[$level];
    }

    public function history()
    {
        $user = Auth::user();
        
        $responses = SurveyResponse::where('user_id', $user->id)
            ->with('survey')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // 각 응답에 대해 회복 점수 확인 및 계산
        foreach ($responses as $response) {
            if (!$response->recovery_score) {
                $this->calculateRecoveryScore($response);
            }
        }

        return view('recovery-dashboard.history', compact('responses'));
    }

    public function compare(Request $request)
    {
        $user = Auth::user();
        
        $responseIds = $request->input('responses', []);
        
        // GET 요청이고 파라미터가 없는 경우 히스토리로 리다이렉트
        if ($request->isMethod('get') && empty($responseIds)) {
            return redirect()->route('recovery.history')
                ->with('info', '비교할 진단 결과를 선택해주세요.');
        }
        
        if (count($responseIds) < 2) {
            return redirect()->route('recovery.history')
                ->with('error', '비교하려면 최소 2개의 결과를 선택해주세요.');
        }

        $responses = SurveyResponse::where('user_id', $user->id)
            ->whereIn('id', $responseIds)
            ->with('survey')
            ->get();

        // 각 응답의 설문 정보 계산
        $comparisonData = [];
        foreach ($responses as $response) {
            if (!$response->recovery_score) {
                $this->calculateRecoveryScore($response);
            }

            // 설문 제목 정리
            $title = $response->survey->title;
            $cleanTitle = preg_replace('/(셀프\s*테스트|자가분석|셀프\s*체크)$/u', '', $title);
            $cleanTitle = trim($cleanTitle);

            $comparisonData[] = [
                'response' => $response,
                'survey_name' => $cleanTitle,
                'survey_full_name' => $title,
                'date' => $response->created_at->format('Y-m-d')
            ];
        }

        return view('recovery-dashboard.compare', compact('comparisonData'));
    }
}