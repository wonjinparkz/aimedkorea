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
                        
                        // 디버깅 정보
                        $debugInfo = [
                            'actualScore' => $actualScore,
                            'maxPossibleScore' => $maxPossibleScore,
                            'rawPercentage' => $rawPercentage,
                            'gaugePercentage' => $gaugePercentage
                        ];
                        
                        // 6개 구간 정의
                        $segments = [
                            ['name' => '붕괴', 'color' => '#991b1b', 'min' => 0, 'max' => 16.67],
                            ['name' => '위험', 'color' => '#dc2626', 'min' => 16.67, 'max' => 33.33],
                            ['name' => '주의', 'color' => '#f59e0b', 'min' => 33.33, 'max' => 50],
                            ['name' => '양호', 'color' => '#10b981', 'min' => 50, 'max' => 66.67],
                            ['name' => '우수', 'color' => '#059669', 'min' => 66.67, 'max' => 83.33],
                            ['name' => '최적', 'color' => '#047857', 'min' => 83.33, 'max' => 100]
                        ];
                        
                        // 현재 구간 찾기
                        $currentSegmentIndex = 0;
                        foreach ($segments as $index => $segment) {
                            if ($gaugePercentage >= $segment['min'] && $gaugePercentage <= $segment['max']) {
                                $currentSegmentIndex = $index;
                                break;
                            }
                        }
                        $currentSegment = $segments[$currentSegmentIndex];
                        
                        // 바늘 각도 계산 (-90도에서 90도)
                        $needleAngle = -90 + ($gaugePercentage * 1.8);
                    @endphp
                    
                    <!-- 계기판 컨테이너 -->
                    <div class="relative mx-auto" style="width: 100%; max-width: 400px; height: 250px;">
                        <!-- 계기판 배경 -->
                        <div class="absolute inset-0">
                            <svg viewBox="0 0 200 100" style="width: 100%; height: 100%;">
                                <!-- 배경 반원 -->
                                <path d="M 20 90 A 70 70 0 0 1 180 90" 
                                      fill="none" 
                                      stroke="#e5e7eb" 
                                      stroke-width="15" />
                                
                                <!-- 색상 구간들 -->
                                @foreach($segments as $index => $segment)
                                    @php
                                        $startAngle = -90 + ($segment['min'] * 1.8);
                                        $endAngle = -90 + ($segment['max'] * 1.8);
                                        $startRad = deg2rad($startAngle);
                                        $endRad = deg2rad($endAngle);
                                        
                                        $x1 = 100 + 70 * cos($startRad);
                                        $y1 = 90 + 70 * sin($startRad);
                                        $x2 = 100 + 70 * cos($endRad);
                                        $y2 = 90 + 70 * sin($endRad);
                                        
                                        $largeArc = ($segment['max'] - $segment['min']) > 50 ? 1 : 0;
                                    @endphp
                                    <path d="M {{ $x1 }} {{ $y1 }} A 70 70 0 {{ $largeArc }} 1 {{ $x2 }} {{ $y2 }}"
                                          fill="none"
                                          stroke="{{ $segment['color'] }}"
                                          stroke-width="15"
                                          opacity="{{ $currentSegmentIndex === $index ? '1' : '0.3' }}"
                                          class="transition-opacity duration-500" />
                                @endforeach
                                
                                <!-- 눈금 표시 -->
                                @for($i = 0; $i <= 100; $i += 20)
                                    @php
                                        $tickAngle = -90 + ($i * 1.8);
                                        $tickRad = deg2rad($tickAngle);
                                        $x1 = 100 + 60 * cos($tickRad);
                                        $y1 = 90 + 60 * sin($tickRad);
                                        $x2 = 100 + 80 * cos($tickRad);
                                        $y2 = 90 + 80 * sin($tickRad);
                                        $textX = 100 + 50 * cos($tickRad);
                                        $textY = 90 + 50 * sin($tickRad);
                                    @endphp
                                    <line x1="{{ $x1 }}" y1="{{ $y1 }}" 
                                          x2="{{ $x2 }}" y2="{{ $y2 }}" 
                                          stroke="#9ca3af" 
                                          stroke-width="2" />
                                    <text x="{{ $textX }}" y="{{ $textY }}" 
                                          text-anchor="middle" 
                                          dominant-baseline="middle"
                                          fill="#6b7280"
                                          font-size="10">{{ $i }}</text>
                                @endfor
                                
                                <!-- 바늘 -->
                                <g transform="translate(100, 90)" id="gauge-needle">
                                    <line x1="0" y1="0" 
                                          x2="0" y2="-55" 
                                          stroke="#1f2937" 
                                          stroke-width="3"
                                          stroke-linecap="round"
                                          transform="rotate({{ $needleAngle }})"
                                          class="transition-transform duration-1000 ease-out" />
                                    <circle cx="0" cy="0" r="6" fill="#1f2937" />
                                </g>
                            </svg>
                        </div>
                        
                        <!-- 중앙 표시 -->
                        <div class="absolute inset-0 flex items-end justify-center pb-8">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-gray-800">{{ round($gaugePercentage) }}%</div>
                                <div class="text-sm text-gray-500">건강 지수</div>
                            </div>
                        </div>
                        
                        <!-- 라벨 -->
                        <div class="absolute bottom-0 left-0 right-0 flex justify-between px-2">
                            @foreach($segments as $segment)
                                <div class="text-xs {{ $currentSegment['name'] === $segment['name'] ? 'font-bold text-gray-800' : 'text-gray-400' }}">
                                    {{ $segment['name'] }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- 점수 정보 -->
                    <div class="text-center mt-8">
                        <div class="mb-4">
                            <p class="text-2xl font-bold text-gray-800">종합 건강 지수</p>
                            <p class="text-sm text-gray-500 mt-1">
                                실제 점수: {{ $actualScore }}점 / {{ $maxPossibleScore }}점
                            </p>
                            <!-- 디버깅 정보 (개발 중에만 표시) -->
                            @if(config('app.debug'))
                                <div class="mt-2 p-2 bg-gray-100 rounded text-xs text-gray-600">
                                    <p>원점수: {{ $rawPercentage }}% (높을수록 나쁨)</p>
                                    <p>역전점수: {{ $gaugePercentage }}% (높을수록 좋음)</p>
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
    <script>
        // 페이지 로드 시 애니메이션
        document.addEventListener('DOMContentLoaded', function() {
            // 바늘 애니메이션을 위한 초기 설정
            const needle = document.querySelector('#gauge-needle line');
            if (needle) {
                // 초기 위치를 -90도로 설정
                needle.style.transform = 'rotate(-90deg)';
                
                // 약간의 지연 후 목표 각도로 회전
                setTimeout(() => {
                    needle.style.transform = 'rotate({{ $needleAngle }}deg)';
                }, 100);
            }
            
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
        
        /* 부드러운 전환 효과 */
        #gauge-needle line {
            transition: transform 1.5s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: center;
        }
    </style>
    @endpush
</x-app-layout>
