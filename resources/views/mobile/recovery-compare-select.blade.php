@extends('layouts.app-mobile')

@section('content')
<div class="mobile-recovery-compare-select">
    <!-- App Header -->
    <div class="app-header">
        <a href="{{ route('recovery.dashboard') }}" class="back-button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="header-title">{{ __('결과 비교') }}</h1>
        <div class="header-action"></div>
    </div>

    <!-- Instructions -->
    <div class="instructions-card">
        <div class="instructions-icon">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <p class="instructions-text">비교할 설문 결과를 2개 이상 5개 이하로 선택해주세요</p>
    </div>

    <!-- Selection Counter -->
    <div class="selection-counter">
        <span class="counter-text">선택된 항목</span>
        <span class="counter-badge">0</span>
    </div>

    <!-- Response List -->
    <form method="POST" action="{{ route('recovery.compare') }}" id="compareForm">
        @csrf
        <div class="responses-list">
            @foreach($responses as $response)
            <div class="response-select-card" onclick="toggleSelection(this, {{ $response->id }})">
                <input type="checkbox" name="response_ids[]" value="{{ $response->id }}" class="response-checkbox">
                
                <div class="response-content">
                    <h3 class="response-title">{{ $response->survey ? $response->survey->getTitle(session('locale', 'kor')) : '설문' }}</h3>
                    <div class="response-info">
                        <span class="info-date">{{ $response->created_at->format('Y.m.d') }}</span>
                        <span class="info-score">회복점수: {{ 100 - $response->total_score }}점</span>
                    </div>
                </div>
                
                <div class="selection-indicator">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Compare Button (Fixed Bottom) -->
        <div class="compare-button-container">
            <button type="submit" class="btn-compare" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                결과 비교하기
            </button>
        </div>
    </form>
</div>

<style>
/* Mobile Recovery Compare Select Styles */
.mobile-recovery-compare-select {
    min-height: 100vh;
    background: #f3f4f6;
    padding-bottom: 100px;
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

/* Instructions Card */
.instructions-card {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 16px;
    padding: 16px;
    background: #eff6ff;
    border-radius: 12px;
    border: 1px solid #dbeafe;
}

.instructions-icon {
    flex-shrink: 0;
}

.instructions-text {
    font-size: 14px;
    color: #1e40af;
    line-height: 1.4;
}

/* Selection Counter */
.selection-counter {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 0 16px 16px;
    padding: 12px 16px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.counter-text {
    font-size: 14px;
    color: #6b7280;
}

.counter-badge {
    width: 28px;
    height: 28px;
    background: #667eea;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
}

/* Response List */
.responses-list {
    padding: 0 16px;
}

.response-select-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: white;
    border-radius: 12px;
    margin-bottom: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    cursor: pointer;
    transition: all 0.2s;
}

.response-select-card.selected {
    background: #eff6ff;
    border: 2px solid #667eea;
}

.response-checkbox {
    display: none;
}

.response-content {
    flex: 1;
}

.response-title {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
}

.response-info {
    display: flex;
    gap: 16px;
    font-size: 13px;
}

.info-date {
    color: #6b7280;
}

.info-score {
    color: #667eea;
    font-weight: 600;
}

.selection-indicator {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: all 0.2s;
}

.response-select-card.selected .selection-indicator {
    background: #667eea;
}

/* Compare Button Container */
.compare-button-container {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 16px;
    background: white;
    border-top: 1px solid #e5e7eb;
    box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.05);
}

.btn-compare {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-size: 16px;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: opacity 0.2s;
}

.btn-compare:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.response-select-card {
    animation: slideIn 0.3s ease-out;
}
</style>

<script>
let selectedCount = 0;

function toggleSelection(card, responseId) {
    const checkbox = card.querySelector('.response-checkbox');
    const isSelected = card.classList.contains('selected');
    
    if (!isSelected && selectedCount >= 5) {
        alert('최대 5개까지만 선택할 수 있습니다.');
        return;
    }
    
    if (isSelected) {
        card.classList.remove('selected');
        checkbox.checked = false;
        selectedCount--;
    } else {
        card.classList.add('selected');
        checkbox.checked = true;
        selectedCount++;
    }
    
    updateCounter();
    updateCompareButton();
}

function updateCounter() {
    document.querySelector('.counter-badge').textContent = selectedCount;
}

function updateCompareButton() {
    const button = document.querySelector('.btn-compare');
    button.disabled = selectedCount < 2;
}

// Form validation
document.getElementById('compareForm').addEventListener('submit', function(e) {
    if (selectedCount < 2) {
        e.preventDefault();
        alert('비교할 결과를 2개 이상 선택해주세요.');
    }
});
</script>
@endsection