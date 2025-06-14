<article class="border border-gray-200 overflow-hidden bg-white hover:shadow-lg transition-shadow duration-300">
    <a href="{{ route('posts.show', ['type' => $post->type, 'post' => $post]) }}" class="block">
        @if($post->image)
            <div class="aspect-[16/9] overflow-hidden">
                <img src="{{ Storage::url($post->image) }}" 
                     alt="{{ $post->title }}" 
                     class="w-full h-full object-cover">
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
                {{ $post->read_more_text }}
            </span>
        </div>
    </a>
</article>
