<x-app-layout>
    {{-- 헤더 배너 섹션 --}}
    <div class="relative h-64 bg-gradient-to-r from-blue-900 to-blue-700 flex items-center justify-center overflow-hidden">
        {{-- 배경 패턴 --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
        </div>
        
        {{-- 중앙 컨텐츠 --}}
        <div class="relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">논문요약</h1>
            <div class="w-24 h-1 bg-white mx-auto"></div>
        </div>
    </div>

    {{-- 메인 컨텐츠 섹션 --}}
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                {{-- 왼쪽 필터 섹션 --}}
                <div class="lg:w-1/4">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('filters') }}</h3>
                        
                        {{-- 발행기관 필터 --}}
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <button type="button" 
                                    class="w-full flex items-center justify-between text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none py-2"
                                    onclick="toggleFilter('publisher')">
                                <span>{{ __('publisher') }}</span>
                                <svg class="w-4 h-4 transition-transform duration-200" id="publisher-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="publisher-filter" class="hidden mt-3 space-y-2 pl-2">
                                @foreach($publishers as $publisher)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="publisher[]" value="{{ $publisher }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-600">{{ $publisher }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- 연도 필터 --}}
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <button type="button" 
                                    class="w-full flex items-center justify-between text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none py-2"
                                    onclick="toggleFilter('year')">
                                <span>{{ __('year') }}</span>
                                <svg class="w-4 h-4 transition-transform duration-200" id="year-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="year-filter" class="hidden mt-3 space-y-2 pl-2">
                                @foreach($years as $year)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="year[]" value="{{ $year }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-600">{{ $year }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- 필터 초기화 버튼 --}}
                        <button type="button" class="w-full text-sm text-blue-600 hover:text-blue-700 font-medium">
                            {{ __('reset_filters') }}
                        </button>
                    </div>
                </div>

                {{-- 오른쪽 논문 리스트 --}}
                <div class="lg:w-3/4">
                    @if($papers->count() > 0)
                        <div class="bg-white">
                            @foreach($papers as $paper)
                                <div class="p-6 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                    {{-- 제목 --}}
                                    <h3 class="text-xl font-semibold text-gray-900 mb-3">
                                        <a href="{{ route('papers.show', $paper->slug) }}" class="hover:text-blue-600">
                                            {{ $paper->title }}
                                        </a>
                                    </h3>
                                    
                                    {{-- 메타 정보 (세로 배치) --}}
                                    <div class="space-y-2 text-gray-600 mb-4">
                                        {{-- 저자 --}}
                                        @if($paper->authors)
                                            <div class="text-base leading-relaxed">
                                                <span class="font-medium">{{ __('author') }}</span>
                                                <span class="mx-2 text-gray-400">|</span>
                                                @if(is_array($paper->authors))
                                                    {{ implode(', ', $paper->authors) }}
                                                @else
                                                    {{ $paper->authors }}
                                                @endif
                                            </div>
                                        @endif
                                        
                                        {{-- 출판사 --}}
                                        @if($paper->publisher)
                                            <div class="text-base leading-relaxed">
                                                <span class="font-medium">{{ __('publisher') }}</span>
                                                <span class="mx-2 text-gray-400">|</span>
                                                {{ $paper->publisher }}
                                            </div>
                                        @endif
                                        
                                        {{-- 날짜 --}}
                                        @if($paper->published_at)
                                            <div class="text-base leading-relaxed">
                                                <span class="font-medium">{{ __('published_date') }}</span>
                                                <span class="mx-2 text-gray-400">|</span>
                                                {{ $paper->published_at->format('Y.m.d') }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- 요약 --}}
                                    @if($paper->summary)
                                        <p class="text-gray-600 mb-4 line-clamp-2">
                                            {{ $paper->summary }}
                                        </p>
                                    @endif
                                    
                                    {{-- 더 보기 버튼 --}}
                                    <a href="{{ route('papers.show', $paper->slug) }}" 
                                       class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm">
                                        {{ __('read_more') }}
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        {{-- 페이지네이션 --}}
                        <div class="mt-8">
                            {{ $papers->links('pagination::tailwind') }}
                        </div>
                    @else
                        {{-- 논문이 없을 때 --}}
                        <div class="bg-white p-12 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">논문이 없습니다</h3>
                            <p class="text-gray-500">{{ __('no_papers_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFilter(type) {
            const filterDiv = document.getElementById(type + '-filter');
            const arrow = document.getElementById(type + '-arrow');
            
            if (filterDiv.classList.contains('hidden')) {
                filterDiv.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            } else {
                filterDiv.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</x-app-layout>