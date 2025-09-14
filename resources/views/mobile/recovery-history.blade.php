@extends('layouts.app-mobile')

@section('content')
<div class="mobile-recovery-history">
    <!-- App Header -->
    <div class="app-header">
        <a href="{{ route('recovery.dashboard') }}" class="back-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="header-title">{{ __('회복 이력') }}</h1>
        <a href="{{ route('recovery.compare') }}" class="header-action">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </a>
    </div>

    <!-- Timeline History Tab -->
    <div class="history-tabs">
        <button class="tab-button active" onclick="switchTab('timelines')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            프로그램
        </button>
        <button class="tab-button" onclick="switchTab('responses')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            응답
        </button>
    </div>

    <!-- Timelines Tab Content -->
    <div id="timelines-tab" class="tab-content active">
        @if($timelines->count() > 0)
            @foreach($timelines as $timeline)
            <div class="timeline-history-card">
                <div class="timeline-status-badge {{ $timeline->status }}">
                    @if($timeline->status == 'active')
                        진행중
                    @elseif($timeline->status == 'completed')
                        완료
                    @else
                        중단
                    @endif
                </div>
                
                <h3 class="timeline-name">{{ $timeline->survey->getTitle(session('locale', 'kor')) }}</h3>
                
                <div class="timeline-meta">
                    <span class="meta-item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $timeline->start_date->format('Y.m.d') }} - {{ $timeline->end_date->format('Y.m.d') }}
                    </span>
                </div>
                
                <div class="timeline-checkpoints">
                    <div class="checkpoints-header">
                        <span>체크포인트</span>
                        <span>{{ $timeline->getCompletedCheckpointsCount() }}/{{ $timeline->checkpoints->count() }}</span>
                    </div>
                    <div class="checkpoints-grid">
                        @foreach($timeline->checkpoints as $checkpoint)
                        <div class="checkpoint-dot {{ $checkpoint->status }}">
                            <span class="checkpoint-week">{{ $checkpoint->week_number }}주</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                @if($timeline->status == 'active')
                <div class="timeline-actions">
                    <a href="{{ route('recovery.check') }}" class="btn-continue">계속하기</a>
                </div>
                @endif
            </div>
            @endforeach
            
            <div class="pagination-container">
                {{ $timelines->links('pagination::simple-bootstrap-4') }}
            </div>
        @else
            <div class="empty-state">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-gray-600">진행한 프로그램이 없습니다</p>
            </div>
        @endif
    </div>

    <!-- Responses Tab Content -->
    <div id="responses-tab" class="tab-content">
        @if($responses->count() > 0)
            @foreach($responses as $response)
            <div class="response-history-card">
                <div class="response-header">
                    <h3 class="response-survey">{{ $response->survey ? $response->survey->getTitle(session('locale', 'kor')) : '설문' }}</h3>
                    <div class="response-score">
                        <span class="score-value">{{ 100 - $response->total_score }}</span>
                        <span class="score-label">점</span>
                    </div>
                </div>
                
                <div class="response-meta">
                    <span class="meta-item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $response->created_at->format('Y.m.d H:i') }}
                    </span>
                    @if($response->analysis_type)
                    <span class="analysis-type-badge">{{ $response->analysis_type == 'detailed' ? '심층' : '간편' }}</span>
                    @endif
                </div>
                
                <!-- Mini Category Bars -->
                @php
                    $categoryScores = $response->getCategoryScores();
                @endphp
                @if($categoryScores && count($categoryScores) > 0)
                <div class="mini-categories">
                    @foreach(array_slice($categoryScores, 0, 3) as $category)
                    <div class="mini-category">
                        <span class="mini-category-name">{{ $category['name'] }}</span>
                        <div class="mini-category-bar">
                            <div class="mini-category-fill" style="width: {{ 100 - $category['percentage'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                
                <div class="response-actions">
                    <a href="{{ route('surveys.results', ['survey' => $response->survey_id, 'response' => $response->id]) }}" class="btn-view-details">
                        상세보기
                    </a>
                </div>
            </div>
            @endforeach
            
            <div class="pagination-container">
                {{ $responses->links('pagination::simple-bootstrap-4') }}
            </div>
        @else
            <div class="empty-state">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-600">설문 응답 기록이 없습니다</p>
            </div>
        @endif
    </div>
