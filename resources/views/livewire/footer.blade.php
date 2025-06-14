<footer class="bg-gray-50 text-gray-900">
    <!-- Main Footer Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Left Column - Company Info -->
            <div>
                <h3 class="text-xl font-bold mb-4">{{ $siteTitle }}</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $siteTagline }}
                </p>
                <!-- Placeholder Image -->
                <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center border border-gray-300">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- Right Column - Service Cards -->
            <div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Card 1 -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">의료 AI 진단</h4>
                            <p class="text-sm text-gray-600">정확한 진단 지원</p>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">헬스케어 플랫폼</h4>
                            <p class="text-sm text-gray-600">통합 건강 관리</p>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">연구 개발</h4>
                            <p class="text-sm text-gray-600">혁신적인 솔루션</p>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">원격 진료</h4>
                            <p class="text-sm text-gray-600">언제 어디서나</p>
                        </div>
                    </div>

                    <!-- Card 5 -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-yellow-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">데이터 분석</h4>
                            <p class="text-sm text-gray-600">빅데이터 인사이트</p>
                        </div>
                    </div>

                    <!-- Card 6 -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-1">기술 지원</h4>
                            <p class="text-sm text-gray-600">24/7 전문 서비스</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="bg-gray-100 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex flex-wrap justify-center md:justify-start space-x-6 text-sm mb-4 md:mb-0">
                    @foreach($footerLinks as $link)
                        <a href="{{ $link['url'] }}" class="text-gray-600 hover:text-gray-900 transition-colors">{{ $link['title'] }}</a>
                    @endforeach
                    <div class="relative group">
                        <button class="text-gray-600 hover:text-gray-900 transition-colors flex items-center">
                            패밀리사이트
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <!-- 패밀리사이트 드롭다운 (추후 구현 가능) -->
                    </div>
                </div>
                <div class="text-gray-600 text-sm">
                    &copy; {{ date('Y') }} {{ $siteTitle }}. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</footer>
