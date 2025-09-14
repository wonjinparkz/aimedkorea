@extends('layouts.app-mobile')

@section('content')
<div class="mobile-recovery-check">
    <!-- App Header -->
    <div class="app-header">
        <a href="{{ route('recovery.dashboard') }}" class="back-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="header-title">{{ __('12주 체크') }}</h1>
        <button onclick="showNewTimelineModal()" class="header-action">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </button>
    </div>

    <!-- Active Timelines -->
    @if($activeTimelines->count() > 0)
    <div class="section">
        <h2 class="section-title">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            진행 중인 프로그램
        </h2>
        
        @foreach($activeTimelines as $timeline)
        <div class="timeline-card active">
            <div class="timeline-header">
                <h3 class="timeline-name">{{ $timeline->survey->getTitle(session('locale', 'kor')) }}</h3>
                <span class="status-badge active">진행중</span>
            </div>
            
            <div class="timeline-meta">
                <div class="meta-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $timeline->start_date->format('Y.m.d') }} 시작</span>
                </div>
                <div class="meta-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ $timeline->getRemainingDays() }}일 남음</span>
                </div>
            </div>
            
            <!-- Weekly Checkpoints -->
            <div class="checkpoints-container">
                <div class="checkpoints-header">
                    <span class="checkpoints-label">주차별 체크포인트</span>
                    <span class="checkpoints-progress">{{ $timeline->getCompletedCheckpointsCount() }}/{{ $timeline->checkpoints->count() }}</span>
                </div>
                
                <div class="checkpoints-timeline">
                    @foreach($timeline->checkpoints as $checkpoint)
                    <div class="checkpoint-item {{ $checkpoint->status }}" 
                         onclick="handleCheckpoint({{ $checkpoint->id }}, '{{ $checkpoint->status }}', {{ $timeline->survey_id }})">
                        <div class="checkpoint-marker">
                            @if($checkpoint->status == 'completed')
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            @elseif($checkpoint->status == 'ongoing')
                            <div class="ongoing-pulse"></div>
                            @elseif($checkpoint->status == 'missed')
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            @else
                            <span class="checkpoint-number">{{ $checkpoint->week_number }}</span>
                            @endif
                        </div>
                        <div class="checkpoint-details">
                            <span class="checkpoint-week">{{ $checkpoint->week_number }}주차</span>
                            <span class="checkpoint-date">{{ $checkpoint->scheduled_date->format('m/d') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Next Action -->
            @if($timeline->nextCheckpoint)
            <div class="next-action">
                <div class="action-info">
                    <span class="action-label">다음 체크포인트</span>
                    <span class="action-date">{{ $timeline->nextCheckpoint->scheduled_date->format('Y년 m월 d일') }}</span>
                </div>
                <a href="{{ route('surveys.show', ['survey' => $timeline->survey_id, 'analysis_type' => 'detailed']) }}" 
                   class="btn-start-checkpoint">
                    시작하기
                </a>
            </div>
            @endif
            
            <!-- Timeline Actions -->
            <div class="timeline-actions">
                <button onclick="updateTimelineStatus({{ $timeline->id }}, 'completed')" class="btn-complete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    완료
                </button>
                <button onclick="updateTimelineStatus({{ $timeline->id }}, 'abandoned')" class="btn-abandon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    중단
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <p class="text-gray-600 text-center mb-4">진행 중인 프로그램이 없습니다</p>
        <button onclick="showNewTimelineModal()" class="btn-primary">
            새 프로그램 시작하기
        </button>
    </div>
    @endif

    <!-- Inactive Timelines -->
    @if($inactiveTimelines->count() > 0)
    <div class="section">
        <h2 class="section-title">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            지난 프로그램
        </h2>
        
        @foreach($inactiveTimelines as $timeline)
        <div class="timeline-card inactive">
            <div class="timeline-header">
                <h3 class="timeline-name">{{ $timeline->survey->getTitle(session('locale', 'kor')) }}</h3>
                <span class="status-badge {{ $timeline->status }}">
                    {{ $timeline->status == 'completed' ? '완료' : '중단' }}
                </span>
            </div>
            
            <div class="timeline-meta">
                <div class="meta-item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $timeline->start_date->format('Y.m.d') }} - {{ $timeline->end_date->format('Y.m.d') }}</span>
                </div>
            </div>
            
            <div class="completion-summary">
                <span class="summary-label">완료율</span>
                <div class="summary-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $timeline->getProgressPercentage() }}%"></div>
                    </div>
                    <span class="progress-text">{{ $timeline->getProgressPercentage() }}%</span>
                </div>
            </div>
            
            @if($timeline->status == 'abandoned')
            <button onclick="updateTimelineStatus({{ $timeline->id }}, 'active')" class="btn-reactivate">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                다시 시작
            </button>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- New Timeline Modal -->
<div id="newTimelineModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">새 프로그램 시작</h3>
            <button onclick="closeNewTimelineModal()" class="modal-close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('recovery.timeline.create') }}">
            @csrf
            <div class="modal-body">
                <label class="form-label">설문 선택</label>
                <select name="survey_id" class="form-select" required>
                    <option value="">설문을 선택하세요</option>
                    @foreach($availableSurveys as $survey)
                    <option value="{{ $survey->id }}">{{ $survey->getTitle(session('locale', 'kor')) }}</option>
                    @endforeach
                </select>
                
                <label class="form-label mt-4">메모 (선택사항)</label>
                <textarea name="notes" class="form-textarea" rows="3" placeholder="프로그램 목표나 메모를 입력하세요"></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeNewTimelineModal()" class="btn-cancel">취소</button>
                <button type="submit" class="btn-submit">시작하기</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Mobile Recovery Check Styles */
.mobile-recovery-check {
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
    border: none;
    cursor: pointer;
}

.header-action {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.header-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

/* Section */
.section {
    padding: 20px 16px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 16px;
}

/* Timeline Card */
.timeline-card {
    background: white;
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.timeline-card.inactive {
    opacity: 0.8;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.timeline-name {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    flex: 1;
    margin-right: 8px;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.active {
    background: #dbeafe;
    color: #1e40af;
}

.status-badge.completed {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.abandoned {
    background: #fee2e2;
    color: #991b1b;
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

/* Checkpoints Container */
.checkpoints-container {
    background: #f9fafb;
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 12px;
}

.checkpoints-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 13px;
}

.checkpoints-label {
    color: #6b7280;
}

.checkpoints-progress {
    font-weight: 600;
    color: #111827;
}

.checkpoints-timeline {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
    gap: 8px;
}

.checkpoint-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: transform 0.2s;
}

.checkpoint-item:active {
    transform: scale(0.95);
}

.checkpoint-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px;
    background: #e5e7eb;
    color: #9ca3af;
}

.checkpoint-item.completed .checkpoint-marker {
    background: #10b981;
    color: white;
}

.checkpoint-item.ongoing .checkpoint-marker {
    background: #f59e0b;
    color: white;
}

.checkpoint-item.missed .checkpoint-marker {
    background: #ef4444;
    color: white;
}

.checkpoint-item.scheduled .checkpoint-marker {
    background: #e5e7eb;
    color: #6b7280;
}

.ongoing-pulse {
    width: 12px;
    height: 12px;
    background: white;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
    }
}

.checkpoint-number {
    font-size: 14px;
    font-weight: 600;
}

.checkpoint-details {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 11px;
}

.checkpoint-week {
    color: #6b7280;
}

.checkpoint-date {
    color: #9ca3af;
}

/* Next Action */
.next-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: linear-gradient(135deg, #eff6ff, #f0f9ff);
    border-radius: 12px;
    margin-bottom: 12px;
}

.action-info {
    display: flex;
    flex-direction: column;
}

.action-label {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 2px;
}

.action-date {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.btn-start-checkpoint {
    padding: 8px 16px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
}

/* Timeline Actions */
.timeline-actions {
    display: flex;
    gap: 8px;
}

.timeline-actions button {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 10px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.btn-complete {
    background: #d1fae5;
    color: #065f46;
}

.btn-abandon {
    background: #fee2e2;
    color: #991b1b;
}

/* Completion Summary */
.completion-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f9fafb;
    border-radius: 12px;
    margin-bottom: 12px;
}

.summary-label {
    font-size: 13px;
    color: #6b7280;
}

.summary-progress {
    display: flex;
    align-items: center;
    gap: 8px;
}

.progress-bar {
    width: 100px;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.progress-text {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
}

.btn-reactivate {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 10px;
    background: #eff6ff;
    color: #2563eb;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.btn-primary {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-weight: 600;
    border-radius: 12px;
    border: none;
    cursor: pointer;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 100;
    padding: 20px;
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 20px;
    width: 100%;
    max-width: 400px;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.modal-close {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
}

.modal-body {
    padding: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.form-select,
.form-textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
}

.modal-footer {
    display: flex;
    gap: 12px;
    padding: 20px;
    border-top: 1px solid #e5e7eb;
}

.btn-cancel,
.btn-submit {
    flex: 1;
    padding: 12px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}

.btn-cancel {
    background: #f3f4f6;
    color: #374151;
}

.btn-submit {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}
</style>

<script>
function showNewTimelineModal() {
    document.getElementById('newTimelineModal').classList.add('show');
}

function closeNewTimelineModal() {
    document.getElementById('newTimelineModal').classList.remove('show');
}

function handleCheckpoint(checkpointId, status, surveyId) {
    if (status === 'ongoing' || status === 'scheduled') {
        // 체크포인트 시작
        window.location.href = `/surveys/${surveyId}?analysis_type=detailed&checkpoint=${checkpointId}`;
    }
}

function updateTimelineStatus(timelineId, status) {
    if (confirm(`이 프로그램을 ${status === 'completed' ? '완료' : status === 'abandoned' ? '중단' : '재시작'}하시겠습니까?`)) {
        fetch(`/recovery-dashboard/timeline/${timelineId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
}

// Modal backdrop close
document.getElementById('newTimelineModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNewTimelineModal();
    }
});
</script>
@endsection