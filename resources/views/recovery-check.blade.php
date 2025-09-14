<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-600 mb-3">
                    12주 회복 프로그램 관리
                </h1>
                <p class="text-xl text-gray-600">체계적인 회복 과정을 관리하고 추적하세요</p>
                
                <!-- 네비게이션 버튼 -->
                <div class="mt-4 flex justify-center space-x-4">
                    <a href="{{ route('recovery.dashboard') }}" 
                       class="px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition duration-200">
                        대시보드
                    </a>
                    <a href="{{ route('recovery.check') }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold">
                        12주 프로그램 관리
                    </a>
                    <a href="{{ route('recovery.history') }}" 
                       class="px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition duration-200">
                        이력 보기
                    </a>
                </div>
            </div>

        <!-- 알림 메시지 -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- 활성 타임라인 -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">진행 중인 프로그램</h2>
            
            @if($activeTimelines->count() > 0)
                <div class="grid gap-6 md:grid-cols-1 lg:grid-cols-2">
                    @foreach($activeTimelines as $timeline)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                            <div class="p-6">
                                <!-- 타임라인 헤더 -->
                                <div class="mb-4">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            진행중
                                        </span>
                                        <h3 class="text-xl font-semibold text-gray-800">
                                            {{ $timeline->survey ? (string)$timeline->survey->getTitle(session('locale', 'kor')) : 'Unknown Survey' }}
                                        </h3>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        시작일: {{ $timeline->start_date->format('Y년 m월 d일') }} | 
                                        종료 예정일: {{ $timeline->end_date->format('Y년 m월 d일') }}
                                    </p>
                                </div>

                                <!-- 진행률 바 -->
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>진행률</span>
                                        <span>{{ $timeline->progress['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $timeline->progress['percentage'] }}%"></div>
                                    </div>
                                </div>

                                <!-- 다음 체크포인트 -->
                                @if($timeline->nextCheckpoint)
                                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                                        <p class="text-sm font-semibold text-blue-800 mb-1">다음 체크포인트</p>
                                        <p class="text-lg font-bold text-blue-900">{{ $timeline->nextCheckpoint->week_number }}주차</p>
                                        <p class="text-sm text-blue-700 mt-1">
                                            예정일: {{ $timeline->nextCheckpoint->scheduled_date->format('Y년 m월 d일') }}
                                        </p>
                                        @if($timeline->nextCheckpoint->status === 'ongoing')
                                            <a href="{{ route('surveys.show', ['survey' => $timeline->survey_id]) }}" 
                                               class="inline-block mt-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-1 px-4 rounded transition duration-200">
                                                설문 진행하기
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                <!-- 체크포인트 목록 -->
                                <div class="space-y-2">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">체크포인트 현황 (0~12주)</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($timeline->checkpoints->sortBy('week_number') as $checkpoint)
                                            <div class="flex flex-col items-center">
                                                <div class="relative group">
                                                    @if($checkpoint->status === 'completed')
                                                        <div class="w-9 h-9 bg-green-500 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    @elseif($checkpoint->status === 'ongoing')
                                                        <div class="w-9 h-9 bg-blue-500 rounded-full flex items-center justify-center animate-pulse">
                                                            <span class="text-white text-xs font-bold">!</span>
                                                        </div>
                                                    @elseif($checkpoint->status === 'missed')
                                                        <div class="w-9 h-9 bg-red-500 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-9 h-9 bg-gray-300 rounded-full flex items-center justify-center">
                                                            <span class="text-gray-600 text-xs font-semibold">{{ $checkpoint->week_number }}</span>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- 툴팁 -->
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                                                        <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                                            {{ $checkpoint->week_number }}주차
                                                            @if($checkpoint->completed_date)
                                                                <br>완료: {{ $checkpoint->completed_date->format('m/d') }}
                                                            @else
                                                                <br>예정: {{ $checkpoint->scheduled_date->format('m/d') }}
                                                            @endif
                                                            @if($checkpoint->score !== null)
                                                                <br>점수: {{ 100 - $checkpoint->score }}점
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-600 mt-1">{{ $checkpoint->week_number }}주</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- 액션 버튼 -->
                                <div class="flex justify-end mt-4 space-x-2">
                                    <button onclick="updateTimelineStatus({{ $timeline->id }}, 'completed')" 
                                            class="text-sm text-green-600 hover:text-green-800 font-semibold">
                                        완료 처리
                                    </button>
                                    <button onclick="updateTimelineStatus({{ $timeline->id }}, 'abandoned')" 
                                            class="text-sm text-red-600 hover:text-red-800 font-semibold">
                                        중단
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-gray-600 mb-4">진행 중인 회복 프로그램이 없습니다.</p>
                    <button onclick="openNewTimelineModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        첫 프로그램 시작하기
                    </button>
                </div>
            @endif
        </div>

        <!-- 완료/중단된 타임라인 -->
        @if($inactiveTimelines->count() > 0)
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">지난 프로그램</h2>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    설문
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    기간
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    진행률
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    상태
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    액션
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($inactiveTimelines as $timeline)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $timeline->survey ? (string)$timeline->survey->getTitle(session('locale', 'kor')) : 'Unknown Survey' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $timeline->start_date->format('Y.m.d') }} - {{ $timeline->end_date->format('Y.m.d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-900 mr-2">{{ $timeline->progress['percentage'] }}%</span>
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $timeline->progress['percentage'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($timeline->status === 'completed')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                완료
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                중단
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($timeline->status === 'abandoned')
                                            <button onclick="updateTimelineStatus({{ $timeline->id }}, 'active')" 
                                                    class="text-blue-600 hover:text-blue-900 font-semibold">
                                                재시작
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- 새 타임라인 생성 모달 -->
<div id="newTimelineModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">새 회복 프로그램 시작</h3>
            <form action="{{ route('recovery.timeline.create') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="survey_id" class="block text-sm font-medium text-gray-700 mb-2">
                        설문 선택
                    </label>
                    <select name="survey_id" id="survey_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">설문을 선택하세요</option>
                        @foreach($availableSurveys as $survey)
                            <option value="{{ $survey->id }}">
                                {{ (string)$survey->getTitle(session('locale', 'kor')) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        메모 (선택사항)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="이 프로그램에 대한 목표나 메모를 입력하세요"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeNewTimelineModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        취소
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                        시작하기
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 타임라인 상태 변경 폼 (hidden) -->
<form id="updateStatusForm" method="POST" action="" class="hidden">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusInput">
</form>

<script>
function openNewTimelineModal() {
    document.getElementById('newTimelineModal').classList.remove('hidden');
}

function closeNewTimelineModal() {
    document.getElementById('newTimelineModal').classList.add('hidden');
}

function updateTimelineStatus(timelineId, status) {
    if (status === 'abandoned') {
        if (!confirm('정말로 이 프로그램을 중단하시겠습니까?')) {
            return;
        }
    } else if (status === 'completed') {
        if (!confirm('이 프로그램을 완료 처리하시겠습니까?')) {
            return;
        }
    } else if (status === 'active') {
        if (!confirm('이 프로그램을 다시 시작하시겠습니까?')) {
            return;
        }
    }
    
    const form = document.getElementById('updateStatusForm');
    form.action = `/recovery-dashboard/timeline/${timelineId}/status`;
    document.getElementById('statusInput').value = status;
    form.submit();
}

// 모달 외부 클릭시 닫기
window.onclick = function(event) {
    const modal = document.getElementById('newTimelineModal');
    if (event.target == modal) {
        closeNewTimelineModal();
    }
}
</script>
        </div>
    </div>
</x-app-layout>