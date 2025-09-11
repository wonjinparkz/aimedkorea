{{-- Horizontal Post Card Component --}}
@props([
    'post' => null
])

<div class="bg-white overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-2">
        {{-- 좌측 이미지 섹션 --}}
        <div class="flex items-center justify-center p-8 lg:p-12">
            @if($post->image)
                <x-optimized-image 
                    :src="Storage::url($post->image)" 
                    :alt="$post->title" 
                    class="w-full h-full max-w-md object-cover"
                    :width="600"
                    :height="400"
                    :lazy="true" />
            @else
                <div class="w-full max-w-md aspect-video bg-gray-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif
        </div>
        
        {{-- 우측 텍스트 섹션 - 테두리 적용 --}}
        <div class="flex items-stretch border border-gray-200">
            <div class="flex flex-col justify-center px-6 lg:px-8 w-full">
                {{-- 제목 --}}
                <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4 break-words">
                    {{ $post->title }}
                </h3>
                
                {{-- 요약 - 길어도 잘리지 않도록 설정 --}}
                <p class="text-gray-600 text-base lg:text-lg mb-6 leading-relaxed break-words">
                    {{ $post->summary ?? Str::limit(strip_tags($post->content), 200) }}
                </p>
                
                {{-- 더보기 링크 --}}
                <a href="{{ route('posts.show', ['type' => $post->type, 'post' => $post]) }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-lg">
                    {{ __('read_more') }}
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
