<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SurveyTimeline;
use App\Models\TimelineCheckpoint;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 모든 기존 타임라인에 대해 누락된 체크포인트 추가
        $timelines = SurveyTimeline::all();
        
        foreach ($timelines as $timeline) {
            $existingWeeks = $timeline->checkpoints()->pluck('week_number')->toArray();
            $startDate = Carbon::parse($timeline->start_date);
            
            // 0~12주까지 모든 체크포인트 확인
            for ($week = 0; $week <= 12; $week++) {
                if (!in_array($week, $existingWeeks)) {
                    $scheduledDate = $startDate->copy()->addWeeks($week);
                    
                    // 날짜가 이미 지났는지 확인
                    $status = 'scheduled';
                    if ($scheduledDate->lt(now()->startOfDay())) {
                        $status = 'missed';
                    } elseif ($scheduledDate->eq(now()->startOfDay())) {
                        $status = 'ongoing';
                    }
                    
                    // 0주차는 initial_response가 있으면 completed
                    if ($week === 0 && $timeline->initial_response_id) {
                        $status = 'completed';
                    }
                    
                    $timeline->checkpoints()->create([
                        'week_number' => $week,
                        'scheduled_date' => $scheduledDate,
                        'status' => $status,
                        'response_id' => ($week === 0 && $timeline->initial_response_id) ? $timeline->initial_response_id : null,
                        'score' => null,
                        'category_scores' => null,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 기존 체크포인트 (0, 2, 4, 6, 8, 10, 12주) 외의 체크포인트 삭제
        $weekToKeep = [0, 2, 4, 6, 8, 10, 12];
        TimelineCheckpoint::whereNotIn('week_number', $weekToKeep)->delete();
    }
};
