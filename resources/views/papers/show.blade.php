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

    {{-- 메인 컨텐츠 --}}
    <div class="min-h-screen bg-white">
        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            {{-- 제목 --}}
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8">
                {{ $paper->title }}
            </h1>

            <div class="flex flex-col lg:flex-row gap-8">
                {{-- 왼쪽: 출판사 및 날짜 정보 --}}
                <div class="lg:w-1/3">
                    <div class="p-6">
                        {{-- 출판사 --}}
                        @if($paper->publisher)
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">출판사</h3>
                                <p class="text-sm text-gray-600 font-light">{{ $paper->publisher }}</p>
                            </div>
                        @endif

                        {{-- 저자 --}}
                        @if($paper->authors)
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">저자</h3>
                                <p class="text-sm text-gray-600 font-light">
                                    @if(is_array($paper->authors))
                                        {{ implode(', ', $paper->authors) }}
                                    @else
                                        {{ $paper->authors }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        {{-- 게시일 --}}
                        @if($paper->published_at)
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">게시일</h3>
                                <p class="text-sm text-gray-600 font-light">{{ $paper->published_at->format('Y년 m월 d일') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 오른쪽: 컨텐츠 및 링크 --}}
                <div class="lg:w-2/3">
                    {{-- 회색 박스 안의 컨텐츠 --}}
                    <div class="bg-gray-100 rounded-lg p-8">
                        {{-- 요약 --}}
                        @if($paper->summary)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">요약</h3>
                                <p class="text-gray-700 leading-relaxed">{{ $paper->summary }}</p>
                            </div>
                        @endif

                        {{-- 내용 --}}
                        @if($paper->content)
                            <div class="prose prose-gray max-w-none mb-6">
                                {!! $paper->content !!}
                            </div>
                        @endif

                        {{-- 원문 링크 --}}
                        @if($paper->link)
                            <div class="pt-6 border-t border-gray-300">
                                <a href="{{ $paper->link }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                                    원문 보기
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- List 버튼 --}}
                    <div class="mt-8 text-center">
                        <a href="{{ route('papers.index') }}" 
                           class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-full shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            List
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>