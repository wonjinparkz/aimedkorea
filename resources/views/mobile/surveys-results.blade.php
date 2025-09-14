@extends('layouts.app-mobile')

@section('content')
<div class="mobile-survey-results">
    <!-- App Header -->
    <div class="app-header">
        <a href="{{ route('surveys.index') }}" class="back-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="header-title">{{ __('설문 결과') }}</h1>
        <div class="header-action"></div>
    </div>

    <!-- Score Summary Card -->
    <div class="score-summary-card">
        <div class="score-circle">
            <svg class="progress-ring" width="200" height="200">
                <circle class="progress-ring__circle" stroke="#e5e5e5" stroke-width="12" fill="transparent" r="88" cx="100" cy="100"/>
                <circle class="progress-ring__circle progress-ring__circle--filled" 
                        stroke="{{ $response->total_score <= 20 ? '#10b981' : ($response->total_score <= 40 ? '#f59e0b' : '#ef4444') }}" 
                        stroke-width="12" 
                        fill="transparent" 
                        r="88" 
                        cx="100" 
                        cy="100"
                        style="stroke-dasharray: {{ 88 * 2 * 3.14159 }}; stroke-dashoffset: {{ 88 * 2 * 3.14159 * (1 - ($response->total_score / 100)) }}"/>
            </svg>
            <div class="score-text">
                <div class="score-number">{{ $response->total_score }}</div>
                <div class="score-label">총점</div>
            </div>
        </div>
        
        <div class="score-status mt-4">
            @if($response->total_score <= 20)
                <span class="status-badge status-good">좋음</span>
                <p class="status-text">전반적으로 양호한 상태입니다</p>
            @elseif($response->total_score <= 40)
                <span class="status-badge status-moderate">보통</span>
                <p class="status-text">일부 개선이 필요한 부분이 있습니다</p>
            @else
                <span class="status-badge status-attention">주의</span>
                <p class="status-text">전문가 상담을 고려해보세요</p>
            @endif
        </div>
    </div>

    <!-- Category Analysis -->
    @if(!empty($categoryAnalysis))
    <div class="category-section">
        <h2 class="section-title">카테고리별 분석</h2>
        <div class="category-cards">
            @foreach($categoryAnalysis as $category)
            <div class="category-card">
                <div class="category-header">
                    <h3 class="category-name">{{ $category['name'] }}</h3>
                    @if($category['percentage'] !== null)
                    <span class="category-score">{{ round($category['percentage']) }}%</span>
                    @endif
                </div>
                
                @if($category['percentage'] !== null)
                <div class="category-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $category['percentage'] }}%; background: {{ $category['percentage'] >= 70 ? '#10b981' : ($category['percentage'] >= 40 ? '#f59e0b' : '#ef4444') }}"></div>
                    </div>
                </div>
                @endif
                
                @if($category['description'])
                <p class="category-description">{{ $category['description'] }}</p>
                @endif
                
                <div class="category-stats">
                    <span class="stat-item">
                        <span class="stat-label">응답:</span>
                        <span class="stat-value">{{ $category['answered_count'] }}/{{ $category['question_count'] }}</span>
                    </span>
                    <span class="stat-item">
                        <span class="stat-label">점수:</span>
                        <span class="stat-value">{{ $category['score'] }}/{{ $category['max_score'] }}</span>
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recommendations -->
    <div class="recommendations-section">
        <h2 class="section-title">추천 사항</h2>
        <div class="recommendation-cards">
            @if($response->total_score <= 20)
                <div class="recommendation-card">
                    <div class="recommendation-icon" style="background: #10b981">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="recommendation-content">
                        <h4>현재 상태 유지</h4>
                        <p>지금의 건강한 생활 습관을 계속 유지하세요</p>
                    </div>
                </div>
            @elseif($response->total_score <= 40)
                <div class="recommendation-card">
                    <div class="recommendation-icon" style="background: #f59e0b">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="recommendation-content">
                        <h4>생활 습관 개선</h4>
                        <p>규칙적인 운동과 균형잡힌 식사를 권장합니다</p>
                    </div>
                </div>
            @else
                <div class="recommendation-card">
                    <div class="recommendation-icon" style="background: #ef4444">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="recommendation-content">
                        <h4>전문가 상담 권장</h4>
                        <p>전문의와 상담을 통해 정확한 진단을 받아보세요</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('surveys.show', $survey) }}" class="btn-retake">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            다시 검사하기
        </a>
        
        @auth
        <a href="{{ route('recovery.dashboard') }}" class="btn-dashboard">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            회복 대시보드
        </a>
        @endauth
        
        <button onclick="shareResults()" class="btn-share">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a3 3 0 10-2.684-2.684m0 0a3 3 0 00-2.684 2.684m0 0a3 3 0 102.684 2.684"></path>
            </svg>
            결과 공유
        </button>
    </div>
