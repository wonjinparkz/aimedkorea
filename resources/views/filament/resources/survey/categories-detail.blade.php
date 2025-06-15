<div class="space-y-6">
    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
        <p class="text-sm text-blue-800 dark:text-blue-200">
            총 {{ count($allQuestions) }}개의 문항이 {{ count($categorizedQuestions) }}개의 카테고리로 구성되어 있습니다.
        </p>
    </div>
    
    @foreach($categorizedQuestions as $category)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ $category['name'] }}
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    ({{ count($category['questions']) }}개 문항)
                </span>
            </h3>
            
            <div class="space-y-3">
                @foreach($category['questions'] as $question)
                    <div class="border-l-4 border-gray-200 dark:border-gray-600 pl-4 py-2">
                        <div class="flex items-start space-x-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $question['index'] + 1 }}.
                            </span>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $question['label'] }}
                                </p>
                                @if($question['has_specific_checklist'])
                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                            개별 체크리스트 사용
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
    
    @php
        // 카테고리에 포함되지 않은 문항 확인
        $categorizedIndices = [];
        foreach($categorizedQuestions as $category) {
            foreach($category['questions'] as $question) {
                $categorizedIndices[] = $question['index'];
            }
        }
        
        $uncategorizedQuestions = [];
        foreach($allQuestions as $index => $question) {
            if (!in_array($index, $categorizedIndices)) {
                $uncategorizedQuestions[] = [
                    'index' => $index,
                    'label' => $question['label']
                ];
            }
        }
    @endphp
    
    @if(count($uncategorizedQuestions) > 0)
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                카테고리 미지정 문항
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    ({{ count($uncategorizedQuestions) }}개 문항)
                </span>
            </h3>
            
            <div class="space-y-3">
                @foreach($uncategorizedQuestions as $question)
                    <div class="border-l-4 border-gray-200 dark:border-gray-600 pl-4 py-2">
                        <div class="flex items-start space-x-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $question['index'] + 1 }}.
                            </span>
                            <p class="text-sm text-gray-700 dark:text-gray-300 flex-1">
                                {{ $question['label'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
