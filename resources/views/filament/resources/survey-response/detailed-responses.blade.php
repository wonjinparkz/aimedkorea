<div class="space-y-6">
    @php
        $categoryQuestions = $survey->getQuestionsByCategory();
        $orderedQuestions = $survey->getOrderedQuestions();
        
        // 카테고리별로 응답 그룹화
        $categorizedResponses = [];
        foreach ($categoryQuestions as $category) {
            $categoryResponses = [];
            foreach ($category['questions'] as $question) {
                if (isset($responses[$question['index']])) {
                    $categoryResponses[] = array_merge($responses[$question['index']], [
                        'question_index' => $question['index']
                    ]);
                }
            }
            if (count($categoryResponses) > 0) {
                $categorizedResponses[] = [
                    'category_name' => $category['name'],
                    'responses' => $categoryResponses
                ];
            }
        }
        
        // 카테고리에 포함되지 않은 응답
        $categorizedIndices = [];
        foreach ($categoryQuestions as $category) {
            foreach ($category['questions'] as $question) {
                $categorizedIndices[] = $question['index'];
            }
        }
        
        $uncategorizedResponses = [];
        foreach ($responses as $index => $response) {
            if (!in_array($index, $categorizedIndices)) {
                $uncategorizedResponses[] = array_merge($response, [
                    'question_index' => $index
                ]);
            }
        }
    @endphp
    
    <!-- 카테고리별 응답 -->
    @foreach($categorizedResponses as $categoryData)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 pb-2 border-b dark:border-gray-700">
                {{ $categoryData['category_name'] }}
            </h3>
            
            <div class="space-y-3">
                @foreach($categoryData['responses'] as $response)
                    <div class="border dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                        <div class="font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            {{ $response['question_index'] + 1 }}. {{ $response['question_label'] }}
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium">답변:</span> {{ $response['selected_label'] }}
                            </div>
                            <div class="text-sm px-3 py-1 rounded-full {{ $response['selected_score'] >= 3 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ($response['selected_score'] >= 2 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                {{ $response['selected_score'] }}점
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- 카테고리 소계 -->
                @php
                    $categoryTotal = array_sum(array_column($categoryData['responses'], 'selected_score'));
                @endphp
                <div class="mt-2 text-right text-sm font-semibold text-gray-600 dark:text-gray-400">
                    카테고리 합계: {{ $categoryTotal }}점
                </div>
            </div>
        </div>
    @endforeach
    
    <!-- 미분류 응답 -->
    @if(count($uncategorizedResponses) > 0)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 pb-2 border-b dark:border-gray-700">
                카테고리 미지정 문항
            </h3>
            
            <div class="space-y-3">
                @foreach($uncategorizedResponses as $response)
                    <div class="border dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                        <div class="font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            {{ $response['question_index'] + 1 }}. {{ $response['question_label'] }}
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="text-gray-600 dark:text-gray-400">
                                <span class="font-medium">답변:</span> {{ $response['selected_label'] }}
                            </div>
                            <div class="text-sm px-3 py-1 rounded-full {{ $response['selected_score'] >= 3 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ($response['selected_score'] >= 2 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                {{ $response['selected_score'] }}점
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- 전체 총점 -->
    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
        <div class="text-lg font-semibold text-blue-900 dark:text-blue-200">
            전체 총점: {{ array_sum(array_column($responses, 'selected_score')) }}점
        </div>
    </div>
</div>
