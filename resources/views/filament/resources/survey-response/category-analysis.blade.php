<div class="space-y-6">
    @php
        $analysisData = $analysisData ?? [];
        $categoryScores = $analysisData['category_scores'] ?? [];
        $totalScore = $analysisData['total_score'] ?? 0;
    @endphp
    
    <!-- 전체 요약 -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                    {{ $totalScore }}점
                </div>
                <div class="text-sm text-blue-600 dark:text-blue-300">
                    전체 총점
                </div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                    {{ count($categoryScores) }}개
                </div>
                <div class="text-sm text-blue-600 dark:text-blue-300">
                    카테고리
                </div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                    {{ $analysisData['response_count'] ?? 0 }} / {{ $analysisData['question_count'] ?? 0 }}
                </div>
                <div class="text-sm text-blue-600 dark:text-blue-300">
                    응답 문항
                </div>
            </div>
        </div>
    </div>
    
    <!-- 카테고리별 분석 -->
    @foreach($categoryScores as $category)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $category['name'] }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $category['answered_count'] }} / {{ $category['question_count'] }}개 문항 응답
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold {{ $category['percentage'] >= 70 ? 'text-green-600' : ($category['percentage'] >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $category['percentage'] }}%
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $category['score'] }} / {{ $category['max_score'] }}점
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-300 {{ $category['percentage'] >= 70 ? 'bg-green-500' : ($category['percentage'] >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}"
                     style="width: {{ $category['percentage'] }}%">
                </div>
            </div>
            
            <!-- 점수 분석 -->
            <div class="mt-4 grid grid-cols-3 gap-4 text-sm">
                <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                    <div class="font-semibold text-gray-700 dark:text-gray-300">획득 점수</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $category['score'] }}점</div>
                </div>
                <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                    <div class="font-semibold text-gray-700 dark:text-gray-300">최대 점수</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $category['max_score'] }}점</div>
                </div>
                <div class="text-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                    <div class="font-semibold text-gray-700 dark:text-gray-300">평균 점수</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ $category['answered_count'] > 0 ? round($category['score'] / $category['answered_count'], 1) : 0 }}점
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    
    <!-- 미분류 문항 -->
    @if(($analysisData['uncategorized_count'] ?? 0) > 0)
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                카테고리 미지정 문항
            </h3>
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $analysisData['uncategorized_count'] }}개 문항
                </p>
                <div class="text-lg font-bold text-gray-700 dark:text-gray-300">
                    {{ $analysisData['uncategorized_score'] }}점
                </div>
            </div>
        </div>
    @endif
    
    <!-- 분석 해석 가이드 -->
    <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">점수 해석 가이드</h4>
        <div class="grid grid-cols-3 gap-2 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span class="text-gray-600 dark:text-gray-400">70% 이상: 우수</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                <span class="text-gray-600 dark:text-gray-400">40-69%: 보통</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                <span class="text-gray-600 dark:text-gray-400">40% 미만: 개선 필요</span>
            </div>
        </div>
    </div>
</div>
