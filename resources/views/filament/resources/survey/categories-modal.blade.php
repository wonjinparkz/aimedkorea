<div class="space-y-4">
    @php
        // 문항을 순서대로 정리
        $orderedQuestions = [];
        $index = 0;
        foreach ($questions as $question) {
            if (!empty($question['label'])) {
                $orderedQuestions[$index] = $question;
                $index++;
            }
        }
    @endphp
    
    @foreach($categories as $category)
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                {{ $category['name'] }}
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    ({{ count($category['question_indices']) }}개 문항)
                </span>
            </h3>
            
            <div class="space-y-2">
                @foreach($category['question_indices'] as $index)
                    @php
                        $intIndex = intval($index);
                    @endphp
                    @if(isset($orderedQuestions[$intIndex]))
                        <div class="flex items-start space-x-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $intIndex + 1 }}.
                            </span>
                            <p class="text-sm text-gray-700 dark:text-gray-300 flex-1">
                                {{ $orderedQuestions[$intIndex]['label'] }}
                            </p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
</div>
