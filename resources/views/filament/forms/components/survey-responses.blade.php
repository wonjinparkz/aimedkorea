<div class="space-y-4">
    @if($getState())
        @foreach($getState() as $index => $response)
            <div class="border rounded-lg p-4 bg-gray-50">
                <div class="font-semibold text-gray-700 mb-2">
                    {{ $index + 1 }}. {{ $response['question_label'] }}
                </div>
                <div class="text-gray-600">
                    <span class="font-medium">답변:</span> {{ $response['selected_label'] }}
                    <span class="text-sm text-gray-500">({{ $response['selected_score'] }}점)</span>
                </div>
            </div>
        @endforeach
        
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <div class="text-lg font-semibold text-blue-900">
                총점: {{ array_sum(array_column($getState(), 'selected_score')) }}점
            </div>
        </div>
    @else
        <p class="text-gray-500">응답 데이터가 없습니다.</p>
    @endif
</div>
