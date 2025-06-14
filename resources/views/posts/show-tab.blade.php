<x-app-layout>
    <div class="min-h-screen bg-white">
        <!-- Hero Section for Tab Posts -->
        <div class="relative bg-gradient-to-r from-blue-900 via-blue-800 to-blue-600 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <h1 class="text-5xl md:text-6xl font-bold text-white text-center mb-16">
                    @php
                        // 제목에서 이모티콘 제거 - 더 호환성 있는 방법
                        $cleanTitle = preg_replace('/[\x{1F300}-\x{1F6FF}\x{1F900}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $post->title);
                        $cleanTitle = trim($cleanTitle);
                    @endphp
                    {{ $cleanTitle }}
                </h1>
            </div>
            
            <!-- Tab Navigation -->
            <div class="bg-black bg-opacity-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="relative flex items-center">
                        <!-- Left Arrow -->
                        <button onclick="scrollTabs('left')" class="absolute left-0 z-10 p-2 text-white hover:text-gray-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Tabs Container -->
                        <div class="overflow-hidden mx-8">
                            <div id="tabsContainer" class="flex transition-transform duration-300 ease-in-out">
                                @php
                                    $allTabPosts = \App\Models\Post::where('type', 'tab')
                                        ->where('is_published', true)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                    $currentIndex = $allTabPosts->search(function ($item) use ($post) {
                                        return $item->id === $post->id;
                                    });
                                @endphp
                                
                                @foreach($allTabPosts as $index => $tabPost)
                                    @php
                                        $tabCleanTitle = preg_replace('/[\x{1F300}-\x{1F6FF}\x{1F900}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $tabPost->title);
                                        $tabCleanTitle = trim($tabCleanTitle);
                                    @endphp
                                    <a href="{{ route('posts.show', ['type' => 'tab', 'post' => $tabPost]) }}" 
                                       class="flex-shrink-0 px-6 py-4 text-center transition-all duration-300 {{ $tabPost->id === $post->id ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white' }}">
                                        <span class="whitespace-nowrap">{{ $tabCleanTitle }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Right Arrow -->
                        <button onclick="scrollTabs('right')" class="absolute right-0 z-10 p-2 text-white hover:text-gray-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <main>
            <article class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <!-- Summary -->
                @if($post->summary)
                    <div class="mb-8">
                        <p class="text-lg text-blue-600 italic leading-relaxed">
                            {{ $post->summary }}
                        </p>
                    </div>
                @endif

                <!-- Featured Image -->
                @if($post->image)
                    <div class="mb-8">
                        <img src="{{ Storage::url($post->image) }}" 
                             alt="{{ $post->title }}" 
                             class="w-full rounded-lg shadow-sm">
                    </div>
                @endif

                <!-- Tab Content Sections -->
                @if($post->content_sections)
                    <div class="mb-12 space-y-16">
                        {{-- Overview Section --}}
                        @if(isset($post->content_sections['overview']))
                            <div class="space-y-4">
                                <div class="w-32 h-0.5 bg-black"></div>
                                <div class="grid grid-cols-12 gap-12">
                                    <div class="col-span-12 md:col-span-3">
                                        <h3 class="text-xl font-semibold">Overview</h3>
                                    </div>
                                    <div class="col-span-12 md:col-span-9 prose prose-lg max-w-none">
                                        {!! $post->content_sections['overview'] !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Our Vision Section --}}
                        @if(isset($post->content_sections['our_vision']))
                            <div class="space-y-4">
                                <div class="w-32 h-0.5 bg-black"></div>
                                <div class="grid grid-cols-12 gap-12">
                                    <div class="col-span-12 md:col-span-3">
                                        <h3 class="text-xl font-semibold">Our Vision</h3>
                                    </div>
                                    <div class="col-span-12 md:col-span-9 prose prose-lg max-w-none">
                                        {!! $post->content_sections['our_vision'] !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Research Topics Section --}}
                        @if(isset($post->content_sections['research_topics']))
                            <div class="space-y-4">
                                <div class="w-32 h-0.5 bg-black"></div>
                                <div class="grid grid-cols-12 gap-12">
                                    <div class="col-span-12 md:col-span-3">
                                        <h3 class="text-xl font-semibold">Research Topics</h3>
                                    </div>
                                    <div class="col-span-12 md:col-span-9 prose prose-lg max-w-none">
                                        {!! $post->content_sections['research_topics'] !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Principles for AI Ethics Section --}}
                        @if(isset($post->content_sections['principles_for_ai_ethics']))
                            <div class="space-y-4">
                                <div class="w-32 h-0.5 bg-black"></div>
                                <div class="grid grid-cols-12 gap-12">
                                    <div class="col-span-12 md:col-span-3">
                                        <h3 class="text-xl font-semibold">Principles for AI Ethics</h3>
                                    </div>
                                    <div class="col-span-12 md:col-span-9 prose prose-lg max-w-none">
                                        {!! $post->content_sections['principles_for_ai_ethics'] !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Related Articles Section -->
                @if($post->related_articles && count($post->related_articles) > 0)
                    <div class="mt-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Related Articles</h2>
                        <div class="border-t-4 border-black mb-8"></div>
                        
                        <div class="space-y-6">
                            @foreach($post->related_articles as $articleId)
                                @php
                                    $relatedPost = \App\Models\Post::find($articleId);
                                @endphp
                                @if($relatedPost)
                                    <x-related-article-card :post="$relatedPost" />
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </article>

            <!-- Bottom CTA -->
            <div class="bg-gray-50 py-12 mt-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <a href="/" 
                       class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        More on Latest
                    </a>
                </div>
            </div>

            <!-- RSS Feed -->
            <div class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <a href="#" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3a1 1 0 000 2c5.523 0 10 4.477 10 10a1 1 0 102 0C17 8.373 11.627 3 5 3z"></path>
                            <path d="M4 9a1 1 0 011-1 7 7 0 017 7 1 1 0 11-2 0 5 5 0 00-5-5 1 1 0 01-1-1zM3 15a2 2 0 114 0 2 2 0 01-4 0z"></path>
                        </svg>
                        RSS FEED
                    </a>
                    <span class="ml-4 text-sm text-gray-500">Get latest updates from {{ config('app.name') }}</span>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        let currentTabIndex = {{ $currentIndex }};
        const tabsContainer = document.getElementById('tabsContainer');
        const tabs = tabsContainer.children;
        
        function scrollTabs(direction) {
            const containerWidth = tabsContainer.parentElement.offsetWidth;
            const scrollStep = 300; // 스크롤 단위
            
            let currentScroll = parseInt(tabsContainer.style.transform.replace('translateX(', '').replace('px)', '') || '0');
            
            if (direction === 'left') {
                currentScroll = Math.min(0, currentScroll + scrollStep);
            } else {
                currentScroll = currentScroll - scrollStep;
                // 스크롤 한계 검사
                const totalWidth = Array.from(tabs).reduce((sum, tab) => sum + tab.offsetWidth, 0);
                const maxScroll = -(totalWidth - containerWidth);
                currentScroll = Math.max(maxScroll, currentScroll);
            }
            
            tabsContainer.style.transform = `translateX(${currentScroll}px)`;
        }
        
        // 초기 위치 설정 - 현재 탭이 보이도록
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const containerWidth = tabsContainer.parentElement.offsetWidth;
                let currentTabOffset = 0;
                
                // 현재 탭까지의 오프셋 계산
                for (let i = 0; i < currentTabIndex; i++) {
                    currentTabOffset += tabs[i].offsetWidth;
                }
                
                // 현재 탭이 화면 중앙에 오도록 조정
                const currentTabWidth = tabs[currentTabIndex].offsetWidth;
                const targetScroll = -(currentTabOffset - (containerWidth / 2) + (currentTabWidth / 2));
                
                // 스크롤 범위 제한
                const totalWidth = Array.from(tabs).reduce((sum, tab) => sum + tab.offsetWidth, 0);
                const maxScroll = -(totalWidth - containerWidth);
                const finalScroll = Math.max(maxScroll, Math.min(0, targetScroll));
                
                tabsContainer.style.transform = `translateX(${finalScroll}px)`;
            }, 100); // DOM 렌더링 완료 후 실행
        });
    </script>
</x-app-layout>
