@extends('layouts.app-mobile')

@section('content')
<div class="mobile-recovery-compare">
    <!-- App Header -->
    <div class="app-header">
        <a href="{{ route('recovery.compare') }}" class="back-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="header-title">{{ __('비교 결과') }}</h1>
        <div class="header-action"></div>
    </div>

    <!-- Comparison Chart -->
    <div class="comparison-chart-section">
        <h2 class="section-title">회복 점수 비교</h2>
        <div class="chart-container">
            <canvas id="comparisonChart"></canvas>
        </div>
    </div>

    <!-- Comparison Cards -->
    <div class="comparison-cards">
        @foreach($comparisonData as $index => $data)
        <div class="comparison-card">
            <div class="card-header">
                <div class="card-number">{{ $index + 1 }}</div>
                <div class="card-info">
                    <h3 class="card-title">{{ $data['survey_name'] }}</h3>
                    <p class="card-date">{{ $data['date'] }}</p>
                </div>
                <div class="card-score">
                    <span class="score-value">{{ $data['recovery_score'] }}</span>
                    <span class="score-label">점</span>
                </div>
            </div>
            
            <!-- Category Scores -->
            @if(!empty($data['category_scores']))
            <div class="category-comparison">
                @foreach($data['category_scores'] as $category)
                <div class="category-row">
                    <span class="category-name">{{ $category['name'] }}</span>
                    <div class="category-bar">
                        <div class="category-fill" 
                             style="width: {{ 100 - $category['percentage'] }}%; 
                                    background: {{ (100 - $category['percentage']) >= 70 ? '#10b981' : ((100 - $category['percentage']) >= 40 ? '#f59e0b' : '#ef4444') }}">
                        </div>
                    </div>
                    <span class="category-value">{{ round(100 - $category['percentage']) }}%</span>
                </div>
                @endforeach
            </div>
            @endif
            
            <!-- View Details Link -->
            <a href="{{ route('surveys.results', ['survey' => $responses[$index]->survey_id, 'response' => $responses[$index]->id]) }}" class="view-details-link">
                상세보기
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Improvement Analysis -->
    @if(count($comparisonData) >= 2)
    <div class="improvement-section">
        <h2 class="section-title">개선도 분석</h2>
        <div class="improvement-card">
            @php
                $firstScore = $comparisonData[0]['recovery_score'];
                $lastScore = $comparisonData[count($comparisonData) - 1]['recovery_score'];
                $improvement = $lastScore - $firstScore;
                $improvementRate = $firstScore > 0 ? round(($improvement / $firstScore) * 100, 1) : 0;
            @endphp
            
            <div class="improvement-stat">
                <div class="stat-icon {{ $improvement > 0 ? 'positive' : ($improvement < 0 ? 'negative' : 'neutral') }}">
                    @if($improvement > 0)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    @elseif($improvement < 0)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                    @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                    </svg>
                    @endif
                </div>
                
                <div class="stat-content">
                    <p class="stat-label">전체 개선도</p>
                    <p class="stat-value">{{ abs($improvement) }}점 {{ $improvement > 0 ? '상승' : ($improvement < 0 ? '하락' : '유지') }}</p>
                    <p class="stat-rate">{{ abs($improvementRate) }}%</p>
                </div>
            </div>
            
            <div class="improvement-timeline">
                <div class="timeline-start">
                    <span class="timeline-label">처음</span>
                    <span class="timeline-score">{{ $firstScore }}점</span>
                </div>
                <div class="timeline-arrow">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </div>
                <div class="timeline-end">
                    <span class="timeline-label">최근</span>
                    <span class="timeline-score">{{ $lastScore }}점</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('recovery.compare') }}" class="btn-secondary">
            다시 선택
        </a>
        <a href="{{ route('surveys.index') }}" class="btn-primary">
            새 설문 시작
        </a>
    </div>
</div>

<style>
/* Mobile Recovery Compare Styles */
.mobile-recovery-compare {
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

.back-button {
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

.header-action {
    width: 40px;
}

/* Comparison Chart Section */
.comparison-chart-section {
    padding: 24px 16px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 16px;
}

.chart-container {
    background: white;
    padding: 16px;
    border-radius: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    height: 250px;
}

/* Comparison Cards */
.comparison-cards {
    padding: 0 16px;
}

.comparison-card {
    background: white;
    padding: 16px;
    border-radius: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.card-number {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.card-info {
    flex: 1;
}

.card-title {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 2px;
}

.card-date {
    font-size: 13px;
    color: #6b7280;
}

.card-score {
    text-align: right;
}

.score-value {
    font-size: 24px;
    font-weight: 700;
    color: #667eea;
}

.score-label {
    font-size: 14px;
    color: #6b7280;
    margin-left: 2px;
}

/* Category Comparison */
.category-comparison {
    padding: 12px;
    background: #f9fafb;
    border-radius: 12px;
    margin-bottom: 12px;
}

.category-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.category-row:last-child {
    margin-bottom: 0;
}

.category-name {
    font-size: 12px;
    color: #6b7280;
    min-width: 60px;
}

.category-bar {
    flex: 1;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.category-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s ease-out;
}

.category-value {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    min-width: 35px;
    text-align: right;
}

.view-details-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 8px;
    background: #f3f4f6;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
}

/* Improvement Section */
.improvement-section {
    padding: 24px 16px;
}

.improvement-card {
    background: white;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.improvement-stat {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon.positive {
    background: #d1fae5;
    color: #065f46;
}

.stat-icon.negative {
    background: #fee2e2;
    color: #991b1b;
}

.stat-icon.neutral {
    background: #f3f4f6;
    color: #6b7280;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 4px;
}

.stat-value {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.stat-rate {
    font-size: 14px;
    color: #667eea;
    font-weight: 600;
}

.improvement-timeline {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
}

.timeline-start,
.timeline-end {
    text-align: center;
}

.timeline-label {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 4px;
}

.timeline-score {
    display: block;
    font-size: 20px;
    font-weight: 700;
    color: #111827;
}

.timeline-arrow {
    display: flex;
    align-items: center;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 12px;
    padding: 24px 16px;
}

.btn-secondary,
.btn-primary {
    flex: 1;
    padding: 14px;
    text-align: center;
    font-weight: 600;
    border-radius: 12px;
    text-decoration: none;
}

.btn-secondary {
    background: white;
    color: #374151;
    border: 2px solid #e5e7eb;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('comparisonChart').getContext('2d');
    
    const data = @json($comparisonData);
    const labels = data.map((d, i) => `${i + 1}. ${d.date}`);
    const scores = data.map(d => d.recovery_score);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: '회복 점수',
                data: scores,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 8
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
                    },
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '점';
                        }
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
                            size: 11
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
                            return value + '점';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection