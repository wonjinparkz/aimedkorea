<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-600 mb-3">
                    회복 이력
                </h1>
                <p class="text-xl text-gray-600">과거 회복 프로그램과 설문 응답 기록을 확인하세요</p>
                
                <!-- 네비게이션 버튼 -->
                <div class="mt-4 flex justify-center space-x-4">
                    <a href="{{ route('recovery.dashboard') }}" 
                       class="px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition duration-200">
                        대시보드
                    </a>
                    <a href="{{ route('recovery.check') }}" 
                       class="px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition duration-200">
                        12주 프로그램 관리
                    </a>
                    <a href="{{ route('recovery.history') }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold">
                        이력 보기
                    </a>
                </div>
            </div>

            <!-- 타임라인 이력 -->
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">프로그램 이력</h2>
                @if($timelines && $timelines->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        설문
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        시작일
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        종료일
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        진행률
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        상태
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($timelines as $timeline)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $timeline->survey->getTitle(session('locale', 'kor')) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $timeline->start_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $timeline->end_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="text-sm text-gray-900 mr-2">{{ $timeline->progress['percentage'] ?? 0 }}%</span>
                                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $timeline->progress['percentage'] ?? 0 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($timeline->status === 'active')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    진행중
                                                </span>
                                            @elseif($timeline->status === 'completed')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    완료
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    중단
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $timelines->links() }}
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-8 text-center">
                        <p class="text-gray-600">아직 프로그램 이력이 없습니다.</p>
                    </div>
                @endif
            </div>

            <!-- 설문 응답 이력 -->
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">설문 응답 이력</h2>
                @if($responses && $responses->count() > 0)
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($responses as $response)
                            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $response->survey ? $response->survey->getTitle(session('locale', 'kor')) : '설문' }}
                                    </h3>
                                    <span class="text-sm text-gray-500">
                                        {{ $response->created_at->format('Y-m-d') }}
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">총점:</span>
                                        <span class="font-semibold">{{ $response->total_score ?? 0 }}점</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">회복 점수:</span>
                                        <span class="font-semibold text-green-600">{{ 100 - ($response->total_score ?? 0) }}%</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('surveys.results', ['survey' => $response->survey_id, 'response' => $response->id]) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                        결과 상세 보기 →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $responses->links() }}
                    </div>
                @else
                    <div class="bg-gray-50 rounded-lg p-8 text-center">
                        <p class="text-gray-600">아직 설문 응답 이력이 없습니다.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>