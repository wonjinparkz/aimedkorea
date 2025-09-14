<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class TimelineCheckpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'timeline_id',
        'week_number',
        'scheduled_date',
        'completed_date',
        'status',
        'response_id',
        'score',
        'category_scores',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'category_scores' => 'array',
    ];

    /**
     * 관계 정의
     */
    public function timeline(): BelongsTo
    {
        return $this->belongsTo(SurveyTimeline::class, 'timeline_id');
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class, 'response_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(TimelineNotification::class, 'checkpoint_id');
    }

    /**
     * 체크포인트 완료 처리
     */
    public function markAsCompleted(SurveyResponse $response): void
    {
        $this->update([
            'status' => 'completed',
            'completed_date' => Carbon::now(),
            'response_id' => $response->id,
            'score' => $response->total_score,
            'category_scores' => $response->getCategoryScores(),
        ]);

        // 완료 알림 생성
        $this->timeline->notifications()->create([
            'checkpoint_id' => $this->id,
            'type' => 'completed',
            'scheduled_at' => Carbon::now(),
            'sent_at' => Carbon::now(),
            'is_sent' => true,
        ]);
    }

    /**
     * 상태별 칩 스타일 클래스
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'ongoing' => 'warning',
            'missed' => 'danger',
            'scheduled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * 상태별 라벨
     */
    public function getStatusLabelAttribute(): string
    {
        $currentLang = session('locale', 'kor');
        
        $labels = [
            'scheduled' => [
                'kor' => '예정',
                'eng' => 'Scheduled',
                'chn' => '预定',
                'hin' => 'निर्धारित',
                'arb' => 'مجدول'
            ],
            'ongoing' => [
                'kor' => '진행',
                'eng' => 'Ongoing',
                'chn' => '进行中',
                'hin' => 'चल रहा है',
                'arb' => 'جاري'
            ],
            'completed' => [
                'kor' => '완료',
                'eng' => 'Completed',
                'chn' => '已完成',
                'hin' => 'पूर्ण',
                'arb' => 'مكتمل'
            ],
            'missed' => [
                'kor' => '누락',
                'eng' => 'Missed',
                'chn' => '错过',
                'hin' => 'छूट गया',
                'arb' => 'مفقود'
            ],
        ];

        return $labels[$this->status][$currentLang] ?? $labels[$this->status]['kor'];
    }

    /**
     * 점수 변화 계산
     */
    public function getScoreChangeAttribute(): ?array
    {
        if (!$this->score || $this->week_number === 0) {
            return null;
        }

        // 이전 체크포인트 찾기
        $previousCheckpoint = $this->timeline->checkpoints()
            ->where('week_number', '<', $this->week_number)
            ->where('status', 'completed')
            ->orderBy('week_number', 'desc')
            ->first();

        if (!$previousCheckpoint || !$previousCheckpoint->score) {
            return null;
        }

        $change = $this->score - $previousCheckpoint->score;
        $percentageChange = $previousCheckpoint->score > 0 
            ? round(($change / $previousCheckpoint->score) * 100, 1)
            : 0;

        return [
            'previous_score' => $previousCheckpoint->score,
            'current_score' => $this->score,
            'change' => $change,
            'percentage' => $percentageChange,
            'improved' => $change < 0, // 점수가 낮을수록 좋음
        ];
    }

    /**
     * D-day 계산
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->status === 'completed' || $this->status === 'missed') {
            return null;
        }

        return Carbon::today()->diffInDays($this->scheduled_date, false);
    }

    /**
     * 체크포인트 라벨
     */
    public function getWeekLabelAttribute(): string
    {
        $currentLang = session('locale', 'kor');
        
        if ($this->week_number === 0) {
            $labels = [
                'kor' => '시작',
                'eng' => 'Start',
                'chn' => '开始',
                'hin' => 'शुरुआत',
                'arb' => 'البداية'
            ];
        } else {
            $labels = [
                'kor' => $this->week_number . '주차',
                'eng' => 'Week ' . $this->week_number,
                'chn' => '第' . $this->week_number . '周',
                'hin' => 'सप्ताह ' . $this->week_number,
                'arb' => 'الأسبوع ' . $this->week_number
            ];
        }

        return $labels[$currentLang] ?? $labels['kor'];
    }
}