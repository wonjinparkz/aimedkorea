<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 mb-3">{{ $survey->title }}</h1>
                <p class="text-xl text-gray-600">귀하의 전반적인 상태를 나타내는 종합 지수입니다.</p>
            </div>

            <!-- 계기판 스타일 점수 표시 -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <div class="max-w-lg mx-auto">
                    @php
                        // 실제 점수와 최대 점수 계산
                        $actualScore = $response->total_score;
                        $maxPossibleScore = count($survey->questions) * 4;
                        $rawPercentage = round(($actualScore / $maxPossibleScore) * 100);
                        
                        // 계기판용 역전 백분율 (낮은 점수가 좋은 상태)
                        $gaugePercentage = 100 - $rawPercentage;
                        
                        // 6개 구간 정의
                        $segments = [
                            ['name' => '붕괴', 'color' => '#991b1b', 'range' => [0, 16.67]],
                            ['name' => '위험', 'color' => '#dc2626', 'range' => [16.67, 33.33]],
                            ['name' => '주의', 'color' => '#f59e0b', 'range' => [33.33, 50]],
                            ['name' => '양호', 'color' => '#10b981', 'range' => [50, 66.67]],
                            ['name' => '우수', 'color' => '#059669', 'range' => [66.67, 83.33]],
                            ['name' => '최적', 'color' => '#047857', 'range' => [83.33, 100]]
                        ];
                        
                        // 현재 구간 찾기
                        $currentSegmentIndex = 0;
                        foreach ($segments as $index => $segment) {
                            if ($gaugePercentage >= $segment['range'][0] && $gaugePercentage <= $segment['range'][1]) {
                                $currentSegmentIndex = $index;
                                break;
                            }
                        }
                        $currentSegment = $segments[$currentSegmentIndex];
                    @endphp
                    
                    <!-- Chart.js 게이지 차트 컨테이너 -->
                    <div class="relative mx-auto" style="width: 100%; max-width: 400px; height: 250px;">
                        <canvas id="gaugeChart"></canvas>
                        
                        <!-- 중앙 텍스트 -->
                        <div class="absolute inset-0 flex items-center justify-center" style="margin-top: 50px;">
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-800">{{ round($gaugePercentage) }}%</div>
                                <div class="text-sm text-gray-500 mt-1">건강 지수</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 구간 라벨 -->
                    <div class="flex justify-between px-4 mt-2">
                        @foreach($segments as $index => $segment)
                            <div class="text-xs {{ $currentSegmentIndex === $index ? 'font-bold text-gray-800' : 'text-gray-400' }}">
                                {{ $segment['name'] }}
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- 점수 정보 -->
                    <div class="text-center mt-8">
                        <div class="mb-4">
                            <p class="text-2xl font-bold text-gray-800">종합 건강 지수</p>
                            <p class="text-sm text-gray-500 mt-1">
                                실제 점수: {{ $actualScore }}점 / {{ $maxPossibleScore }}점
                            </p>
                            @if(config('app.debug'))
                                <div class="mt-2 p-2 bg-gray-100 rounded text-xs text-gray-600">
                                    <p>원점수: {{ $rawPercentage }}% (높을수록 나쁨)</p>
                                    <p>건강지수: {{ $gaugePercentage }}% (높을수록 좋음)</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- 현재 상태 표시 -->
                        <div class="mb-6">
                            <span class="inline-flex items-center px-8 py-4 rounded-full text-xl font-bold shadow-lg bg-gradient-to-r from-gray-50 to-gray-100 border-2" 
                                  style="border-color: {{ $currentSegment['color'] }}; color: {{ $currentSegment['color'] }}">
                                {{ $currentSegment['name'] }} 상태
                            </span>
                        </div>
                        
                        <!-- 상태별 설명 -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <p class="text-gray-700 leading-relaxed">
                                @if($gaugePercentage >= 83.33)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">최적의 상태입니다!</span> 현재의 우수한 컨디션을 계속 유지하시기 바랍니다.
                                @elseif($gaugePercentage >= 66.67)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">우수한 상태입니다.</span> 조금만 더 노력하면 최적 상태에 도달할 수 있습니다.
                                @elseif($gaugePercentage >= 50)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">양호한 상태입니다.</span> 지속적인 관리로 더 나은 상태를 만들어보세요.
                                @elseif($gaugePercentage >= 33.33)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">주의가 필요한 상태입니다.</span> 생활 습관 개선이 시급합니다.
                                @elseif($gaugePercentage >= 16.67)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">위험한 상태입니다.</span> 즉시 전문가의 도움을 받으시기 바랍니다.
                                @else
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">매우 심각한 상태입니다.</span> 반드시 전문의와 상담하여 적극적인 치료를 받으세요.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 카테고리별 분석 -->
            @if(!empty($categoryAnalysis))
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">카테고리별 분석</h2>
                    
                    <div class="space-y-4">
                        @foreach($categoryAnalysis as $category)
                            <div>
                                <div class="flex justify-between mb-2">
                                    <div>
                                        <span class="text-gray-700 font-medium">{{ $category['name'] }}</span>
                                        @if(isset($category['answered_count']) && isset($category['question_count']))
                                            <span class="text-sm text-gray-500 ml-2">
                                                ({{ $category['answered_count'] }}/{{ $category['question_count'] }}개 응답)
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-gray-600 font-semibold">{{ $category['percentage'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000 ease-out relative {{ $category['percentage'] >= 70 ? 'bg-gradient-to-r from-green-500 to-green-600' : ($category['percentage'] >= 50 ? 'bg-gradient-to-r from-yellow-500 to-yellow-600' : ($category['percentage'] >= 30 ? 'bg-gradient-to-r from-orange-500 to-orange-600' : 'bg-gradient-to-r from-red-500 to-red-600')) }}" 
                                         style="width: {{ $category['percentage'] }}%">
                                        <div class="absolute inset-0 bg-white bg-opacity-20"></div>
                                    </div>
                                </div>
                                @if(isset($category['score']) && isset($category['max_score']))
                                    <div class="text-xs text-gray-500 mt-1 text-right">
                                        원점수: {{ $category['score'] }}/{{ $category['max_score'] }}점
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- 카테고리 분석 설명 -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold">카테고리별 점수 해석:</span><br>
                            70% 이상: 우수한 상태입니다.<br>
                            50-69%: 양호한 상태이나 개선의 여지가 있습니다.<br>
                            30-49%: 주의가 필요한 상태입니다.<br>
                            30% 미만: 즉각적인 개선이 필요합니다.
                        </p>
                    </div>
                </div>
            @endif

            <!-- 액션 버튼 -->
            <div class="flex justify-center space-x-4 no-print">
                <a href="{{ route('surveys.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    설문 목록으로
                </a>
                
                <a href="{{ route('surveys.show', $survey) }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    다시 테스트하기
                </a>
                
                <button onclick="window.print()" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    결과 인쇄
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Chart.js 라이브러리 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 게이지 데이터
            const gaugePercentage = {{ $gaugePercentage }};
            const segments = @json($segments);
            const currentSegmentIndex = {{ $currentSegmentIndex }};
            
            // 각 세그먼트별 데이터 준비
            const segmentData = [];
            const segmentColors = [];
            const segmentBorderColors = [];
            
            segments.forEach((segment, index) => {
                // 각 세그먼트는 16.67%씩 차지
                segmentData.push(16.67);
                
                // 현재 값이 포함된 구간까지는 실제 색상, 나머지는 옅은 색
                if (index <= currentSegmentIndex) {
                    segmentColors.push(segment.color);
                    segmentBorderColors.push(segment.color);
                } else {
                    segmentColors.push(segment.color + '20'); // 20% 투명도
                    segmentBorderColors.push(segment.color + '40'); // 40% 투명도
                }
            });
            
            // 하단 반원을 만들기 위한 빈 데이터 추가 (50%)
            segmentData.push(100);
            segmentColors.push('transparent');
            segmentBorderColors.push('transparent');
            
            // Chart.js 설정
            const ctx = document.getElementById('gaugeChart').getContext('2d');
            const gaugeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: segmentData,
                        backgroundColor: segmentColors,
                        borderColor: segmentBorderColors,
                        borderWidth: 2,
                        circumference: 180,
                        rotation: 270,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    }
                }
            });
            
            // 카테고리별 진행도 바 애니메이션
            const progressBars = document.querySelectorAll('.space-y-4 [style*="width"]');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
    @endpush
</x-app-layout>
