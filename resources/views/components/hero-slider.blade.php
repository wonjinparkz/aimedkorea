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
                $contentAlignment = $settings['content_alignment'] ?? 'center-left';
                $alignmentClasses = match($contentAlignment) {
                    'top-left' => 'items-start justify-start',
                    'center-left' => 'items-center justify-start',
                    'bottom-left' => 'items-end justify-start',
                    'top-center' => 'items-start justify-center text-center',
                    'center' => 'items-center justify-center text-center',
                    'bottom-center' => 'items-end justify-center text-center',
                    'top-right' => 'items-start justify-end text-right',
                    'center-right' => 'items-center justify-end text-right',
                    'bottom-right' => 'items-end justify-end text-right',
                    default => 'items-center justify-start',
                };
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
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                </div>
                
                {{-- Content --}}
                <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex {{ $alignmentClasses }} h-full">
                        <div class="w-full md:w-2/3 lg:w-1/2">
                            @if($hero->subtitle)
                                <p class="text-sm uppercase tracking-wider mb-2"
                                   style="color: {{ $settings['subtitle']['color'] ?? '#E5E7EB' }}">
                                    {{ $hero->subtitle }}
                                </p>
                            @endif
                            
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4"
                                style="color: {{ $settings['title']['color'] ?? '#FFFFFF' }}">
                                {{ $hero->title }}
                            </h1>
                            
                            @if($hero->description)
                                <p class="text-lg mb-8 leading-relaxed"
                                   style="color: {{ $settings['description']['color'] ?? '#D1D5DB' }}">
                                    {{ $hero->description }}
                                </p>
                            @endif
                            
                            @if($hero->button_text && $hero->button_url)
                                @php
                                    $buttonSettings = $settings['button'] ?? [];
                                    $textColor = $buttonSettings['text_color'] ?? '#FFFFFF';
                                    $borderColor = $buttonSettings['border_color'] ?? '#FFFFFF';
                                    $bgColor = $buttonSettings['bg_color'] ?? 'transparent';
                                    $hoverTextColor = $buttonSettings['hover_text_color'] ?? '#000000';
                                    $hoverBgColor = $buttonSettings['hover_bg_color'] ?? '#FFFFFF';
                                @endphp
                                <a href="{{ $hero->button_url }}" 
                                   class="inline-flex items-center px-8 py-3 border rounded-full transition-all duration-300"
                                   style="color: {{ $textColor }}; border-color: {{ $borderColor }}; background-color: {{ $bgColor === 'transparent' ? 'transparent' : $bgColor }};"
                                   onmouseover="this.style.backgroundColor='{{ $hoverBgColor }}'; this.style.color='{{ $hoverTextColor }}';"
                                   onmouseout="this.style.backgroundColor='{{ $bgColor === 'transparent' ? 'transparent' : $bgColor }}'; this.style.color='{{ $textColor }}';">
                                    {{ $hero->button_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{-- Slide Indicators --}}
    @if($heroes->count() > 1)
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-4 z-20">
            @foreach($heroes as $index => $hero)
                <button @click="goToSlide({{ $index }})"
                        class="relative h-1 transition-all duration-300"
                        :class="currentSlide === {{ $index }} ? 'w-12 bg-white' : 'w-8 bg-white/40'">
                </button>
            @endforeach
        </div>
        
        {{-- Navigation Arrows --}}
        <button @click="previousSlide()"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white/60 hover:text-white z-20">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <button @click="nextSlide()"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white/60 hover:text-white z-20">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
        
        {{-- Pause/Play Button --}}
        <button @click="toggleAutoPlay()"
                class="absolute bottom-8 right-8 text-white/60 hover:text-white z-20">
            <svg x-show="!isPaused" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <svg x-show="isPaused" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </button>
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
