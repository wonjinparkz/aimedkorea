<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="lg:grid lg:grid-cols-4 lg:gap-8">
                {{-- 사이드바 --}}
                <div class="lg:col-span-1">
                    {{-- 검색 --}}
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('search') }}</h3>
                        <form method="GET" action="{{ route('qna.index') }}">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('search_questions') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @if(request()->has('featured'))
                                <input type="hidden" name="featured" value="{{ request('featured') }}">
                            @endif
                            @if(request()->has('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                        </form>
                    </div>

                    {{-- 필터 --}}
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('filters') }}</h3>
                        <div class="space-y-3">
                            <a href="{{ route('qna.index', array_merge(request()->except('featured'), [])) }}" 
                               class="block px-3 py-2 rounded-md {{ !request()->has('featured') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                {{ __('all_qna') }}
                            </a>
                            <a href="{{ route('qna.index', array_merge(request()->except('featured'), ['featured' => '1'])) }}" 
                               class="block px-3 py-2 rounded-md {{ request('featured') == '1' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                {{ __('frequently_asked_questions') }}
                            </a>
                        </div>
                    </div>

                    {{-- 정렬 --}}
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('sort') }}</h3>
                        <div class="space-y-3">
                            <a href="{{ route('qna.index', array_merge(request()->except('sort'), ['sort' => 'latest'])) }}" 
                               class="block px-3 py-2 rounded-md {{ request('sort', 'latest') == 'latest' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                {{ __('latest') }}
                            </a>
                            <a href="{{ route('qna.index', array_merge(request()->except('sort'), ['sort' => 'featured'])) }}" 
                               class="block px-3 py-2 rounded-md {{ request('sort') == 'featured' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                {{ __('faq_first') }}
                            </a>
                            <a href="{{ route('qna.index', array_merge(request()->except('sort'), ['sort' => 'oldest'])) }}" 
                               class="block px-3 py-2 rounded-md {{ request('sort') == 'oldest' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                {{ __('oldest') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 메인 콘텐츠 --}}
                <div class="mt-6 lg:mt-0 lg:col-span-3">
                    {{-- 자주 묻는 질문 섹션 (검색/필터가 없을 때만 표시) --}}
                    @if(!request()->hasAny(['search', 'featured', 'sort']) && $featuredQnas->count() > 0)
                    <div class="bg-amber-50 rounded-lg p-6 mb-8">
                        <h2 class="text-xl font-semibold text-amber-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            {{ __('frequently_asked_questions') }}
                        </h2>
                        <div class="space-y-3">
                            @foreach($featuredQnas as $faq)
                            <a href="{{ route('qna.show', $faq->id) }}" class="block p-3 bg-white rounded-md hover:shadow-md transition-shadow">
                                <h3 class="font-medium text-gray-900">Q. {{ $faq->title }}</h3>
                                <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                    A. {{ $faq->summary ?: strip_tags(Str::limit($faq->content, 100)) }}
                                </p>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Q&A 목록 --}}
                    <div class="space-y-4">
                        @forelse($qnas as $qna)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <a href="{{ route('qna.show', $qna->id) }}" class="block p-6">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">Q</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        @if($qna->is_featured)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 mb-2">
                                            {{ __('frequently_asked_questions') }}
                                        </span>
                                        @endif
                                        <h2 class="text-lg font-semibold text-gray-900 mb-2">{{ $qna->title }}</h2>
                                        <p class="text-gray-600 line-clamp-3">
                                            {{ $qna->summary ?: strip_tags(Str::limit($qna->content, 150)) }}
                                        </p>
                                        <div class="mt-3 flex items-center text-sm text-gray-500">
                                            <time datetime="{{ $qna->published_at->toIso8601String() }}">
                                                {{ $qna->published_at->format('Y년 m월 d일') }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Q&A가 없습니다</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->has('search'))
                                    검색 결과가 없습니다. 다른 키워드로 검색해보세요.
                                @else
                                    {{ __('no_qna_yet') }}
                                @endif
                            </p>
                        </div>
                        @endforelse
                    </div>

                    {{-- 페이지네이션 --}}
                    @if($qnas->hasPages())
                    <div class="mt-8">
                        {{ $qnas->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>