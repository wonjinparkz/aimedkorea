<div x-data="heroPreview()" x-init="init()">
    <div id="hero-preview-container" class="hero-preview-scope" style="width: 100%; height: 320px; border-radius: 8px; overflow: hidden; position: relative; background: #000;">
        <!-- 프리뷰 내용이 여기에 렌더링됩니다 -->
    </div>
    
    <div style="margin-top: 8px; padding: 8px; background-color: #f3f4f6; border-radius: 4px; font-size: 12px; color: #6b7280;">
        💡 팁: 각 섹션의 설정을 변경하면 위 미리보기에 실시간으로 반영됩니다
    </div>
</div>

<style>
/* 프리뷰 영역에만 Tailwind 리셋 적용 */
.hero-preview-scope * {
    all: revert;
    box-sizing: border-box;
}

/* Tailwind 유틸리티 클래스를 수동으로 정의 */
.hero-preview-scope .relative { position: relative; }
.hero-preview-scope .absolute { position: absolute; }
.hero-preview-scope .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
.hero-preview-scope .z-10 { z-index: 10; }
.hero-preview-scope .z-20 { z-index: 20; }
.hero-preview-scope .h-full { height: 100%; }
.hero-preview-scope .max-w-7xl { max-width: 80rem; }
.hero-preview-scope .max-w-xl { max-width: 36rem; }
.hero-preview-scope .mx-auto { margin-left: auto; margin-right: auto; }
.hero-preview-scope .px-8 { padding-left: 2rem; padding-right: 2rem; }
.hero-preview-scope .flex { display: flex; }
.hero-preview-scope .items-center { align-items: center; }
.hero-preview-scope .justify-start { justify-content: flex-start; }
.hero-preview-scope .justify-center { justify-content: center; }
.hero-preview-scope .justify-end { justify-content: flex-end; }
.hero-preview-scope .text-left { text-align: left; }
.hero-preview-scope .text-center { text-align: center; }
.hero-preview-scope .uppercase { text-transform: uppercase; }
.hero-preview-scope .tracking-wider { letter-spacing: 0.05em; }
.hero-preview-scope .mb-2 { margin-bottom: 0.5rem; }
.hero-preview-scope .mb-4 { margin-bottom: 1rem; }
.hero-preview-scope .mb-6 { margin-bottom: 1.5rem; }
.hero-preview-scope .font-bold { font-weight: 700; }
.hero-preview-scope .leading-relaxed { line-height: 1.625; }
.hero-preview-scope .inline-flex { display: inline-flex; }
.hero-preview-scope .items-center { align-items: center; }
.hero-preview-scope .px-8 { padding-left: 2rem; padding-right: 2rem; }
.hero-preview-scope .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
.hero-preview-scope .rounded-full { border-radius: 9999px; }
.hero-preview-scope .border-2 { border-width: 2px; }
.hero-preview-scope .transition-all { transition-property: all; }
.hero-preview-scope .duration-300 { transition-duration: 300ms; }