</div>

<style>
/* Mobile Recovery History Styles */
.mobile-recovery-history {
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

/* History Tabs */
.history-tabs {
    display: flex;
    background: white;
    padding: 8px 16px;
    gap: 8px;
    border-bottom: 1px solid #e5e7eb;
}

.tab-button {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    transition: all 0.2s;
}

.tab-button.active {
    background: #eff6ff;
    color: #2563eb;
    font-weight: 600;
}

.tab-content {
    display: none;
    padding: 16px;
}

.tab-content.active {
    display: block;
}

/* Timeline History Card */
.timeline-history-card {
    background: white;
    padding: 16px;
    border-radius: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    position: relative;
}

.timeline-status-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.timeline-status-badge.active {
    background: #dbeafe;
    color: #1e40af;
}

.timeline-status-badge.completed {
    background: #d1fae5;
    color: #065f46;
}

.timeline-status-badge.abandoned {
    background: #fee2e2;
    color: #991b1b;
}

.timeline-name {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
    margin-right: 60px;
}

.timeline-meta {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    color: #6b7280;
}

.timeline-checkpoints {
    background: #f9fafb;
    padding: 12px;
    border-radius: 12px;
    margin-bottom: 12px;
}

.checkpoints-header {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 12px;
}

.checkpoints-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
}

.checkpoint-dot {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
}

.checkpoint-dot.completed {
    background: #10b981;
}

.checkpoint-dot.ongoing {
    background: #f59e0b;
}

.checkpoint-week {
    font-size: 10px;
    color: white;
    font-weight: 600;
}

.checkpoint-dot:not(.completed):not(.ongoing) .checkpoint-week {
    color: #9ca3af;
}

.timeline-actions {
    display: flex;
    gap: 8px;
}

.btn-continue {
    flex: 1;
    padding: 10px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    text-align: center;
    font-weight: 600;
    border-radius: 10px;
    text-decoration: none;
}

/* Response History Card */
.response-history-card {
    background: white;
    padding: 16px;
    border-radius: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.response-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.response-survey {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    flex: 1;
}

.response-score {
    display: flex;
    align-items: baseline;
    gap: 2px;
}

.score-value {
    font-size: 24px;
    font-weight: 700;
    color: #2563eb;
}

.score-label {
    font-size: 14px;
    color: #6b7280;
}

.response-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.analysis-type-badge {
    padding: 2px 8px;
    background: #fef3c7;
    color: #92400e;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

/* Mini Categories */
.mini-categories {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}

.mini-category {
    display: flex;
    align-items: center;
    gap: 8px;
}

.mini-category-name {
    font-size: 12px;
    color: #6b7280;
    min-width: 60px;
}

.mini-category-bar {
    flex: 1;
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
}

.mini-category-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
}

.response-actions {
    display: flex;
    gap: 8px;
}

.btn-view-details {
    flex: 1;
    padding: 8px;
    background: #f3f4f6;
    color: #374151;
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

/* Pagination */
.pagination-container {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.pagination {
    gap: 4px;
}

.pagination .page-link {
    border-radius: 8px;
    border: none;
    background: white;
    color: #374151;
    padding: 8px 12px;
}

.pagination .page-item.active .page-link {
    background: #667eea;
    color: white;
}
</style>

<script>
function switchTab(tabName) {
    // Remove active class from all tabs and contents
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Add active class to selected tab and content
    event.target.closest('.tab-button').classList.add('active');
    document.getElementById(tabName + '-tab').classList.add('active');
}
</script>
@endsection