</div>

<style>
/* Mobile Survey Results Styles */
.mobile-survey-results {
    min-height: 100vh;
    background: linear-gradient(180deg, #f0f9ff 0%, #ffffff 100%);
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
}

.header-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.header-action {
    width: 40px;
}

/* Score Summary Card */
.score-summary-card {
    background: white;
    margin: 16px;
    padding: 24px;
    border-radius: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    text-align: center;
}

.score-circle {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring__circle--filled {
    transition: stroke-dashoffset 1s ease-in-out;
}

.score-text {
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
    font-size: 14px;
    color: #6b7280;
    margin-top: 4px;
}

.score-status {
    text-align: center;
}

.status-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
}

.status-good {
    background: #d1fae5;
    color: #065f46;
}

.status-moderate {
    background: #fed7aa;
    color: #92400e;
}

.status-attention {
    background: #fee2e2;
    color: #991b1b;
}

.status-text {
    color: #6b7280;
    font-size: 14px;
}

/* Category Section */
.category-section {
    padding: 0 16px;
    margin-top: 24px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 16px;
}

.category-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.category-card {
    background: white;
    padding: 16px;
    border-radius: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.category-name {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
}

.category-score {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
}

.category-progress {
    margin-bottom: 12px;
}

.progress-bar {
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 1s ease-out;
}

.category-description {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.5;
    margin-bottom: 12px;
}

.category-stats {
    display: flex;
    gap: 16px;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
}

.stat-label {
    color: #9ca3af;
}

.stat-value {
    font-weight: 600;
    color: #374151;
}

/* Recommendations */
.recommendations-section {
    padding: 24px 16px;
}

.recommendation-cards {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.recommendation-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: white;
    padding: 16px;
    border-radius: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.recommendation-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.recommendation-content h4 {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.recommendation-content p {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.4;
}

/* Action Buttons */
.action-buttons {
    padding: 24px 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.action-buttons a,
.action-buttons button {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    border-radius: 16px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-retake {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.btn-dashboard {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

.btn-share {
    background: white;
    color: #374151;
    border: 2px solid #e5e7eb;
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

.score-summary-card,
.category-card,
.recommendation-card {
    animation: slideUp 0.5s ease-out;
}

.category-card:nth-child(2) { animation-delay: 0.1s; }
.category-card:nth-child(3) { animation-delay: 0.2s; }
.recommendation-card:nth-child(2) { animation-delay: 0.1s; }
</style>

<script>
function shareResults() {
    if (navigator.share) {
        navigator.share({
            title: '설문 결과',
            text: '내 설문 결과를 확인해보세요!',
            url: window.location.href
        }).then(() => {
            console.log('Shared successfully');
        }).catch((error) => {
            console.log('Error sharing:', error);
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            alert('링크가 복사되었습니다!');
        });
    }
}

// Animate progress on load
window.addEventListener('load', function() {
    const progressCircles = document.querySelectorAll('.progress-ring__circle--filled');
    progressCircles.forEach(circle => {
        const offset = circle.style.strokeDashoffset;
        circle.style.strokeDashoffset = circle.style.strokeDasharray;
        setTimeout(() => {
            circle.style.strokeDashoffset = offset;
        }, 100);
    });
    
    // Animate progress bars
    const progressFills = document.querySelectorAll('.progress-fill');
    progressFills.forEach(fill => {
        const width = fill.style.width;
        fill.style.width = '0';
        setTimeout(() => {
            fill.style.width = width;
        }, 300);
    });
});
</script>
@endsection