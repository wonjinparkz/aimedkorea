<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\SurveyTimeline;
use App\Models\TimelineCheckpoint;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

try {
    DB::beginTransaction();
    
    // Get test user
    $user = User::where('email', 'test@test.com')->first();
    
    if (!$user) {
        echo "Test user not found.\n";
        exit;
    }
    
    echo "Found test user: " . $user->email . " (ID: " . $user->id . ")\n\n";
    
    // Get detailed survey (id=8)
    $detailedSurvey = Survey::find(8);
    
    if (!$detailedSurvey) {
        echo "Detailed survey (ID: 8) not found.\n";
        exit;
    }
    
    echo "Found detailed survey: " . $detailedSurvey->getTitle('kor') . "\n";
    echo "Is detailed: " . ($detailedSurvey->is_detailed ? 'Yes' : 'No') . "\n\n";
    
    // Create responses for the detailed survey over the past 8 weeks
    echo "Creating responses for 12-week program...\n";
    
    $questions = $detailedSurvey->getQuestions('kor');
    $checklistItems = $detailedSurvey->getChecklistItems('kor');
    $frequencyItems = $detailedSurvey->getFrequencyItems('kor');
    
    // Week 0 - 초기 응답 (8주 전, 점수 높음 - 나쁜 상태)
    $week0Date = Carbon::now()->subWeeks(8);
    $responses0 = [];
    $totalScore0 = 0;
    
    foreach ($questions as $qIndex => $question) {
        $score = rand(2, 3); // 높은 점수 (나쁜 상태)
        $maxScore = min(3, count($checklistItems) - 1);
        $score = min($score, $maxScore);
        
        $responses0[$qIndex] = [
            'checklist_type' => 'default',
            'question_label' => $question['label'],
            'selected_label' => $checklistItems[$score]['label'] ?? 'Unknown',
            'selected_score' => $score,
            'question_original_index' => $qIndex,
        ];
        
        // Add frequency response for detailed analysis
        if ($detailedSurvey->is_detailed && !empty($frequencyItems)) {
            $freqScore = rand(2, min(3, count($frequencyItems) - 1));
            $responses0[$qIndex]['frequency_response'] = [
                'selected_label' => $frequencyItems[$freqScore]['label'] ?? 'Unknown',
                'selected_score' => $freqScore,
            ];
        }
        
        $totalScore0 += $score;
    }
    
    $initialResponse = SurveyResponse::create([
        'survey_id' => $detailedSurvey->id,
        'user_id' => $user->id,
        'responses_data' => $responses0,
        'total_score' => $totalScore0,
        'recovery_score' => 100 - min(100, $totalScore0),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Mozilla/5.0',
        'analysis_type' => 'detailed',
        'created_at' => $week0Date,
        'updated_at' => $week0Date,
    ]);
    
    echo "  Week 0 (Initial): Score = $totalScore0, Recovery = " . (100 - $totalScore0) . "%\n";
    
    // Create timeline starting 8 weeks ago
    $timeline = SurveyTimeline::create([
        'user_id' => $user->id,
        'survey_id' => $detailedSurvey->id,
        'initial_response_id' => $initialResponse->id,
        'start_date' => $week0Date->toDateString(),
        'end_date' => $week0Date->copy()->addWeeks(12)->toDateString(),
        'status' => 'active',
        'notes' => '12주 심화 분석 프로그램 - 진행 중',
        'created_at' => $week0Date,
        'updated_at' => Carbon::now(),
    ]);
    
    echo "\n12-week timeline created (ID: " . $timeline->id . ")\n\n";
    
    // Create checkpoints and responses for each week
    $checkpointData = [
        0 => ['status' => 'completed', 'score_range' => [60, 70], 'completed' => true],
        1 => ['status' => 'completed', 'score_range' => [58, 68], 'completed' => true],
        2 => ['status' => 'completed', 'score_range' => [55, 65], 'completed' => true],
        3 => ['status' => 'missed', 'score_range' => null, 'completed' => false], // 누락
        4 => ['status' => 'completed', 'score_range' => [50, 60], 'completed' => true],
        5 => ['status' => 'completed', 'score_range' => [48, 58], 'completed' => true],
        6 => ['status' => 'missed', 'score_range' => null, 'completed' => false], // 누락
        7 => ['status' => 'completed', 'score_range' => [45, 55], 'completed' => true],
        8 => ['status' => 'ongoing', 'score_range' => null, 'completed' => false], // 현재 진행 중
        9 => ['status' => 'scheduled', 'score_range' => null, 'completed' => false],
        10 => ['status' => 'scheduled', 'score_range' => null, 'completed' => false],
        11 => ['status' => 'scheduled', 'score_range' => null, 'completed' => false],
        12 => ['status' => 'scheduled', 'score_range' => null, 'completed' => false],
    ];
    
    foreach ($checkpointData as $week => $data) {
        $checkpointDate = $week0Date->copy()->addWeeks($week);
        $responseId = null;
        
        // Create response if completed
        if ($data['completed'] && $data['score_range']) {
            $responses = [];
            $totalScore = 0;
            $targetScore = rand($data['score_range'][0], $data['score_range'][1]);
            
            foreach ($questions as $qIndex => $question) {
                $avgScore = $targetScore / count($questions);
                $score = max(0, min(count($checklistItems) - 1, floor($avgScore) + rand(-1, 1)));
                
                $responses[$qIndex] = [
                    'checklist_type' => 'default',
                    'question_label' => $question['label'],
                    'selected_label' => $checklistItems[$score]['label'] ?? 'Unknown',
                    'selected_score' => $score,
                    'question_original_index' => $qIndex,
                ];
                
                // Add frequency response
                if ($detailedSurvey->is_detailed && !empty($frequencyItems)) {
                    $freqScore = max(0, min(count($frequencyItems) - 1, $score + rand(-1, 0)));
                    $responses[$qIndex]['frequency_response'] = [
                        'selected_label' => $frequencyItems[$freqScore]['label'] ?? 'Unknown',
                        'selected_score' => $freqScore,
                    ];
                }
                
                $totalScore += $score;
            }
            
            $response = SurveyResponse::create([
                'survey_id' => $detailedSurvey->id,
                'user_id' => $user->id,
                'responses_data' => $responses,
                'total_score' => $totalScore,
                'recovery_score' => 100 - min(100, $totalScore),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0',
                'analysis_type' => 'detailed',
                'created_at' => $checkpointDate,
                'updated_at' => $checkpointDate,
            ]);
            
            $responseId = $response->id;
            
            echo "  Week $week: Score = $totalScore, Recovery = " . (100 - $totalScore) . "% [" . $data['status'] . "]\n";
        } elseif ($week == 0) {
            // Use initial response for week 0
            $responseId = $initialResponse->id;
        } else {
            echo "  Week $week: No response [" . $data['status'] . "]\n";
        }
        
        // Create checkpoint
        TimelineCheckpoint::create([
            'timeline_id' => $timeline->id,
            'week_number' => $week,
            'scheduled_date' => $checkpointDate,
            'status' => $data['status'],
            'response_id' => $responseId,
            'completed_date' => $data['completed'] ? $checkpointDate : null,
            'created_at' => $checkpointDate,
            'updated_at' => $data['status'] == 'completed' ? $checkpointDate : Carbon::now(),
        ]);
    }
    
    DB::commit();
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "SUMMARY\n";
    echo str_repeat("=", 50) . "\n";
    
    // Get statistics
    $totalCheckpoints = TimelineCheckpoint::where('timeline_id', $timeline->id)->count();
    $completedCheckpoints = TimelineCheckpoint::where('timeline_id', $timeline->id)
        ->where('status', 'completed')->count();
    $missedCheckpoints = TimelineCheckpoint::where('timeline_id', $timeline->id)
        ->where('status', 'missed')->count();
    
    $surveyResponses = SurveyResponse::where('user_id', $user->id)
        ->where('survey_id', $detailedSurvey->id)
        ->count();
    
    $avgRecoveryScore = SurveyResponse::where('user_id', $user->id)
        ->where('survey_id', $detailedSurvey->id)
        ->avg('recovery_score');
    
    echo "Timeline ID: " . $timeline->id . "\n";
    echo "Survey: " . $detailedSurvey->getTitle('kor') . " (ID: 8)\n";
    echo "Duration: " . $timeline->start_date->format('Y-m-d') . " to " . $timeline->end_date->format('Y-m-d') . "\n";
    echo "Status: " . $timeline->status . "\n\n";
    
    echo "Checkpoints:\n";
    echo "  - Total: $totalCheckpoints\n";
    echo "  - Completed: $completedCheckpoints\n";
    echo "  - Missed: $missedCheckpoints\n";
    echo "  - Progress: " . round(($completedCheckpoints / $totalCheckpoints) * 100) . "%\n\n";
    
    echo "Survey Responses:\n";
    echo "  - Total for this survey: $surveyResponses\n";
    echo "  - Average recovery score: " . round($avgRecoveryScore, 1) . "%\n";
    
    echo "\n✅ Detailed timeline with dummy data created successfully!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}