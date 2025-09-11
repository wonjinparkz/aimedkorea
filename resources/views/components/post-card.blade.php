<article class="border border-gray-200 overflow-hidden bg-white hover:shadow-lg transition-shadow duration-300">
    <a href="{{ route('posts.show', ['type' => $post->type, 'post' => $post]) }}" class="block">
        {{-- 비디오 타입 처리 --}}
        @if($post->type === 'video')
            <div class="aspect-[16/9] overflow-hidden relative">
                @if($post->video_thumbnail && !str_contains($post->video_thumbnail, 'youtube.com'))
                    {{-- 업로드된 썸네일 --}}
                    <x-optimized-image 
                        :src="Storage::url($post->video_thumbnail)" 
                        :alt="$post->title" 
                        class="w-full h-full object-cover"
                        :width="800"
                        :height="450"
                        :lazy="true" />
                @elseif($post->video_thumbnail && str_contains($post->video_thumbnail, 'youtube.com'))
                    {{-- 유튜브 썸네일 URL --}}
                    <img src="{{ $post->video_thumbnail }}" 
                         alt="{{ $post->title }}" 
                         class="w-full h-full object-cover"
                         onerror="this.src='https://img.youtube.com/vi/{{ preg_match('/\/vi\/([^\/]+)\//', $post->video_thumbnail, $m) ? $m[1] : '' }}/hqdefault.jpg'">
                @elseif($post->youtube_url && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $post->youtube_url, $matches))
                    {{-- 유튜브 URL에서 썸네일 추출 --}}
                    <img src="https://img.youtube.com/vi/{{ $matches[1] }}/maxresdefault.jpg" 
                         alt="{{ $post->title }}" 
                         class="w-full h-full object-cover"
                         onerror="this.src='https://img.youtube.com/vi/{{ $matches[1] }}/hqdefault.jpg'">
                @else
                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                {{-- 재생 버튼 오버레이 --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="bg-black bg-opacity-50 rounded-full p-4 transition-all hover:bg-opacity-70">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
                {{-- 비디오 길이 표시 --}}
                @if($post->video_duration)
                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                        {{ sprintf('%02d:%02d', floor($post->video_duration / 60), $post->video_duration % 60) }}
                    </div>
                @endif
            </div>
        {{-- 일반 이미지 처리 --}}
        @elseif($post->image)
            <div class="aspect-[16/9] overflow-hidden">
                <x-optimized-image 
                    :src="Storage::url($post->image)" 
                    :alt="$post->title" 
                    class="w-full h-full object-cover"
                    :width="800"
                    :height="450"
                    :lazy="true" />
            </div>
        @else
            <div class="aspect-[16/9] bg-gray-100 flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
        
        <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-3">
                {{ $post->title }}
            </h3>
            
            @if($post->summary)
                <p class="text-gray-600 mb-4 leading-relaxed">
                    {{ $post->summary }}
                </p>
            @endif
            
            <span class="text-blue-600 hover:text-blue-800 font-medium inline-flex items-center">
                {{ __('read_more') }}
            </span>
        </div>
    </a>
</article>
