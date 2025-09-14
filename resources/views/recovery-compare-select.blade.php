<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-green-600 mb-3">
                    회복 점수 비교
                </h1>
                <p class="text-xl text-gray-600">최대 5개의 설문 응답을 선택하여 비교하세요</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <form action="{{ route('recovery.compare') }}" method="POST" id="compareForm">
                    @csrf
                    
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-4">비교할 응답을 선택하세요 (2개 이상, 최대 5개)</p>
                        
                        @if($responses && $responses->count() > 0)
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($responses as $response)
                                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" 
                                               name="response_ids[]" 
                                               value="{{ $response->id }}"
                                               class="mr-3 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <span class="font-semibold text-gray-800">
                                                        {{ $response->survey ? $response->survey->getTitle(session('locale', 'kor')) : '설문' }}
                                                    </span>
                                                    <span class="ml-2 text-sm text-gray-500">
                                                        {{ $response->created_at->format('Y-m-d H:i') }}
                                                    </span>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-sm text-gray-600">총점: {{ $response->total_score ?? 0 }}점</div>
                                                    <div class="text-sm font-semibold text-green-600">회복: {{ 100 - ($response->total_score ?? 0) }}%</div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">비교할 설문 응답이 없습니다.</p>
                        @endif
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('recovery.dashboard') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                            취소
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                id="compareButton"
                                disabled>
                            비교하기
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="response_ids[]"]');
            const compareButton = document.getElementById('compareButton');
            const form = document.getElementById('compareForm');

            function updateButtonState() {
                const checkedCount = document.querySelectorAll('input[name="response_ids[]"]:checked').length;
                compareButton.disabled = checkedCount < 2 || checkedCount > 5;
                
                if (checkedCount > 5) {
                    alert('최대 5개까지만 선택할 수 있습니다.');
                    return false;
                }
                return true;
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        const checkedCount = document.querySelectorAll('input[name="response_ids[]"]:checked').length;
                        if (checkedCount > 5) {
                            this.checked = false;
                            alert('최대 5개까지만 선택할 수 있습니다.');
                        }
                    }
                    updateButtonState();
                });
            });

            form.addEventListener('submit', function(e) {
                const checkedCount = document.querySelectorAll('input[name="response_ids[]"]:checked').length;
                if (checkedCount < 2) {
                    e.preventDefault();
                    alert('최소 2개 이상 선택해주세요.');
                } else if (checkedCount > 5) {
                    e.preventDefault();
                    alert('최대 5개까지만 선택할 수 있습니다.');
                }
            });
        });
    </script>
</x-app-layout>