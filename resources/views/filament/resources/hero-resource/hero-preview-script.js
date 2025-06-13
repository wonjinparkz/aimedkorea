document.addEventListener('alpine:init', () => {
    Alpine.data('heroPreview', () => ({
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
        
        initPreview() {
            // 폼 변경 감지
            setTimeout(() => {
                document.addEventListener('input', (e) => {
                    setTimeout(() => this.updateFromForm(), 100);
                });
                
                document.addEventListener('change', (e) => {
                    setTimeout(() => this.updateFromForm(), 100);
                });
                
                // 초기 렌더링
                this.updateFromForm();
                this.updatePreview();
            }, 500);
        },
        
        updateFromForm() {
            // 폼 데이터 읽기
            const titleInput = document.querySelector('input[name="data.title"]');
            const subtitleInput = document.querySelector('input[name="data.subtitle"]');
            const descriptionInput = document.querySelector('textarea[name="data.description"]');
            const buttonTextInput = document.querySelector('input[name="data.button_text"]');
            
            if (titleInput) this.title = titleInput.value || '';
            if (subtitleInput) this.subtitle = subtitleInput.value || '';
            if (descriptionInput) this.description = descriptionInput.value || '';
            if (buttonTextInput) this.buttonText = buttonTextInput.value || '';
            
            // 스타일 설정 읽기
            const titleColorInput = document.querySelector('input[name="data.hero_settings.title.color"]');
            const titleSizeInput = document.querySelector('select[name="data.hero_settings.title.size"]');
            const subtitleColorInput = document.querySelector('input[name="data.hero_settings.subtitle.color"]');
            const subtitleSizeInput = document.querySelector('select[name="data.hero_settings.subtitle.size"]');
            const descriptionColorInput = document.querySelector('input[name="data.hero_settings.description.color"]');
            const descriptionSizeInput = document.querySelector('select[name="data.hero_settings.description.size"]');
            const buttonTextColorInput = document.querySelector('input[name="data.hero_settings.button.text_color"]');
            const buttonBgColorInput = document.querySelector('input[name="data.hero_settings.button.bg_color"]');
            const buttonStyleInput = document.querySelector('select[name="data.hero_settings.button.style"]');
            const contentAlignmentInputs = document.querySelectorAll('input[name="data.hero_settings.content_alignment"]');
            const overlayEnabledInput = document.querySelector('input[name="data.hero_settings.overlay.enabled"]');
            const overlayColorInput = document.querySelector('input[name="data.hero_settings.overlay.color"]');
            const overlayOpacityInput = document.querySelector('input[name="data.hero_settings.overlay.opacity"]');
            
            if (titleColorInput) this.titleColor = titleColorInput.value || '#FFFFFF';
            if (titleSizeInput) this.titleSize = titleSizeInput.value || 'text-5xl';
            if (subtitleColorInput) this.subtitleColor = subtitleColorInput.value || '#E5E7EB';
            if (subtitleSizeInput) this.subtitleSize = subtitleSizeInput.value || 'text-sm';
            if (descriptionColorInput) this.descriptionColor = descriptionColorInput.value || '#D1D5DB';
            if (descriptionSizeInput) this.descriptionSize = descriptionSizeInput.value || 'text-lg';
            if (buttonTextColorInput) this.buttonTextColor = buttonTextColorInput.value || '#FFFFFF';
            if (buttonBgColorInput) this.buttonBgColor = buttonBgColorInput.value || '#3B82F6';
            if (buttonStyleInput) this.buttonStyle = buttonStyleInput.value || 'filled';
            
            contentAlignmentInputs.forEach(input => {
                if (input.checked) this.contentAlignment = input.value;
            });
            
            if (overlayEnabledInput) this.overlayEnabled = overlayEnabledInput.checked;
            if (overlayColorInput) this.overlayColor = overlayColorInput.value || '#000000';
            if (overlayOpacityInput) this.overlayOpacity = parseInt(overlayOpacityInput.value) || 60;
            
            this.updatePreview();
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
    }));
});
