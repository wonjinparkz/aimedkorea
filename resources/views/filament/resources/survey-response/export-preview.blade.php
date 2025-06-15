<div class="space-y-6 p-6">
    <!-- 헤더 정보 -->
    <div class="text-center border-b pb-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            설문 응답 분석 보고서
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            {{ $response->survey->title }}
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
            응답일시: {{ $response->created_at->format('Y년 m월 d일 H:i') }}
        </p>
    </div>
    
    <!-- 전체 요약 -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">전체 요약</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $analysisData['total_score'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">총점</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                    {{ count($analysisData['category_scores']) }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">카테고리</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    {{ $analysisData['response_count'] }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">응답 문항</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">
                    @php
                        $avgPercentage = count($analysisData['category_scores']) > 0
                            ? round(collect($analysisData['category_scores'])->avg('percentage'), 1)
                            : 0;
                    @endphp
                    {{ $avgPercentage }}%
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">평균 성취도</div>
            </div>
        </div>
    </div>
    
    <!-- 카테고리별 상세 분석 -->
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">카테고리별 상세 분석</h3>
        
        @foreach($analysisData['category_scores'] as $index => $category)
            <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-200">
                        {{ $index + 1 }}. {{ $category['name'] }}
                    </h4>
                    <span class="text-lg font-bold {{ $category['percentage'] >= 70 ? 'text-green-600' : ($category['percentage'] >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $category['percentage'] }}%
                    </span>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-3">
                    <div class="h-full rounded-full transition-all {{ $category['percentage'] >= 70 ? 'bg-green-500' : ($category['percentage'] >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}"
                         style="width: {{ $category['percentage'] }}%">
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <div>득점: {{ $category['score'] }}/{{ $category['max_score'] }}점</div>
                    <div>응답: {{ $category['answered_count'] }}/{{ $category['question_count'] }}개</div>
                    <div>평균: {{ $category['answered_count'] > 0 ? round($category['score'] / $category['answered_count'], 1) : 0 }}점</div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- 성과 분석 -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">성과 분석</h3>
        
        @php
            $highPerformance = collect($analysisData['category_scores'])->where('percentage', '>=', 70);
            $mediumPerformance = collect($analysisData['category_scores'])->where('percentage', '>=', 40)->where('percentage', '<', 70);
            $lowPerformance = collect($analysisData['category_scores'])->where('percentage', '<', 40);
        @endphp
        
        @if($highPerformance->count() > 0)
            <div class="mb-3">
                <h4 class="font-medium text-green-700 dark:text-green-400 mb-2">우수 카테고리</h4>
                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400">
                    @foreach($highPerformance as $category)
                        <li>{{ $category['name'] }} ({{ $category['percentage'] }}%)</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if($mediumPerformance->count() > 0)
            <div class="mb-3">
                <h4 class="font-medium text-yellow-700 dark:text-yellow-400 mb-2">보통 카테고리</h4>
                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400">
                    @foreach($mediumPerformance as $category)
                        <li>{{ $category['name'] }} ({{ $category['percentage'] }}%)</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if($lowPerformance->count() > 0)
            <div class="mb-3">
                <h4 class="font-medium text-red-700 dark:text-red-400 mb-2">개선 필요 카테고리</h4>
                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400">
                    @foreach($lowPerformance as $category)
                        <li>{{ $category['name'] }} ({{ $category['percentage'] }}%)</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    
    <!-- 푸터 -->
    <div class="text-center text-sm text-gray-500 dark:text-gray-400 pt-4 border-t">
        <p>이 보고서는 {{ now()->format('Y년 m월 d일 H:i') }}에 생성되었습니다.</p>
    </div>
</div>
