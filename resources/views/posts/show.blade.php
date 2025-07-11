<x-app-layout>
    <div class="min-h-screen bg-white">

        <!-- Main Content -->
        <main>
            <article class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
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

                <!-- Featured Image or Video -->
                @if($post->type === 'video')
                    <div class="mb-8 -mx-4 sm:mx-0">
                        @if($post->video_type === 'youtube' && $post->youtube_url)
                            @php
                                preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $post->youtube_url, $matches);
                                $videoId = $matches[1] ?? '';
                            @endphp
                            @if($videoId)
                                <div class="relative" style="padding-bottom: 56.25%; height: 0;">
                                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                            allowfullscreen
                                            class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg"
                                            style="height: 100%; width: 100%;"></iframe>
                                </div>
                            @endif
                        @elseif($post->video_type === 'upload' && $post->video_file)
                            <video controls 
                                   poster="{{ $post->video_thumbnail ? Storage::url($post->video_thumbnail) : '' }}"
                                   class="w-full rounded-lg shadow-lg">
                                <source src="{{ Storage::url($post->video_file) }}" type="video/mp4">
                                브라우저가 비디오 태그를 지원하지 않습니다.
                            </video>
                        @endif
                    </div>
                @elseif($post->image)
                    <div class="mb-8">
                        <img src="{{ Storage::url($post->image) }}" 
                             alt="{{ $post->title }}" 
                             class="w-full rounded-lg shadow-sm">
                    </div>
                @endif

                <!-- Content -->
                <div class="prose prose-lg max-w-none mb-12">
                    {!! $post->content !!}
                </div>

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
