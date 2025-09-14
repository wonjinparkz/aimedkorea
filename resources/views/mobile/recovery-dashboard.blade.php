@extends('layouts.app-mobile')

@section('content')
<div class="mobile-recovery-dashboard">
    <!-- App Header -->
    <div class="app-header">
        <a href="{{ route('surveys.index') }}" class="back-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="header-title">{{ __('회복 대시보드') }}</h1>
        <a href="{{ route('recovery.history') }}" class="header-action">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </a>
    </div>

    <!-- Recovery Score Overview -->
    @if($latestResponse)
    <div class="recovery-score-card">
        <div class="score-header">
            <h2 class="score-title">{{ __('현재 회복 점수') }}</h2>
            <span class="score-date">{{ $latestResponse->created_at->format('Y.m.d') }}</span>
        </div>
        
        <div class="score-display">
            <div class="score-circle-container">
                <svg class="score-circle" viewBox="0 0 200 200">
                    <circle cx="100" cy="100" r="90" fill="none" stroke="#e5e7eb" stroke-width="12"/>
                    <circle cx="100" cy="100" r="90" fill="none" 
                            stroke="{{ $latestResponse->total_score <= 30 ? '#10b981' : ($latestResponse->total_score <= 60 ? '#f59e0b' : '#ef4444') }}"
                            stroke-width="12"
                            stroke-dasharray="{{ (100 - $latestResponse->total_score) * 5.65 }} 565"
                            stroke-dashoffset="0"
                            transform="rotate(-90 100 100)"
                            class="score-progress"/>
                </svg>
                <div class="score-value">
                    <span class="score-number">{{ 100 - $latestResponse->total_score }}</span>
                    <span class="score-label">점</span>
                </div>
            </div>
            
            @if($improvementRate)
            <div class="improvement-indicator {{ $improvementRate['direction'] }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($improvementRate['direction'] == 'up')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    @elseif($improvementRate['direction'] == 'down')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                    @endif
                </svg>
                <span>{{ $improvementRate['rate'] }}%</span>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="empty-state-card">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-gray-600 text-center">아직 설문 응답이 없습니다</p>
        <a href="{{ route('surveys.index') }}" class="btn-primary mt-4">설문 시작하기</a>
    </div>
    @endif

    <!-- Active Timelines -->
    @if($timelines->count() > 0)
    <div class="timelines-section">
        <h2 class="section-title">{{ __('진행 중인 프로그램') }}</h2>
        <div class="timeline-cards">
            @foreach($timelines as $timeline)
            <div class="timeline-card">
                <div class="timeline-header">
                    <h3 class="timeline-title">{{ $timeline->survey->getTitle(session('locale', 'kor')) }}</h3>
                    <span class="timeline-badge">{{ $timeline->getProgressPercentage() }}%</span>
                </div>
                
                <div class="timeline-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $timeline->getProgressPercentage() }}%"></div>
                    </div>
                    <div class="progress-info">
                        <span>{{ $timeline->getCompletedCheckpointsCount() }}/{{ $timeline->checkpoints->count() }} 완료</span>
                        <span>{{ $timeline->getRemainingDays() }}일 남음</span>
                    </div>
                </div>
                
                <!-- Next Checkpoint -->
                @php
                    $nextCheckpoint = $timeline->checkpoints->where('status', 'scheduled')->first();
                @endphp
                @if($nextCheckpoint)
                <div class="next-checkpoint">
                    <div class="checkpoint-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="checkpoint-info">
                        <p class="checkpoint-label">다음 체크포인트</p>
                        <p class="checkpoint-date">{{ $nextCheckpoint->scheduled_date->format('m월 d일') }}</p>
                    </div>
                    <a href="{{ route('surveys.show', ['survey' => $timeline->survey_id, 'analysis_type' => 'detailed']) }}" class="checkpoint-action">
                        시작
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Category Analysis -->
    @if(!empty($categoryAnalysis))
    <div class="category-section">
        <h2 class="section-title">{{ __('카테고리별 분석') }}</h2>
        <div class="category-grid">
            @foreach($categoryAnalysis as $category)
            <div class="category-item">
                <div class="category-label">{{ $category['name'] }}</div>
                <div class="category-bar">
                    <div class="category-fill" style="width: {{ $category['percentage'] }}%; background: {{ $category['percentage'] >= 70 ? '#10b981' : ($category['percentage'] >= 40 ? '#f59e0b' : '#ef4444') }}"></div>
                </div>
                <div class="category-value">{{ round($category['percentage']) }}%</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recovery Trend Chart -->
    @if(count($timelineData['scores']) > 0)
    <div class="trend-section">
        <h2 class="section-title">{{ __('회복 추세') }}</h2>
        <div class="trend-chart-container">
            <canvas id="recoveryTrendChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="{{ route('surveys.index') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #667eea, #764ba2)">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span>새 설문</span>
        </a>
        
        <a href="{{ route('recovery.check') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c)">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <span>12주 체크</span>
        </a>
        
        <a href="{{ route('recovery.history') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #fa709a, #fee140)">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span>이력</span>
        </a>
        
        <a href="{{ route('recovery.compare') }}" class="action-card">
            <div class="action-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe)">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <span>비교</span>
        </a>
    </div>
</div>

<style>
/* Mobile Recovery Dashboard Styles */
.mobile-recovery-dashboard {
    min-height: 100vh;
    background: #f3f4f6;
    padding-bottom: 80px;
}

/* App Header */
.app-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background: white;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 50;
}

.back-button, .header-action {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f3f4f6;
    color: #374151;
    text-decoration: none;
}

.header-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

/* Recovery Score Card */
.recovery-score-card {
    background: white;
    margin: 16px;
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.score-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.score-title {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
}

.score-date {
    font-size: 14px;
    color: #9ca3af;
}

.score-display {
    position: relative;
}

.score-circle-container {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto;
}

.score-circle {
    width: 100%;
    height: 100%;
}

.score-progress {
    transition: stroke-dasharray 1s ease-in-out;
}

.score-value {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.score-number {
    font-size: 48px;
    font-weight: 700;
    color: #111827;
}

.score-label {
    font-size: 16px;
    color: #6b7280;
    margin-left: 4px;
}

.improvement-indicator {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.improvement-indicator.up {
    background: #d1fae5;
    color: #065f46;
}

.improvement-indicator.down {
    background: #fee2e2;
    color: #991b1b;
}

.improvement-indicator.same {
    background: #f3f4f6;
    color: #6b7280;
}

/* Empty State */
.empty-state-card {
    background: white;
    margin: 16px;
    padding: 40px 20px;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.btn-primary {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-weight: 600;
    border-radius: 12px;
    text-decoration: none;
    text-align: center;
}

/* Timelines Section */
.timelines-section {
    padding: 0 16px;
    margin-top: 24px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 16px;
}

.timeline-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.timeline-card {
    background: white;
    padding: 16px;
    border-radius: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.timeline-title {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
}

.timeline-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
}

.timeline-progress {
    margin-bottom: 16px;
}

.progress-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 8px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 4px;
    transition: width 0.5s ease-out;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #6b7280;
}

.next-checkpoint {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 12px;
}

.checkpoint-icon {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
}

.checkpoint-info {
    flex: 1;
}

.checkpoint-label {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 2px;
}

.checkpoint-date {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.checkpoint-action {
    padding: 8px 16px;
    background: #667eea;
    color: white;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
}

/* Category Section */
.category-section {
    padding: 24px 16px;
}

.category-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.category-item {
    background: white;
    padding: 12px;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.category-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
}

.category-bar {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 8px;
}

.category-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s ease-out;
}

.category-value {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
}

/* Trend Section */
.trend-section {
    padding: 0 16px 24px;
}

.trend-chart-container {
    background: white;
    padding: 16px;
    border-radius: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    height: 200px;
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    padding: 24px 16px;
    background: white;
    border-top: 1px solid #e5e7eb;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-card span {
    font-size: 12px;
    color: #374151;
    font-weight: 500;
}

/* Animations */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.recovery-score-card,
.timeline-card,
.category-item {
    animation: slideUp 0.5s ease-out;
}
</style>

@if(count($timelineData['scores']) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('recoveryTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($timelineData['labels']),
            datasets: [{
                label: '회복 점수',
                data: @json($timelineData['scores']),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection