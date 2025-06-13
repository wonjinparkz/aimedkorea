<div class="shadow-sm overflow-hidden hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 h-full"
     style="background-color: {{ $backgroundColor }}">
    <div class="h-full flex flex-col items-center justify-center p-4 text-white relative">
        {{-- 아이콘 추출 및 표시 --}}
        @php
            $iconPattern = '/[\p{Emoji_Presentation}\p{Emoji}\x{1F300}-\x{1F9FF}]/u';
            preg_match($iconPattern, $post->title, $matches);
            $icon = $matches[0] ?? '';
            $titleWithoutIcon = preg_replace($iconPattern, '', $post->title);
            $titleWithoutIcon = trim($titleWithoutIcon);
            
            // 제목에서 영어와 한글 분리
            $englishTitle = '';
            $koreanTitle = '';
            
            // Resilience 또는 Recovery가 포함된 패턴 매칭
            if (preg_match('/^(.+?(?:Resilience|Recovery|Routine)[™]?)\s*(.*)$/u', $titleWithoutIcon, $titleMatches)) {
                $englishTitle = trim($titleMatches[1]);
                $remaining = trim($titleMatches[2]);
                // 괄호 안의 한글 또는 괄호 없는 한글 추출
                if (preg_match('/[\(（]?([가-힣\s]+)[\)）]?/', $remaining, $koreanMatches)) {
                    $koreanTitle = trim($koreanMatches[1]);
                }
            } else {
                $englishTitle = $titleWithoutIcon;
            }
        @endphp
        
        @if($icon)
            <div class="text-5xl mb-3">{{ $icon }}</div>
        @endif
        
        <div class="text-center mb-4">
            @if($englishTitle)
                <h3 class="text-xl font-bold leading-tight">{{ $englishTitle }}</h3>
            @endif
            @if($koreanTitle)
                <p class="text-base font-semibold mt-1 opacity-90">{{ $koreanTitle }}</p>
            @endif
        </div>
        
        @if($post->summary)
            <p class="text-white text-lg leading-relaxed mb-3 text-center px-2">
                {{ Str::limit($post->summary, 100) }}
            </p>
        @endif
        
        @if($post->read_more_text)
            <a href="{{ route('posts.show', $post) }}" class="inline-flex text-center items-center text-white hover:opacity-80 text-sm font-medium">
                <span>{{ $post->read_more_text }}</span>
            </a>
        @endif
    </div>
</div>
