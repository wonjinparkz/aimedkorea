<x-app-layout>
    <div class="min-h-screen bg-white">

        <!-- Main Content -->
        <main>
            <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <!-- Title Section -->
                <div class="mb-8">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        {{ $post->title }}
                    </h1>
                    
                    <div class="flex items-center justify-between">
                        <time class="text-sm text-gray-500">
                            {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                        </time>
                        
                    </div>
                </div>

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

                <!-- Content for tab type posts -->
                @if($post->type === 'tab' && $post->content_sections)
                    <div class="mb-12">
                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button 
                                    onclick="showTab('overview')" 
                                    id="overview-tab" 
                                    class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-blue-500 text-blue-600">
                                    Overview
                                </button>
                                <button 
                                    onclick="showTab('our_vision')" 
                                    id="our_vision-tab" 
                                    class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    Our Vision
                                </button>
                                <button 
                                    onclick="showTab('research_topics')" 
                                    id="research_topics-tab" 
                                    class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    Research Topics
                                </button>
                                <button 
                                    onclick="showTab('principles_for_ai_ethics')" 
                                    id="principles_for_ai_ethics-tab" 
                                    class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                    Principles for AI Ethics
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="mt-8">
                            <div id="overview-content" class="tab-content prose prose-lg max-w-none">
                                {!! $post->content_sections['overview'] ?? '' !!}
                            </div>
                            <div id="our_vision-content" class="tab-content prose prose-lg max-w-none hidden">
                                {!! $post->content_sections['our_vision'] ?? '' !!}
                            </div>
                            <div id="research_topics-content" class="tab-content prose prose-lg max-w-none hidden">
                                {!! $post->content_sections['research_topics'] ?? '' !!}
                            </div>
                            <div id="principles_for_ai_ethics-content" class="tab-content prose prose-lg max-w-none hidden">
                                {!! $post->content_sections['principles_for_ai_ethics'] ?? '' !!}
                            </div>
                        </div>
                    </div>

                    <script>
                        function showTab(tabName) {
                            // Hide all tab contents
                            document.querySelectorAll('.tab-content').forEach(content => {
                                content.classList.add('hidden');
                            });
                            
                            // Remove active state from all tabs
                            document.querySelectorAll('.tab-button').forEach(button => {
                                button.classList.remove('border-blue-500', 'text-blue-600');
                                button.classList.add('border-transparent', 'text-gray-500');
                            });
                            
                            // Show selected content
                            document.getElementById(tabName + '-content').classList.remove('hidden');
                            
                            // Add active state to selected tab
                            const activeTab = document.getElementById(tabName + '-tab');
                            activeTab.classList.remove('border-transparent', 'text-gray-500');
                            activeTab.classList.add('border-blue-500', 'text-blue-600');
                        }
                    </script>
                @else
                    <!-- Regular Content -->
                    <div class="prose prose-lg max-w-none mb-12">
                        {!! $post->content !!}
                    </div>
                @endif

                <!-- Related Articles Section -->
                @if($post->type === 'tab' && $post->related_articles && count($post->related_articles) > 0)
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
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <a href="/" 
                       class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        More on Latest
                    </a>
                </div>
            </div>

            <!-- RSS Feed -->
            <div class="py-8">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
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
</x-app-layout>
