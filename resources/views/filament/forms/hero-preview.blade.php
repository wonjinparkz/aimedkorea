<div x-data="heroPreview()" x-init="init()">
    <iframe 
        id="hero-preview-iframe"
        style="width: 100%; height: 320px; border: none; border-radius: 8px; background: #000;"
        srcdoc="">
    </iframe>
    
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
            if (this.$wire.data.hero_settings) {
                this.$watch('$wire.data.hero_settings.title.color', value => { this.titleColor = value || '#FFFFFF'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.title.size', value => { this.titleSize = value || 'text-5xl'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.subtitle.color', value => { this.subtitleColor = value || '#E5E7EB'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.subtitle.size', value => { this.subtitleSize = value || 'text-sm'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.description.color', value => { this.descriptionColor = value || '#D1D5DB'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.description.size', value => { this.descriptionSize = value || 'text-lg'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.button.text_color', value => { this.buttonTextColor = value || '#FFFFFF'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.button.bg_color', value => { this.buttonBgColor = value || '#3B82F6'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.button.style', value => { this.buttonStyle = value || 'filled'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.content_alignment', value => { this.contentAlignment = value || 'left'; this.updatePreview(); });
                
                // 오버레이 설정 감지
                this.$watch('$wire.data.hero_settings.overlay.enabled', value => { this.overlayEnabled = value !== false; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.overlay.color', value => { this.overlayColor = value || '#000000'; this.updatePreview(); });
                this.$watch('$wire.data.hero_settings.overlay.opacity', value => { this.overlayOpacity = value || 60; this.updatePreview(); });
            }
            
            // 초기값 설정
            this.loadInitialValues();
            this.updatePreview();
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
        
        updatePreview() {
            const iframe = document.getElementById('hero-preview-iframe');
            if (!iframe) return;
            
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
            
            const content = `
                <!DOCTYPE html>
                <html>
                <head>
                    <script src="https://cdn.tailwindcss.com"></script>
                    <style>
                        body { margin: 0; padding: 0; }
                    </style>
                </head>
                <body>
                    <div class="relative h-[320px] bg-black overflow-hidden">
                        ${this.overlayEnabled ? `
                        <div class="absolute inset-0 z-10" 
                             style="background: linear-gradient(to right, ${this.overlayColor}${overlayOpacityHex}, ${this.overlayColor}${overlayOpacityHex2}, transparent);">
                        </div>
                        ` : ''}
                        
                        <div class="relative h-full z-20">
                            <div class="h-full max-w-7xl mx-auto px-8">
                                <div class="flex h-full ${containerAlignment}">
                                    <div class="max-w-xl ${textAlignment}">
                                        ${this.subtitle ? `
                                        <p class="${this.subtitleSize} uppercase tracking-wider mb-2" 
                                           style="color: ${this.subtitleColor}">
                                            ${this.subtitle}
                                        </p>
                                        ` : ''}
                                        
                                        ${this.title ? `
                                        <h1 class="${this.titleSize} font-bold mb-4" 
                                            style="color: ${this.titleColor}">
                                            ${this.title}
                                        </h1>
                                        ` : ''}
                                        
                                        ${this.description ? `
                                        <p class="${this.descriptionSize} mb-6 leading-relaxed" 
                                           style="color: ${this.descriptionColor}">
                                            ${this.description}
                                        </p>
                                        ` : ''}
                                        
                                        ${this.buttonText ? `
                                        <button class="inline-flex items-center px-8 py-3 rounded-full transition-all duration-300 border-2"
                                                style="${buttonClasses}">
                                            ${this.buttonText}
                                        </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            `;
            
            iframe.srcdoc = content;
        }
    }
}
</script>
