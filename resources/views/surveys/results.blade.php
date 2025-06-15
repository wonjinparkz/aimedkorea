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
                        // 6개 구간 정의
                        $segments = [
                            ['name' => '붕괴', 'color' => '#991b1b', 'lightColor' => '#fecaca', 'start' => 0, 'end' => 16.67],
                            ['name' => '위험', 'color' => '#dc2626', 'lightColor' => '#fecaca', 'start' => 16.67, 'end' => 33.33],
                            ['name' => '주의', 'color' => '#f59e0b', 'lightColor' => '#fed7aa', 'start' => 33.33, 'end' => 50],
                            ['name' => '양호', 'color' => '#10b981', 'lightColor' => '#bbf7d0', 'start' => 50, 'end' => 66.67],
                            ['name' => '우수', 'color' => '#059669', 'lightColor' => '#a7f3d0', 'start' => 66.67, 'end' => 83.33],
                            ['name' => '최적', 'color' => '#047857', 'lightColor' => '#86efac', 'start' => 83.33, 'end' => 100]
                        ];
                        
                        // 현재 퍼센트가 속한 구간 찾기
                        $activeSegment = 0;
                        foreach ($segments as $index => $segment) {
                            if ($percentage >= $segment['start'] && $percentage <= $segment['end']) {
                                $activeSegment = $index;
                                break;
                            }
                        }
                    @endphp
                    
                    <!-- 게이지 차트 -->
                    <div class="relative mx-auto" style="width: 100%; max-width: 400px; height: 220px;">
                        @php
                            $centerX = 150;
                            $centerY = 130;
                            $radius = 100;
                        @endphp
                        
                        <!-- SVG 게이지 -->
                        <svg viewBox="0 0 300 160" style="width: 100%; height: 100%;">
                            <!-- 배경 반원 -->
                            <path d="M 50 130 A 100 100 0 0 1 250 130" 
                                  fill="none" 
                                  stroke="#e5e7eb" 
                                  stroke-width="20" 
                                  stroke-linecap="round" />
                            
                            <!-- 6개 구간 -->
                            @foreach($segments as $index => $segment)
                                @php
                                    // 각 구간의 시작과 끝 각도 (180도를 6등분)
                                    $startAngle = 180 - ($index * 30);
                                    $endAngle = 180 - (($index + 1) * 30);
                                    
                                    // 라디안으로 변환
                                    $startRad = deg2rad($startAngle);
                                    $endRad = deg2rad($endAngle);
                                    
                                    // 좌표 계산
                                    $x1 = $centerX + $radius * cos($startRad);
                                    $y1 = $centerY - $radius * sin($startRad);
                                    $x2 = $centerX + $radius * cos($endRad);
                                    $y2 = $centerY - $radius * sin($endRad);
                                @endphp
                                <path d="M {{ number_format($x1, 1) }} {{ number_format($y1, 1) }} A {{ $radius }} {{ $radius }} 0 0 0 {{ number_format($x2, 1) }} {{ number_format($y2, 1) }}" 
                                      fill="none" 
                                      stroke="{{ $segment['color'] }}" 
                                      stroke-width="20"
                                      stroke-linecap="butt"
                                      opacity="{{ $activeSegment === $index ? '1' : '0.2' }}" />
                            @endforeach
                            
                            <!-- 구분선 -->
                            @for($i = 1; $i < 6; $i++)
                                @php
                                    $angle = 180 - ($i * 30);
                                    $rad = deg2rad($angle);
                                    $x1 = $centerX + ($radius - 10) * cos($rad);
                                    $y1 = $centerY - ($radius - 10) * sin($rad);
                                    $x2 = $centerX + ($radius + 10) * cos($rad);
                                    $y2 = $centerY - ($radius + 10) * sin($rad);
                                @endphp
                                <line x1="{{ number_format($x1, 1) }}" y1="{{ number_format($y1, 1) }}" 
                                      x2="{{ number_format($x2, 1) }}" y2="{{ number_format($y2, 1) }}" 
                                      stroke="white" 
                                      stroke-width="3" />
                            @endfor
                            
                            <!-- 포인터 -->
                            @php
                                $pointerAngle = 180 - ($percentage * 1.8);
                                $pointerRad = deg2rad($pointerAngle);
                                $pointerX = $centerX + ($radius - 20) * cos($pointerRad);
                                $pointerY = $centerY - ($radius - 20) * sin($pointerRad);
                            @endphp
                            <line x1="{{ $centerX }}" y1="{{ $centerY }}" 
                                  x2="{{ number_format($pointerX, 1) }}" y2="{{ number_format($pointerY, 1) }}" 
                                  stroke="#1f2937" 
                                  stroke-width="4"
                                  stroke-linecap="round" />
                            <circle cx="{{ $centerX }}" cy="{{ $centerY }}" r="8" fill="#1f2937" />
                            
                            <!-- 중앙 텍스트 -->
                            <text x="{{ $centerX }}" y="115" text-anchor="middle" class="text-4xl font-bold fill-gray-800">
                                {{ $percentage }}%
                            </text>
                        </svg>
                        
                        <!-- 레벨 라벨들 -->
                        <div class="absolute bottom-0 left-0 right-0 flex justify-between px-1">
                            @foreach($segments as $index => $segment)
                                <div class="text-xs font-medium {{ $activeSegment === $index ? 'text-gray-900 font-bold' : 'text-gray-400' }} transition-all duration-500">
                                    {{ $segment['name'] }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- 점수 정보 -->
                    <div class="text-center mt-8">
                        <div class="mb-4">
                            <p class="text-2xl font-bold text-gray-800">노화 전환 임계</p>
                            <p class="text-sm text-gray-500 mt-1">(0% 불과 ~ 50% 양호 ~ 100% 확적)</p>
                        </div>
                        
                        <!-- 현재 상태 표시 -->
                        <div class="mb-6">
                            @php
                                $currentSegment = $segments[$activeSegment] ?? $segments[0];
                            @endphp
                            <span class="inline-flex items-center px-8 py-4 rounded-full text-xl font-bold shadow-lg bg-gradient-to-r from-gray-50 to-gray-100 border-2" 
                                  style="border-color: {{ $currentSegment['color'] }}; color: {{ $currentSegment['color'] }}">
                                {{ $currentSegment['name'] }} 상태
                            </span>
                        </div>
                        
                        <!-- 상태별 설명 -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <p class="text-gray-700 leading-relaxed">
                                @if($activeSegment === 5)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">최적의 상태입니다!</span> 현재의 우수한 컨디션을 계속 유지하시기 바랍니다.
                                @elseif($activeSegment === 4)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">우수한 상태입니다.</span> 조금만 더 노력하면 최적 상태에 도달할 수 있습니다.
                                @elseif($activeSegment === 3)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">양호한 상태입니다.</span> 지속적인 관리로 더 나은 상태를 만들어보세요.
                                @elseif($activeSegment === 2)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">주의가 필요한 상태입니다.</span> 생활 습관 개선이 시급합니다.
                                @elseif($activeSegment === 1)
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
                                        득점: {{ $category['score'] }}/{{ $category['max_score'] }}점
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
            <div class="flex justify-center space-x-4">
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
        // 페이지 로드 시 애니메이션 효과
        document.addEventListener('DOMContentLoaded', function() {
            // 카테고리별 진행도 바 애니메이션
            const progressBars = document.querySelectorAll('[style*="width"]');
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
