{{-- Hero Slider Component --}}
@props([
    'heroes' => collect([])
])

<div class="relative h-[500px] bg-black overflow-hidden" x-data="heroSlider()">
    {{-- Slides --}}
    <div class="relative h-full">
        @foreach($heroes as $index => $hero)
            @php
                $settings = $hero->hero_settings ?? [];
                $contentAlignment = $settings['content_alignment'] ?? 'left';
                $overlaySettings = $settings['overlay'] ?? ['enabled' => true, 'color' => '#000000', 'opacity' => 60];
                
                // 텍스트 사이즈 매핑
                $titleSizeMap = [
                    'text-3xl' => 'text-3xl md:text-4xl',
                    'text-4xl' => 'text-4xl md:text-5xl',
                    'text-5xl' => 'text-4xl md:text-5xl lg:text-6xl',
                    'text-6xl' => 'text-5xl md:text-6xl lg:text-7xl'
                ];
                
                $subtitleSizeMap = [
                    'text-xs' => 'text-xs',
                    'text-sm' => 'text-sm',
                    'text-base' => 'text-base',
                    'text-lg' => 'text-lg'
                ];
                
                $descriptionSizeMap = [
                    'text-sm' => 'text-sm',
                    'text-base' => 'text-base',
                    'text-lg' => 'text-lg',
                    'text-xl' => 'text-xl'
                ];
                
                $titleSize = $titleSizeMap[$settings['title']['size'] ?? 'text-5xl'] ?? 'text-4xl md:text-5xl lg:text-6xl';
                $subtitleSize = $subtitleSizeMap[$settings['subtitle']['size'] ?? 'text-sm'] ?? 'text-sm';
                $descriptionSize = $descriptionSizeMap[$settings['description']['size'] ?? 'text-lg'] ?? 'text-lg';
                
                // 오버레이 계산
                $overlayOpacity = ($overlaySettings['opacity'] ?? 60) / 100;
                $overlayColor = $overlaySettings['color'] ?? '#000000';
            @endphp
            
            <div class="absolute inset-0 transition-opacity duration-1000"
                 :class="currentSlide === {{ $index }} ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                {{-- Background --}}
                <div class="absolute inset-0">
                    @if($hero->background_type === 'video' && $hero->background_video)
                        <video autoplay loop muted playsinline 
                               class="w-full h-full object-cover">
                            <source src="{{ Storage::url($hero->background_video) }}" type="video/mp4">
                        </video>
                    @elseif($hero->background_type === 'image' && $hero->background_image)
                        <img src="{{ Storage::url($hero->background_image) }}" 
                             alt="{{ $hero->title }}"
                             class="w-full h-full object-cover">
                    @endif
                    
                    {{-- 오버레이 --}}
                    @if($overlaySettings['enabled'] ?? true)
                        @php
                            $opacity1 = dechex(min(255, round($overlayOpacity * 255 * 0.8)));
                            $opacity2 = dechex(min(255, round($overlayOpacity * 255 * 0.5)));
                            $opacity1 = str_pad($opacity1, 2, '0', STR_PAD_LEFT);
                            $opacity2 = str_pad($opacity2, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <div class="absolute inset-0" 
                             style="background: linear-gradient(to right, 
                                    {{ $overlayColor }}{{ $opacity1 }}, 
                                    {{ $overlayColor }}{{ $opacity2 }}, 
                                    transparent);">
                        </div>
                    @endif
                </div>
                
                {{-- Content --}}
                <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center h-full {{ $contentAlignment === 'center' ? 'justify-center' : ($contentAlignment === 'right' ? 'justify-end' : 'justify-start') }}">
                        <div class="w-full md:w-2/3 lg:w-1/2 {{ $contentAlignment === 'center' ? 'text-center' : 'text-left' }}">
                            @if($hero->subtitle && ($settings['subtitle']['position'] ?? 'above') === 'above')
                                <p class="uppercase tracking-wider mb-2 {{ $subtitleSize }}"
                                   style="color: {{ $settings['subtitle']['color'] ?? '#E5E7EB' }}">
                                    {{ $hero->subtitle }}
                                </p>
                            @endif
                            
                            <h1 class="font-bold mb-4 {{ $titleSize }}"
                                style="color: {{ $settings['title']['color'] ?? '#FFFFFF' }}">
                                {{ $hero->title }}
                            </h1>
                            
                            @if($hero->subtitle && ($settings['subtitle']['position'] ?? 'above') === 'below')
                                <p class="uppercase tracking-wider mb-2 {{ $subtitleSize }}"
                                   style="color: {{ $settings['subtitle']['color'] ?? '#E5E7EB' }}">
                                    {{ $hero->subtitle }}
                                </p>
                            @endif
                            
                            @if($hero->description)
                                <p class="mb-8 leading-relaxed {{ $descriptionSize }}"
                                   style="color: {{ $settings['description']['color'] ?? '#D1D5DB' }}">
                                    {{ $hero->description }}
                                </p>
                            @endif
                            
                            @if($hero->button_text && $hero->button_url)
                                @php
                                    // 버튼 설정 가져오기 - 다양한 키 형식 지원
                                    $buttonSettings = $settings['button'] ?? [];
                                    
                                    // text_color 또는 textColor 키 확인
                                    $textColor = $buttonSettings['text_color'] ?? 
                                                $buttonSettings['textColor'] ?? 
                                                '#FFFFFF';
                                    
                                    // bg_color 또는 bgColor 키 확인                    
                                    $bgColor = $buttonSettings['bg_color'] ?? 
                                              $buttonSettings['bgColor'] ?? 
                                              '#3B82F6';
                                              
                                    $buttonStyle = $buttonSettings['style'] ?? 'filled';
                                    
                                    if ($buttonStyle === 'filled') {
                                        $buttonClasses = 'border-2';
                                        $buttonStyles = "color: {$textColor}; background-color: {$bgColor}; border-color: {$bgColor};";
                                    } else {
                                        $buttonClasses = 'border-2';
                                        $buttonStyles = "color: {$textColor}; background-color: transparent; border-color: {$textColor};";
                                    }
                                @endphp
                                <a href="{{ $hero->button_url }}" 
                                   class="inline-flex items-center px-8 py-3 rounded-full transition-all duration-300 hover:opacity-80 {{ $buttonClasses }}"
                                   style="{{ $buttonStyles }}">
                                    {{ $hero->button_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{-- Navigation Controls --}}
    @if($heroes->count() > 1)
        <div class="absolute inset-x-0 bottom-0 top-0 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pointer-events-none">
            {{-- Navigation Arrows --}}
            <button @click="previousSlide()"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white/60 hover:text-white z-20 pointer-events-auto">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <button @click="nextSlide()"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white/60 hover:text-white z-20 pointer-events-auto">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            
            {{-- Slide Indicators --}}
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-4 z-20 pointer-events-auto">
                @foreach($heroes as $index => $hero)
                    <button @click="goToSlide({{ $index }})"
                            class="relative h-1 transition-all duration-300"
                            :class="currentSlide === {{ $index }} ? 'w-12 bg-white' : 'w-8 bg-white/40'">
                    </button>
                @endforeach
            </div>
            
            {{-- Pause/Play Button --}}
            <button @click="toggleAutoPlay()"
                    class="absolute bottom-8 right-8 text-white/60 hover:text-white z-20 pointer-events-auto">
                <svg x-show="!isPaused" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg x-show="isPaused" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </button>
        </div>
    @endif
</div>

<script>
function heroSlider() {
    return {
        currentSlide: 0,
        isPaused: false,
        interval: null,
        totalSlides: {{ $heroes->count() }},
        
        init() {
            if (this.totalSlides > 1) {
                this.startAutoPlay();
            }
        },
        
        startAutoPlay() {
            this.interval = setInterval(() => {
                if (!this.isPaused) {
                    this.nextSlide();
                }
            }, 5000);
        },
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        },
        
        previousSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        },
        
        goToSlide(index) {
            this.currentSlide = index;
        },
        
        toggleAutoPlay() {
            this.isPaused = !this.isPaused;
        },
        
        destroy() {
            if (this.interval) {
                clearInterval(this.interval);
            }
        }
    }
}
</script>