/* 텍스트 크기 */
.hero-preview-scope .text-xs { font-size: 0.75rem; line-height: 1rem; }
.hero-preview-scope .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.hero-preview-scope .text-base { font-size: 1rem; line-height: 1.5rem; }
.hero-preview-scope .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
.hero-preview-scope .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
.hero-preview-scope .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.hero-preview-scope .text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
.hero-preview-scope .text-5xl { font-size: 3rem; line-height: 1; }
.hero-preview-scope .text-6xl { font-size: 3.75rem; line-height: 1; }
</style>

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
            this.$watch('$wire.data.title', value => { this.title = value || ''; this.updatePreview(); });
            this.$watch('$wire.data.subtitle', value => { this.subtitle = value || ''; this.updatePreview(); });
            this.$watch('$wire.data.description', value => { this.description = value || ''; this.updatePreview(); });
            this.$watch('$wire.data.button_text', value => { this.buttonText = value || ''; this.updatePreview(); });
            
            // 스타일 설정 감지
            if (window.Livewire) {
                this.$watch('$wire.data.hero_settings', value => {
                    if (value) {
                        this.titleColor = value.title?.color || '#FFFFFF';
                        this.titleSize = value.title?.size || 'text-5xl';
                        this.subtitleColor = value.subtitle?.color || '#E5E7EB';
                        this.subtitleSize = value.subtitle?.size || 'text-sm';
                        this.descriptionColor = value.description?.color || '#D1D5DB';
                        this.descriptionSize = value.description?.size || 'text-lg';
                        this.buttonTextColor = value.button?.text_color || '#FFFFFF';
                        this.buttonBgColor = value.button?.bg_color || '#3B82F6';
                        this.buttonStyle = value.button?.style || 'filled';
                        this.contentAlignment = value.content_alignment || 'left';
                        this.overlayEnabled = value.overlay?.enabled !== false;
                        this.overlayColor = value.overlay?.color || '#000000';
                        this.overlayOpacity = value.overlay?.opacity || 60;
                        this.updatePreview();
                    }
                }, { deep: true });
            }
            
            // 초기값 설정
            this.loadInitialValues();
            this.updatePreview();
        },
        
        loadInitialValues() {
            if (this.$wire && this.$wire.data) {
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
        
        updatePreview() {
            const container = document.getElementById('hero-preview-container');
            if (!container) return;
            
            const alignmentClasses = {
                'left': 'items-center justify-start',
                'center': 'items-center justify-center',
                'right': 'items-center justify-end'
            };
            
            const textAlignment = this.contentAlignment === 'center' ? 'text-center' : 'text-left';
            const containerAlignment = alignmentClasses[this.contentAlignment] || 'items-center justify-start';
            
            const overlayOpacityHex = Math.round(this.overlayOpacity * 2.55).toString(16).padStart(2, '0');
            const overlayOpacityHex2 = Math.round(this.overlayOpacity * 1.275).toString(16).padStart(2, '0');
            
            const buttonClasses = this.buttonStyle === 'filled' 
                ? `color: ${this.buttonTextColor}; background-color: ${this.buttonBgColor}; border-color: ${this.buttonBgColor};`
                : `color: ${this.buttonTextColor}; background-color: transparent; border-color: ${this.buttonTextColor};`;
            
            let html = '';
            
            // 오버레이
            if (this.overlayEnabled) {
                html += `<div class="absolute inset-0 z-10" style="background: linear-gradient(to right, ${this.overlayColor}${overlayOpacityHex}, ${this.overlayColor}${overlayOpacityHex2}, transparent);"></div>`;
            }
            
            // 콘텐츠
            html += `<div class="relative h-full z-20">
                        <div class="h-full max-w-7xl mx-auto px-8">
                            <div class="flex h-full ${containerAlignment}">
                                <div class="max-w-xl ${textAlignment}">`;
            
            // 부제목
            if (this.subtitle) {
                html += `<p class="${this.subtitleSize} uppercase tracking-wider mb-2" style="color: ${this.subtitleColor}">${this.escapeHtml(this.subtitle)}</p>`;
            }
            
            // 제목
            if (this.title) {
                html += `<h1 class="${this.titleSize} font-bold mb-4" style="color: ${this.titleColor}">${this.escapeHtml(this.title)}</h1>`;
            }
            
            // 설명
            if (this.description) {
                html += `<p class="${this.descriptionSize} mb-6 leading-relaxed" style="color: ${this.descriptionColor}">${this.escapeHtml(this.description)}</p>`;
            }
            
            // 버튼
            if (this.buttonText) {
                html += `<button class="inline-flex items-center px-8 py-3 rounded-full transition-all duration-300 border-2" style="${buttonClasses}">${this.escapeHtml(this.buttonText)}</button>`;
            }
            
            html += `       </div>
                        </div>
                    </div>
                </div>`;
            
            container.innerHTML = html;
        },
        
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }
}
</script>
