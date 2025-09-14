<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelineNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'timeline_id',
        'checkpoint_id',
        'type',
        'scheduled_at',
        'sent_at',
        'is_sent',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    /**
     * 관계 정의
     */
    public function timeline(): BelongsTo
    {
        return $this->belongsTo(SurveyTimeline::class, 'timeline_id');
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(TimelineCheckpoint::class, 'checkpoint_id');
    }

    /**
     * 알림 전송 처리
     */
    public function markAsSent(): void
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }

    /**
     * 알림 타입 라벨 가져오기
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'reminder' => '리마인더',
            'missed' => '누락 알림',
            'completed' => '완료 알림',
            default => $this->type,
        };
    }

    /**
     * 스코프: 전송되지 않은 알림
     */
    public function scopePending($query)
    {
        return $query->where('is_sent', false);
    }

    /**
     * 스코프: 예정된 알림
     */
    public function scopeScheduled($query)
    {
        return $query->where('scheduled_at', '<=', now())
                     ->where('is_sent', false);
    }
}