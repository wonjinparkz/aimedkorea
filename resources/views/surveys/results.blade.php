<x-app-layout>
        <meta name="robots" content="noindex, nofollow">
        <meta name="googlebot" content="noindex, nofollow">
        <meta name="description" content="Private survey results - not for indexing">
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 mb-3">{{ $survey->title }}</h1>
                <p class="text-xl text-gray-600">귀하의 디지털 노화 상태를 나타내는 노화지수입니다.</p>
                
                @php
                    $currentLang = session('locale', 'kor');
                    // 노화지수 계산
                    $actualScore = $response->total_score;
                    $maxPossibleScore = count($survey->questions) * 4;
                    $agingIndex = round(($actualScore / $maxPossibleScore) * 100);
                    
                    // 노화지수에 따른 결과 해설 가져오기
                    $resultCommentary = $survey->getResultCommentary($currentLang, $agingIndex);
                @endphp
                
                @if($resultCommentary)
                    <div class="mt-6 p-6 bg-blue-50 rounded-xl border border-blue-200">
                        <div class="prose prose-blue mx-auto">
                            {!! $resultCommentary !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- 면책 고지 (상단) -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-amber-800">
                        <p class="font-medium mb-1">중요 안내</p>
                        <p>본 결과는 웰니스 목적의 자가평가 정보이며, 질병의 진단·치료·예방을 위한 것이 아닙니다.</p>
                        <p class="mt-2">화면의 수치는 <strong>내부 컨디션 지표(H/레벨/Model Confidence)</strong>로, 개인 특성에 따라 달라질 수 있습니다.</p>
                    </div>
                </div>
            </div>

            <!-- 계기판 스타일 점수 표시 -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <div class="max-w-lg mx-auto">
                    @php
                        // 노화지수 기준 6개 구간 정의 (역순으로 표시하기 위해)
                        $segments = [
                            ['name' => '최적', 'color' => '#047857', 'range' => [0, 15]],      // 0~15%
                            ['name' => '우수', 'color' => '#059669', 'range' => [16, 30]],     // 16~30%
                            ['name' => '양호', 'color' => '#10b981', 'range' => [31, 50]],     // 31~50%
                            ['name' => '주의', 'color' => '#f59e0b', 'range' => [51, 70]],     // 51~70%
                            ['name' => '위험', 'color' => '#dc2626', 'range' => [71, 85]],     // 71~85%
                            ['name' => '붕괴', 'color' => '#991b1b', 'range' => [86, 100]]     // 86~100%
                        ];
                        
                        // 현재 구간 찾기 (노화지수 기준)
                        $currentSegmentIndex = 5; // 기본값: 붕괴
                        foreach ($segments as $index => $segment) {
                            if ($agingIndex >= $segment['range'][0] && $agingIndex <= $segment['range'][1]) {
                                $currentSegmentIndex = $index;
                                break;
                            }
                        }
                        $currentSegment = $segments[$currentSegmentIndex];
                        
                        // 게이지 표시를 위한 역전된 백분율 (시각적 표현용)
                        $gaugePercentage = 100 - $agingIndex;
                    @endphp
                    
                    <!-- Chart.js 게이지 차트 컨테이너 -->
                    <div class="relative mx-auto" style="width: 100%; max-width: 400px; height: 250px;">
                        <canvas id="gaugeChart"></canvas>
                        
                        <!-- 중앙 텍스트 (하단 위치) -->
                        <div class="absolute bottom-0 left-0 right-0 flex justify-center pb-12">
                            <div class="text-center">
                                <div class="text-5xl font-bold text-gray-800">{{ $agingIndex }}%</div>
                                <div class="text-sm text-gray-500 mt-1">노화지수</div>
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
                            <p class="text-2xl font-bold text-gray-800">디지털 노화지수</p>
                            <p class="text-sm text-gray-500 mt-1">
                                실제 점수: {{ $actualScore }}점 / {{ $maxPossibleScore }}점 ({{ $agingIndex }}%)
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                낮은 노화지수일수록 좋은 상태입니다
                            </p>
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
                                @if($agingIndex <= 15)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">최적의 상태입니다!</span> 디지털 노화가 거의 진행되지 않은 매우 좋은 상태입니다.
                                @elseif($agingIndex <= 30)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">우수한 상태입니다.</span> 디지털 노화가 경미한 수준이며, 조금만 더 관리하면 최적 상태를 유지할 수 있습니다.
                                @elseif($agingIndex <= 50)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">양호한 상태입니다.</span> 디지털 노화가 진행되고 있지만 관리 가능한 수준입니다.
                                @elseif($agingIndex <= 70)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">주의가 필요한 상태입니다.</span> 디지털 노화가 상당히 진행되어 적극적인 관리가 필요합니다.
                                @elseif($agingIndex <= 85)
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">위험한 상태입니다.</span> 디지털 노화가 심각한 수준으로 즉시 전문가의 도움이 필요합니다.
                                @else
                                    <span class="font-semibold" style="color: {{ $currentSegment['color'] }}">매우 심각한 상태입니다.</span> 디지털 노화가 극심한 수준으로 반드시 전문의와 상담하여 적극적인 치료를 받으세요.
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
                    
                    @php
                        $categoryAnalysisDescription = $survey->getCategoryAnalysisDescription($currentLang);
                    @endphp
                    
                    @if($categoryAnalysisDescription)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <div class="prose prose-sm prose-gray max-w-none">
                                {!! $categoryAnalysisDescription !!}
                            </div>
                        </div>
                    @endif
                    
                    @php
                        // 카테고리를 점수 기준으로 정렬 (응답이 있는 카테고리만)
                        $scoredCategories = collect($categoryAnalysis)->filter(function($cat) {
                            return $cat['percentage'] !== null && $cat['answered_count'] > 0;
                        })->sortByDesc('percentage')->values();
                        
                        $top3Categories = $scoredCategories->take(3);
                        $bottom3Categories = $scoredCategories->slice(-3)->reverse()->values();
                    @endphp
                    
                    @if($scoredCategories->count() >= 3)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                            <!-- Top 3 카테고리 -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    우수 영역 TOP 3
                                </h3>
                                <div class="space-y-2">
                                    @foreach($top3Categories as $index => $category)
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center">
                                                <span class="text-green-600 font-bold mr-2">{{ $index + 1 }}.</span>
                                                <span class="text-gray-700">F{{ collect($categoryAnalysis)->search(function($item) use ($category) { 
                                                    return $item['name'] === $category['name']; 
                                                }) + 1 }}. {{ $category['name'] }}</span>
                                            </div>
                                            <span class="font-semibold text-green-700">{{ $category['percentage'] }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Bottom 3 카테고리 -->
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-red-800 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    개선 필요 영역
                                </h3>
                                <div class="space-y-2">
                                    @foreach($bottom3Categories as $index => $category)
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center">
                                                <span class="text-red-600 font-bold mr-2">{{ $index + 1 }}.</span>
                                                <span class="text-gray-700">F{{ collect($categoryAnalysis)->search(function($item) use ($category) { 
                                                    return $item['name'] === $category['name']; 
                                                }) + 1 }}. {{ $category['name'] }}</span>
                                            </div>
                                            <span class="font-semibold text-red-700">{{ $category['percentage'] }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="space-y-8 pt-8">
                        @foreach($categoryAnalysis as $index => $category)
                            <div>
                                <div class="flex justify-between mb-2">
                                    <div class="flex items-center">
                                        @php
                                            // 상태 뱃지 결정 (100%에 가까울수록 최적, 0%에 가까울수록 붕괴)
                                            $statusBadge = '';
                                            $badgeColor = '';
                                            if ($category['percentage'] !== null && $category['answered_count'] > 0) {
                                                if ($category['percentage'] >= 85) {
                                                    $statusBadge = '최적';
                                                    $badgeColor = 'bg-green-100 text-green-800 border-green-300';
                                                } elseif ($category['percentage'] >= 70) {
                                                    $statusBadge = '우수';
                                                    $badgeColor = 'bg-blue-100 text-blue-800 border-blue-300';
                                                } elseif ($category['percentage'] >= 50) {
                                                    $statusBadge = '양호';
                                                    $badgeColor = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                                                } elseif ($category['percentage'] >= 30) {
                                                    $statusBadge = '주의';
                                                    $badgeColor = 'bg-orange-100 text-orange-800 border-orange-300';
                                                } elseif ($category['percentage'] >= 15) {
                                                    $statusBadge = '위험';
                                                    $badgeColor = 'bg-red-100 text-red-800 border-red-300';
                                                } else {
                                                    $statusBadge = '붕괴';
                                                    $badgeColor = 'bg-red-200 text-red-900 border-red-400';
                                                }
                                            }
                                        @endphp
                                        
                                        @if($statusBadge)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }} mr-2">
                                                {{ $statusBadge }}
                                            </span>
                                        @endif
                                        <span class="text-gray-500 font-semibold mr-2">F{{ $index + 1 }}.</span>
                                        <span class="text-gray-700 font-medium">{{ $category['name'] }}</span>
                                    </div>
                                    @if($category['percentage'] !== null)
                                        <span class="text-gray-600 font-semibold">{{ $category['percentage'] }}%</span>
                                    @else
                                        <span class="text-gray-400 text-sm">미응답</span>
                                    @endif
                                </div>
                                
                                @if(!empty($category['description']))
                                    <p class="text-sm text-gray-600 mb-2">{{ $category['description'] }}</p>
                                @endif
                                <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                                    @php
                                        // 카테고리별 노화지수에 따른 색상 결정 (역전된 백분율 사용)
                                        $categoryAgingIndex = 100 - $category['percentage'];
                                        if ($categoryAgingIndex <= 15) {
                                            $barColor = 'bg-gradient-to-r from-green-500 to-green-600'; // 최적
                                        } elseif ($categoryAgingIndex <= 30) {
                                            $barColor = 'bg-gradient-to-r from-blue-500 to-blue-600'; // 우수
                                        } elseif ($categoryAgingIndex <= 50) {
                                            $barColor = 'bg-gradient-to-r from-yellow-500 to-yellow-600'; // 양호
                                        } elseif ($categoryAgingIndex <= 70) {
                                            $barColor = 'bg-gradient-to-r from-orange-500 to-orange-600'; // 주의
                                        } elseif ($categoryAgingIndex <= 85) {
                                            $barColor = 'bg-gradient-to-r from-red-500 to-red-600'; // 위험
                                        } else {
                                            $barColor = 'bg-gradient-to-r from-red-800 to-red-900'; // 붕괴
                                        }
                                    @endphp
                                    @if($category['percentage'] !== null && $category['answered_count'] > 0)
                                        <div class="h-full rounded-full transition-all duration-1000 ease-out relative {{ $barColor }}" 
                                             style="width: {{ $category['percentage'] }}%">
                                            <div class="absolute inset-0 bg-white bg-opacity-20"></div>
                                        </div>
                                    @else
                                        <div class="h-full flex items-center justify-center text-xs text-gray-500">
                                            응답 데이터 없음
                                        </div>
                                    @endif
                                </div>
                                
                                @if(!empty($category['result_description']))
                                    <div class="mt-2 p-3 bg-gray-50 rounded text-sm text-gray-700">
                                        {{ $category['result_description'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
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
                
                @auth
                    <a href="{{ route('recovery.dashboard') }}" 
                       class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        회복 대시보드
                    </a>
                @endauth
                
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
            
            <!-- 면책 고지 (하단) -->
            <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-gray-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-gray-700">
                        <p class="font-medium mb-1">면책 고지</p>
                        <p>본 결과는 웰니스 목적의 자가평가 정보이며, 질병의 진단·치료·예방을 위한 것이 아닙니다.</p>
                        <p class="mt-2">화면의 수치는 <strong>내부 컨디션 지표(H/레벨/Model Confidence)</strong>로, 개인 특성에 따라 달라질 수 있습니다.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js 라이브러리 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 게이지 데이터
            const gaugePercentage = {{ $gaugePercentage }};
            const segments = @json($segments);
            const currentSegmentIndex = {{ $currentSegmentIndex }};
            
            // 각 세그먼트별 데이터 준비 (6개 세그먼트만)
            const segmentData = [1, 1, 1, 1, 1, 1]; // 모두 동일한 크기
            const segmentColors = [];
            const segmentBorderColors = [];
            
            segments.forEach((segment, index) => {
                // 현재 값이 포함된 구간만 실제 색상, 나머지는 매우 옅은 색
                if (index === currentSegmentIndex) {
                    // 현재 구간만 진한 색상
                    segmentColors.push(segment.color);
                    segmentBorderColors.push(segment.color);
                } else {
                    // 나머지는 매우 옅은 색으로 (15% 불투명도)
                    segmentColors.push(segment.color + '26'); // 약 15% 불투명도 (hex)
                    segmentBorderColors.push(segment.color + '40'); // 약 25% 불투명도 (hex)
                }
            });
            
            // Chart.js 설정
            const ctx = document.getElementById('gaugeChart').getContext('2d');
            const gaugeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: segmentData,
                        backgroundColor: segmentColors,
                        borderColor: segmentBorderColors,
                        borderWidth: 1,
                        circumference: 180,
                        rotation: 270,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%', // 도넛 두께 조정
                    aspectRatio: 2,
                    layout: {
                        padding: {
                            bottom: 50 // 하단 여백 증가
                        }
                    },
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
    
    <script>
    // Add version stamp to the page
    document.addEventListener('DOMContentLoaded', function() {
        const versionStamp = document.createElement('div');
        versionStamp.className = 'mt-12 pt-6 border-t border-gray-200 text-center text-xs text-gray-500';
        versionStamp.innerHTML = `
            <div class="space-y-1">
                <div>Form ID: {{ $survey->id }} • Model Version: {{ config('app.version', '1.0.0') }} • Generated: {{ now()->format('Y-m-d H:i:s T') }}</div>
                <div>Response ID: {{ $response->id }} • Privacy: noindex, nofollow</div>
            </div>
        `;
        
        // Find the main container and append the stamp
        const mainContainer = document.querySelector('.max-w-4xl.mx-auto');
        if (mainContainer) {
            mainContainer.appendChild(versionStamp);
        }
    });
    </script>
</x-app-layout>
