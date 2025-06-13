<div x-data="heroPreview()" x-init="init()" class="relative">
    <div class="relative h-[300px] bg-black overflow-hidden rounded-lg">
        <!-- Background -->
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
        
        <!-- Content -->
        <div class="relative h-full p-8" :class="getAlignmentClasses()">
            <div class="max-w-xl">
                <!-- Subtitle -->
                <p x-show="subtitle" 
                   x-text="subtitle" 
                   :style="{ color: subtitleColor }"
                   class="text-xs uppercase tracking-wider mb-1">
                </p>
                
                <!-- Title -->
                <h1 x-show="title" 
                    x-text="title" 
                    :style="{ color: titleColor }"
                    class="text-2xl font-bold mb-2">
                </h1>
                
                <!-- Description -->
                <p x-show="description" 
                   x-text="description" 
                   :style="{ color: descriptionColor }"
                   class="text-sm mb-4 leading-relaxed">
                </p>
                
                <!-- Button -->
                <button x-show="buttonText" 
                        x-text="buttonText"
                        :style="{ 
                            color: buttonTextColor, 
                            borderColor: buttonBorderColor,
                            backgroundColor: buttonBgColor === 'transparent' ? 'transparent' : buttonBgColor
                        }"
                        class="inline-flex items-center px-4 py-2 border text-xs rounded-full transition-all duration-300"
                        @mouseenter="$el.style.backgroundColor = buttonHoverBgColor; $el.style.color = buttonHoverTextColor"
                        @mouseleave="$el.style.backgroundColor = buttonBgColor === 'transparent' ? 'transparent' : buttonBgColor; $el.style.color = buttonTextColor">
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function heroPreview() {
    return {
        title: '',
        subtitle: '',
        description: '',
        buttonText: '',
        titleColor: '#FFFFFF',
        subtitleColor: '#E5E7EB',
        descriptionColor: '#D1D5DB',
        buttonTextColor: '#FFFFFF',
        buttonBorderColor: '#FFFFFF',
        buttonBgColor: 'transparent',
        buttonHoverTextColor: '#000000',
        buttonHoverBgColor: '#FFFFFF',
        contentAlignment: 'center-left',
        
        init() {
            // 폼 필드 변경 감지
            this.$watch('$wire.data.title', value => this.title = value || '');
            this.$watch('$wire.data.subtitle', value => this.subtitle = value || '');
            this.$watch('$wire.data.description', value => this.description = value || '');
            this.$watch('$wire.data.button_text', value => this.buttonText = value || '');
            
            // 스타일 설정 감지
            this.$watch('$wire.data.hero_settings.title.color', value => this.titleColor = value || '#FFFFFF');
            this.$watch('$wire.data.hero_settings.subtitle.color', value => this.subtitleColor = value || '#E5E7EB');
            this.$watch('$wire.data.hero_settings.description.color', value => this.descriptionColor = value || '#D1D5DB');
            this.$watch('$wire.data.hero_settings.button.text_color', value => this.buttonTextColor = value || '#FFFFFF');
            this.$watch('$wire.data.hero_settings.button.border_color', value => this.buttonBorderColor = value || '#FFFFFF');
            this.$watch('$wire.data.hero_settings.button.bg_color', value => this.buttonBgColor = value || 'transparent');
            this.$watch('$wire.data.hero_settings.button.hover_text_color', value => this.buttonHoverTextColor = value || '#000000');
            this.$watch('$wire.data.hero_settings.button.hover_bg_color', value => this.buttonHoverBgColor = value || '#FFFFFF');
            this.$watch('$wire.data.hero_settings.content_alignment', value => this.contentAlignment = value || 'center-left');
            
            // 초기값 설정
            if (this.$wire.data) {
                this.title = this.$wire.data.title || '';
                this.subtitle = this.$wire.data.subtitle || '';
                this.description = this.$wire.data.description || '';
                this.buttonText = this.$wire.data.button_text || '';
                
                if (this.$wire.data.hero_settings) {
                    this.titleColor = this.$wire.data.hero_settings.title?.color || '#FFFFFF';
                    this.subtitleColor = this.$wire.data.hero_settings.subtitle?.color || '#E5E7EB';
                    this.descriptionColor = this.$wire.data.hero_settings.description?.color || '#D1D5DB';
                    this.buttonTextColor = this.$wire.data.hero_settings.button?.text_color || '#FFFFFF';
                    this.buttonBorderColor = this.$wire.data.hero_settings.button?.border_color || '#FFFFFF';
                    this.buttonBgColor = this.$wire.data.hero_settings.button?.bg_color || 'transparent';
                    this.buttonHoverTextColor = this.$wire.data.hero_settings.button?.hover_text_color || '#000000';
                    this.buttonHoverBgColor = this.$wire.data.hero_settings.button?.hover_bg_color || '#FFFFFF';
                    this.contentAlignment = this.$wire.data.hero_settings.content_alignment || 'center-left';
                }
            }
        },
        
        getAlignmentClasses() {
            const alignments = {
                'top-left': 'flex items-start justify-start',
                'center-left': 'flex items-center justify-start',
                'bottom-left': 'flex items-end justify-start',
                'top-center': 'flex items-start justify-center text-center',
                'center': 'flex items-center justify-center text-center',
                'bottom-center': 'flex items-end justify-center text-center',
                'top-right': 'flex items-start justify-end text-right',
                'center-right': 'flex items-center justify-end text-right',
                'bottom-right': 'flex items-end justify-end text-right',
            };
            return alignments[this.contentAlignment] || alignments['center-left'];
        }
    }
}
</script>
