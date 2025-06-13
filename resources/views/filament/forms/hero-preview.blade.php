<div x-data="heroPreview()" x-init="init()" style="position: relative;">
    <div style="position: relative; height: 300px; background-color: #000; overflow: hidden; border-radius: 8px;">
        <!-- 배경 그라데이션 -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to right, rgba(0,0,0,0.8), rgba(0,0,0,0.5), transparent);"></div>
        
        <!-- 콘텐츠 -->
        <div x-bind:style="getContainerStyle()" style="position: relative; height: 100%; padding: 32px;">
            <div style="max-width: 600px;">
                <!-- 부제목 -->
                <p x-show="subtitle" 
                   x-text="subtitle" 
                   x-bind:style="getSubtitleStyle()">
                </p>
                
                <!-- 제목 -->
                <h1 x-show="title" 
                    x-text="title" 
                    x-bind:style="getTitleStyle()">
                </h1>
                
                <!-- 설명 -->
                <p x-show="description" 
                   x-text="description" 
                   x-bind:style="getDescriptionStyle()">
                </p>
                
                <!-- 버튼 -->
                <button x-show="buttonText" 
                        x-text="buttonText"
                        x-bind:style="getButtonStyle()">
                </button>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 8px; padding: 8px; background-color: #f3f4f6; border-radius: 4px; font-size: 12px; color: #6b7280;">
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
                }
            }
        },
        
        getContainerStyle() {
            const alignments = {
                'left': 'display: flex; align-items: center; justify-content: flex-start; text-align: left;',
                'center': 'display: flex; align-items: center; justify-content: center; text-align: center;',
                'right': 'display: flex; align-items: center; justify-content: flex-end; text-align: right;'
            };
            return alignments[this.contentAlignment] || alignments['left'];
        },
        
        getTitleStyle() {
            const sizes = {
                'text-3xl': 'font-size: 1.875rem;',
                'text-4xl': 'font-size: 2.25rem;',
                'text-5xl': 'font-size: 3rem;',
                'text-6xl': 'font-size: 3.75rem;'
            };
            return `color: ${this.titleColor}; ${sizes[this.titleSize] || sizes['text-5xl']} font-weight: bold; margin-bottom: 16px; line-height: 1.2;`;
        },
        
        getSubtitleStyle() {
            const sizes = {
                'text-xs': 'font-size: 0.75rem;',
                'text-sm': 'font-size: 0.875rem;',
                'text-base': 'font-size: 1rem;',
                'text-lg': 'font-size: 1.125rem;'
            };
            return `color: ${this.subtitleColor}; ${sizes[this.subtitleSize] || sizes['text-sm']} text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;`;
        },
        
        getDescriptionStyle() {
            const sizes = {
                'text-sm': 'font-size: 0.875rem;',
                'text-base': 'font-size: 1rem;',
                'text-lg': 'font-size: 1.125rem;',
                'text-xl': 'font-size: 1.25rem;'
            };
            return `color: ${this.descriptionColor}; ${sizes[this.descriptionSize] || sizes['text-lg']} margin-bottom: 24px; line-height: 1.6;`;
        },
        
        getButtonStyle() {
            let baseStyle = 'display: inline-flex; align-items: center; padding: 12px 32px; font-size: 1rem; font-weight: 500; border-radius: 9999px; transition: all 0.3s; cursor: pointer;';
            
            if (this.buttonStyle === 'filled') {
                return `${baseStyle} color: ${this.buttonTextColor}; background-color: ${this.buttonBgColor}; border: 2px solid ${this.buttonBgColor};`;
            } else {
                return `${baseStyle} color: ${this.buttonTextColor}; background-color: transparent; border: 2px solid ${this.buttonTextColor};`;
            }
        }
    }
}
</script>
