{{-- Tailwind CSS CDN --}}
<script src="https://cdn.tailwindcss.com"></script>

<div x-data="heroPreview()" x-init="init()">
    <div class="relative h-[300px] bg-black overflow-hidden rounded-lg">
        {{-- 배경 오버레이 --}}
        <div x-show="overlayEnabled" 
             class="absolute inset-0 z-10"
             :style="`background: linear-gradient(to right, ${overlayColor}${Math.round(overlayOpacity * 2.55).toString(16).padStart(2, '0')}, ${overlayColor}${Math.round(overlayOpacity * 1.275).toString(16).padStart(2, '0')}, transparent);`">
        </div>
        
        {{-- 콘텐츠 --}}
        <div class="relative h-full z-20" :class="getContainerClasses()">
            <div class="h-full max-w-7xl mx-auto px-8">
                <div class="flex h-full" :class="getAlignmentClasses()">
                    <div class="max-w-xl">
                        {{-- 부제목 --}}
                        <p x-show="subtitle" 
                           x-text="subtitle" 
                           :style="{ color: subtitleColor }"
                           :class="subtitleSize + ' uppercase tracking-wider mb-2'">
                        </p>
                        
                        {{-- 제목 --}}
                        <h1 x-show="title" 
                            x-text="title" 
                            :style="{ color: titleColor }"
                            :class="titleSize + ' font-bold mb-4'">
                        </h1>
                        
                        {{-- 설명 --}}
                        <p x-show="description" 
                           x-text="description" 
                           :style="{ color: descriptionColor }"
                           :class="descriptionSize + ' mb-6 leading-relaxed'">
                        </p>
                        
                        {{-- 버튼 --}}
                        <button x-show="buttonText" 
                                x-text="buttonText"
                                :style="getButtonStyle()"
                                class="inline-flex items-center px-8 py-3 rounded-full transition-all duration-300">
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-2 p-2 bg-gray-100 rounded text-xs text-gray-600">
        💡 팁: 각 섹션의 설정을 변경하면 위 미리보기에 실시간으로 반영됩니다
    </div>
</div>

<script>
function heroPreview() {
    return {
        // 텍스트 내용
        title: '',
        subtitle: '',
        description: '',
        buttonText: '',
        
        // 스타일 설정
        titleColor: '#FFFFFF',
        titleSize: 'text-5xl',
        subtitleColor: '#E5E7EB',
        subtitleSize: 'text-sm',
        descriptionColor: '#D1D5DB',
        descriptionSize: 'text-lg',
        buttonTextColor: '#FFFFFF',
        buttonBgColor: '#3B82F6',
        buttonStyle: 'filled',
        contentAlignment: 'left',
        
        // 오버레이 설정
        overlayEnabled: true,
        overlayColor: '#000000',
        overlayOpacity: 60,
        
        init() {
            // 폼 필드 변경 감지
            this.$watch('$wire.data.title', value => this.title = value || '');
            this.$watch('$wire.data.subtitle', value => this.subtitle = value || '');
            this.$watch('$wire.data.description', value => this.description = value || '');
            this.$watch('$wire.data.button_text', value => this.buttonText = value || '');
            
            // 스타일 설정 감지
            if (this.$wire.data.hero_settings) {
                this.$watch('$wire.data.hero_settings.title.color', value => this.titleColor = value || '#FFFFFF');
                this.$watch('$wire.data.hero_settings.title.size', value => this.titleSize = value || 'text-5xl');
                this.$watch('$wire.data.hero_settings.subtitle.color', value => this.subtitleColor = value || '#E5E7EB');
                this.$watch('$wire.data.hero_settings.subtitle.size', value => this.subtitleSize = value || 'text-sm');
                this.$watch('$wire.data.hero_settings.description.color', value => this.descriptionColor = value || '#D1D5DB');
                this.$watch('$wire.data.hero_settings.description.size', value => this.descriptionSize = value || 'text-lg');
                this.$watch('$wire.data.hero_settings.button.text_color', value => this.buttonTextColor = value || '#FFFFFF');
                this.$watch('$wire.data.hero_settings.button.bg_color', value => this.buttonBgColor = value || '#3B82F6');
                this.$watch('$wire.data.hero_settings.button.style', value => this.buttonStyle = value || 'filled');
                this.$watch('$wire.data.hero_settings.content_alignment', value => this.contentAlignment = value || 'left');
                
                // 오버레이 설정 감지
                this.$watch('$wire.data.hero_settings.overlay.enabled', value => this.overlayEnabled = value !== false);
                this.$watch('$wire.data.hero_settings.overlay.color', value => this.overlayColor = value || '#000000');
                this.$watch('$wire.data.hero_settings.overlay.opacity', value => this.overlayOpacity = value || 60);
            }
            
            // 초기값 설정
            this.loadInitialValues();
        },
        
        loadInitialValues() {
            if (this.$wire.data) {
                this.title = this.$wire.data.title || '';
                this.subtitle = this.$wire.data.subtitle || '';
                this.description = this.$wire.data.description || '';
                this.buttonText = this.$wire.data.button_text || '';
                
                if (this.$wire.data.hero_settings) {
                    const settings = this.$wire.data.hero_settings;
                    this.titleColor = settings.title?.color || '#FFFFFF';
                    this.titleSize = settings.title?.size || 'text-5xl';
                    this.subtitleColor = settings.subtitle?.color || '#E5E7EB';
                    this.subtitleSize = settings.subtitle?.size || 'text-sm';
                    this.descriptionColor = settings.description?.color || '#D1D5DB';
                    this.descriptionSize = settings.description?.size || 'text-lg';
                    this.buttonTextColor = settings.button?.text_color || '#FFFFFF';
                    this.buttonBgColor = settings.button?.bg_color || '#3B82F6';
                    this.buttonStyle = settings.button?.style || 'filled';
                    this.contentAlignment = settings.content_alignment || 'left';
                    
                    // 오버레이 설정
                    this.overlayEnabled = settings.overlay?.enabled !== false;
                    this.overlayColor = settings.overlay?.color || '#000000';
                    this.overlayOpacity = settings.overlay?.opacity || 60;
                }
            }
        },
        
        getContainerClasses() {
            if (this.contentAlignment === 'right') {
                return 'text-left'; // 오른쪽 위치, 왼쪽 정렬
            }
            return this.contentAlignment === 'center' ? 'text-center' : 'text-left';
        },
        
        getAlignmentClasses() {
            const alignments = {
                'left': 'items-center justify-start',
                'center': 'items-center justify-center',
                'right': 'items-center justify-end'
            };
            return alignments[this.contentAlignment] || 'items-center justify-start';
        },
        
        getButtonStyle() {
            if (this.buttonStyle === 'filled') {
                return `color: ${this.buttonTextColor}; background-color: ${this.buttonBgColor}; border: 2px solid ${this.buttonBgColor};`;
            } else {
                return `color: ${this.buttonTextColor}; background-color: transparent; border: 2px solid ${this.buttonTextColor};`;
            }
        }
    }
}
</script>
