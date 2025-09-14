<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-600 mb-3">
                    체크 이력
                </h1>
                <p class="text-xl text-gray-600">{{ Auth::user()->name }}님의 모든 셀프 체크 기록입니다</p>
            </div>

            <!-- 이력 테이블 -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">체크 이력 목록</h2>
                        <a href="{{ route('recovery.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            대시보드로 돌아가기
                        </a>
                    </div>

                    @if($responses->count() > 0)
                        <form action="{{ route('recovery.compare') }}" method="POST" id="compareForm">
                            @csrf
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                선택
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                날짜
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                설문
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                회복 점수
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
                                        @foreach($responses as $response)
                                            @php
                                                $score = $response->recovery_score ?? 0;
                                                $level = $score >= 80 ? '최적' : ($score >= 65 ? '우수' : ($score >= 50 ? '양호' : ($score >= 35 ? '주의' : ($score >= 20 ? '위험' : '붕괴'))));
                                                $levelColor = $score >= 80 ? 'text-green-600' : ($score >= 65 ? 'text-green-500' : ($score >= 50 ? 'text-yellow-600' : ($score >= 35 ? 'text-orange-600' : ($score >= 20 ? 'text-red-600' : 'text-red-800'))));
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="checkbox" name="responses[]" value="{{ $response->id }}" 
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $response->created_at->format('Y년 m월 d일') }}
                                                    <div class="text-xs text-gray-500">{{ $response->created_at->format('H:i') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $response->survey->title }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="text-2xl font-bold {{ $levelColor }}">
                                                            {{ $score }}%
                                                        </div>
                                                        @if($loop->index > 0)
                                                            @php
                                                                $prevScore = $responses[$loop->index - 1]->recovery_score ?? 0;
                                                                $diff = $score - $prevScore;
                                                            @endphp
                                                            @if($diff > 0)
                                                                <span class="ml-2 text-sm text-green-600">
                                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                                                    </svg>
                                                                    +{{ $diff }}
                                                                </span>
                                                            @elseif($diff < 0)
                                                                <span class="ml-2 text-sm text-red-600">
                                                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                                                    </svg>
                                                                    {{ $diff }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $levelColor }} bg-gray-100">
                                                        {{ $level }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('surveys.results', ['survey' => $response->survey_id, 'response' => $response->id]) }}" 
                                                       class="text-blue-600 hover:text-blue-900">
                                                        상세보기
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- 비교 버튼 -->
                            <div class="mt-4 flex justify-between items-center">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                        id="compareButton"
                                        disabled>
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    선택한 결과 비교하기
                                </button>
                                <div class="text-sm text-gray-500">
                                    2개 이상 선택하여 비교할 수 있습니다
                                </div>
                            </div>
                        </form>

                        <!-- 페이지네이션 -->
                        <div class="mt-6">
                            {{ $responses->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">체크 이력이 없습니다</h3>
                            <p class="mt-1 text-sm text-gray-500">셀프 체크를 시작하여 첫 기록을 만들어보세요.</p>
                            <div class="mt-6">
                                <a href="{{ route('surveys.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    체크 시작하기
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="responses[]"]');
            const compareButton = document.getElementById('compareButton');
            
            function updateCompareButton() {
                const checkedCount = document.querySelectorAll('input[name="responses[]"]:checked').length;
                compareButton.disabled = checkedCount < 2;
            }
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCompareButton);
            });
        });
    </script>
</x-app-layout>