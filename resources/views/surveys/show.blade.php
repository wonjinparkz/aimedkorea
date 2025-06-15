<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 mb-3">{{ $survey->title }}</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">{{ $survey->description }}</p>
            </div>

            <!-- 진행도 표시 -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-700">진행 상황</span>
                    <span class="text-sm font-medium text-indigo-600" id="progress-text">1 / {{ count($survey->questions) }} 문항</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                    <div id="progress-bar" class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full transition-all duration-500 ease-out" style="width: {{ 100 / count($survey->questions) }}%">
                        <div class="h-full bg-white bg-opacity-30 animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- 설문 폼 -->
            <form id="survey-form" action="{{ route('surveys.store', $survey) }}" method="POST">
                @csrf
                
                <!-- 문항들 -->
                @foreach($survey->questions as $index => $question)
                    <div class="question-container {{ $index === 0 ? '' : 'hidden' }}" data-question="{{ $index }}">
                        <div class="bg-white rounded-2xl shadow-xl p-8 transform transition-all duration-500">
                            <!-- 문항 번호와 내용 -->
                            <div class="mb-8">
                                <div class="flex items-start">
                                    <span class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-full font-bold text-xl shadow-lg flex-shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <h2 class="ml-5 text-xl md:text-2xl font-semibold text-gray-800 leading-relaxed">
                                        {{ $question['label'] }}
                                    </h2>
                                </div>
                            </div>

                            <!-- 선택지 -->
                            <div class="space-y-3">
                                @php
                                    $options = isset($question['has_specific_checklist']) && $question['has_specific_checklist'] 
                                        ? $question['specific_checklist_items'] 
                                        : $survey->checklist_items;
                                @endphp
                                
                                @foreach($options as $optionIndex => $option)
                                    <label class="block cursor-pointer group">
                                        <input type="radio" 
                                               name="responses[{{ $index }}]" 
                                               value="{{ $optionIndex }}" 
                                               class="sr-only peer"
                                               required>
                                        <div class="relative border-2 border-gray-200 rounded-xl p-5 hover:border-indigo-400 hover:shadow-md peer-checked:border-indigo-600 peer-checked:bg-gradient-to-r peer-checked:from-indigo-50 peer-checked:to-purple-50 transition-all duration-300 transform hover:scale-[1.02]">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-6 h-6 border-2 border-gray-300 rounded-full relative transition-all duration-300 peer-checked:border-transparent peer-checked:bg-gradient-to-r peer-checked:from-indigo-600 peer-checked:to-purple-600 group-hover:border-gray-400">
                                                    <div class="absolute inset-1 bg-white rounded-full scale-0 transition-transform duration-300 peer-checked:scale-100"></div>
                                                </div>
                                                <span class="ml-4 text-gray-700 text-base peer-checked:text-gray-900 peer-checked:font-semibold transition-colors duration-300">
                                                    {{ $option['label'] }}
                                                </span>
                                            </div>
                                            <!-- 점수 배지 -->
                                            <div class="absolute top-3 right-3 px-3 py-1 bg-gray-100 text-gray-500 text-xs rounded-full peer-checked:bg-indigo-100 peer-checked:text-indigo-700 transition-all duration-300">
                                                {{ $option['score'] }}점
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- 네비게이션 버튼 -->
                <div class="mt-8 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                    <button type="button" 
                            id="prev-btn" 
                            class="flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-gray-300"
                            disabled>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        이전 문항
                    </button>

                    <a href="{{ route('surveys.index') }}" 
                       class="text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200 underline-offset-4 hover:underline">
                        설문 목록으로
                    </a>

                    <button type="button" 
                            id="next-btn" 
                            class="flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 shadow-lg">
                        다음 문항
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <button type="submit" 
                            id="submit-btn" 
                            class="hidden flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        설문 완료하기
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('survey-form');
            const questions = document.querySelectorAll('.question-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');
            
            let currentQuestion = 0;
            const totalQuestions = questions.length;

            function showQuestion(index) {
                questions.forEach(q => q.classList.add('hidden'));
                questions[index].classList.remove('hidden');
                
                // 진행도 업데이트
                const progress = ((index + 1) / totalQuestions) * 100;
                progressBar.style.width = progress + '%';
                progressText.textContent = `${index + 1} / ${totalQuestions} 문항`;
                
                // 버튼 상태 업데이트
                prevBtn.disabled = index === 0;
                
                if (index === totalQuestions - 1) {
                    nextBtn.classList.add('hidden');
                    submitBtn.classList.remove('hidden');
                } else {
                    nextBtn.classList.remove('hidden');
                    submitBtn.classList.add('hidden');
                }
                
                // 현재 문항이 답변되었는지 확인
                checkCurrentAnswer();
            }

            function checkCurrentAnswer() {
                const currentInputs = questions[currentQuestion].querySelectorAll('input[type="radio"]');
                const isAnswered = Array.from(currentInputs).some(input => input.checked);
                
                if (currentQuestion < totalQuestions - 1) {
                    nextBtn.disabled = !isAnswered;
                    nextBtn.classList.toggle('opacity-50', !isAnswered);
                    nextBtn.classList.toggle('cursor-not-allowed', !isAnswered);
                } else {
                    submitBtn.disabled = !isAnswered;
                    submitBtn.classList.toggle('opacity-50', !isAnswered);
                    submitBtn.classList.toggle('cursor-not-allowed', !isAnswered);
                }
            }

            // 라디오 버튼 변경 감지
            questions.forEach(question => {
                const radios = question.querySelectorAll('input[type="radio"]');
                radios.forEach(radio => {
                    radio.addEventListener('change', checkCurrentAnswer);
                });
            });

            prevBtn.addEventListener('click', function() {
                if (currentQuestion > 0) {
                    currentQuestion--;
                    showQuestion(currentQuestion);
                }
            });

            nextBtn.addEventListener('click', function() {
                const currentInputs = questions[currentQuestion].querySelectorAll('input[type="radio"]');
                const isAnswered = Array.from(currentInputs).some(input => input.checked);
                
                if (isAnswered && currentQuestion < totalQuestions - 1) {
                    currentQuestion++;
                    showQuestion(currentQuestion);
                }
            });

            // 초기 표시
            showQuestion(0);
        });
    </script>
</x-app-layout>
