<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class SurveyTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'survey_id',
        'initial_response_id',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * 관계 정의
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function initialResponse(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class, 'initial_response_id');
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(TimelineCheckpoint::class, 'timeline_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(TimelineNotification::class, 'timeline_id');
    }

    /**
     * 12주 타임라인 생성
     */
    public static function createForResponse(SurveyResponse $response): self
    {
        // 심층 분석 설문인지 확인
        if (!$response->survey->is_detailed || $response->analysis_type !== 'detailed') {
            throw new \Exception('타임라인은 심층 분석 설문에만 생성할 수 있습니다.');
        }

        // 이미 타임라인이 있는지 확인
        $existingTimeline = self::where('user_id', $response->user_id)
            ->where('survey_id', $response->survey_id)
            ->where('status', 'active')
            ->first();

        if ($existingTimeline) {
            return $existingTimeline;
        }

        // 타임라인 생성
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addWeeks(12);

        $timeline = self::create([
            'user_id' => $response->user_id,
            'survey_id' => $response->survey_id,
            'initial_response_id' => $response->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
        ]);

        // 체크포인트 생성 (0, 2, 4, 6, 8, 10, 12주)
        $weeks = [0, 2, 4, 6, 8, 10, 12];
        foreach ($weeks as $week) {
            $scheduledDate = $startDate->copy()->addWeeks($week);
            
            $checkpoint = $timeline->checkpoints()->create([
                'week_number' => $week,
                'scheduled_date' => $scheduledDate,
                'status' => $week === 0 ? 'completed' : 'scheduled',
                'response_id' => $week === 0 ? $response->id : null,
                'score' => $week === 0 ? $response->total_score : null,
                'category_scores' => $week === 0 ? $response->getCategoryScores() : null,
            ]);

            // 알림 설정 (0주차 제외)
            if ($week > 0) {
                // 3일 전 리마인더
                $timeline->notifications()->create([
                    'checkpoint_id' => $checkpoint->id,
                    'type' => 'reminder',
                    'scheduled_at' => $scheduledDate->copy()->subDays(3)->setTime(9, 0),
                ]);

                // 당일 리마인더
                $timeline->notifications()->create([
                    'checkpoint_id' => $checkpoint->id,
                    'type' => 'reminder',
                    'scheduled_at' => $scheduledDate->copy()->setTime(9, 0),
                ]);
            }
        }

        return $timeline;
    }

    /**
     * 현재 진행 상황 계산
     */
    public function getProgressAttribute(): array
    {
        $total = $this->checkpoints()->count();
        $completed = $this->checkpoints()->where('status', 'completed')->count();
        $missed = $this->checkpoints()->where('status', 'missed')->count();
        $ongoing = $this->checkpoints()->where('status', 'ongoing')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'missed' => $missed,
            'ongoing' => $ongoing,
            'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
        ];
    }

    /**
     * 다음 체크포인트 가져오기
     */
    public function getNextCheckpoint(): ?TimelineCheckpoint
    {
        return $this->checkpoints()
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->orderBy('scheduled_date')
            ->first();
    }

    /**
     * 현재 체크포인트 가져오기
     */
    public function getCurrentCheckpoint(): ?TimelineCheckpoint
    {
        $today = Carbon::today();
        
        // 진행 중인 체크포인트가 있으면 반환
        $ongoing = $this->checkpoints()->where('status', 'ongoing')->first();
        if ($ongoing) {
            return $ongoing;
        }

        // 오늘이 예정일인 체크포인트
        return $this->checkpoints()
            ->where('scheduled_date', $today)
            ->where('status', 'scheduled')
            ->first();
    }

    /**
     * 상태 업데이트 (매일 실행할 스케줄러에서 사용)
     */
    public function updateCheckpointStatuses(): void
    {
        $today = Carbon::today();

        foreach ($this->checkpoints as $checkpoint) {
            if ($checkpoint->status === 'scheduled') {
                // 예정일이 지났으면 누락으로 변경
                if ($checkpoint->scheduled_date->lt($today)) {
                    $checkpoint->update(['status' => 'missed']);
                }
                // 오늘이 예정일이면 진행 중으로 변경
                elseif ($checkpoint->scheduled_date->eq($today)) {
                    $checkpoint->update(['status' => 'ongoing']);
                }
            }
        }

        // 모든 체크포인트가 완료되었는지 확인
        if ($this->checkpoints()->whereIn('status', ['scheduled', 'ongoing'])->count() === 0) {
            $this->update(['status' => 'completed']);
        }
    }

    /**
     * 체크포인트 생성 메서드
     */
    public function createCheckpoints(): void
    {
        // 체크포인트 생성 (0~12주, 매주)
        $startDate = $this->start_date instanceof Carbon ? $this->start_date : Carbon::parse($this->start_date);
        
        for ($week = 0; $week <= 12; $week++) {
            $scheduledDate = $startDate->copy()->addWeeks($week);
            
            $checkpoint = $this->checkpoints()->create([
                'week_number' => $week,
                'scheduled_date' => $scheduledDate,
                'status' => $week === 0 ? 'completed' : 'scheduled',
                'response_id' => $week === 0 ? $this->initial_response_id : null,
                'score' => null,
                'category_scores' => null,
            ]);

            // 알림 설정 (0주차 제외)
            if ($week > 0) {
                // 3일 전 리마인더
                $this->notifications()->create([
                    'checkpoint_id' => $checkpoint->id,
                    'type' => 'reminder',
                    'scheduled_at' => $scheduledDate->copy()->subDays(3)->setTime(9, 0),
                ]);

                // 당일 리마인더
                $this->notifications()->create([
                    'checkpoint_id' => $checkpoint->id,
                    'type' => 'reminder',
                    'scheduled_at' => $scheduledDate->copy()->setTime(9, 0),
                ]);
            }
        }
    }
}