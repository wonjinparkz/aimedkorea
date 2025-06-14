{{-- Related Article Card Component --}}
@props([
    'post' => null
])

<article class="flex bg-white overflow-hidden hover:shadow-lg transition-shadow duration-300">
    {{-- 썸네일 영역 --}}
    <div class="w-48 h-36 flex-shrink-0">
        @if($post->image)
            <img src="{{ Storage::url($post->image) }}" 
                 alt="{{ $post->title }}" 
                 class="w-full h-full object-cover">
        @else
            @php
                // 제목에서 처음 몇 글자 추출
                $titlePreview = Str::limit($post->title, 50);
                // 제목에서 [논문] 같은 접두사 제거
                $cleanTitle = preg_replace('/^\[.*?\]\s*/', '', $titlePreview);
            @endphp
            <div class="w-full h-full bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center p-4">
                <p class="text-white text-sm font-medium text-center leading-tight">
                    {{ $cleanTitle }}
                </p>
            </div>
        @endif
    </div>
    
    {{-- 콘텐츠 영역 --}}
    <div class="flex-1 p-6 flex flex-col justify-center">
        <h3 class="text-xl font-semibold text-gray-900 mb-2 line-clamp-2">
            <a href="{{ route('posts.show', ['type' => $post->type, 'post' => $post]) }}" 
               class="hover:text-blue-600 transition-colors">
                {{ $post->title }}
            </a>
        </h3>
        <time class="text-sm text-gray-500">
            On {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
        </time>
    </div>
</article>
