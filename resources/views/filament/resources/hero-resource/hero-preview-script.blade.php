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
            // Livewire 데이터 감지
            if (window.Livewire) {
                Livewire.on('form-data-updated', (data) => {
                    this.updateFromLivewire(data);
                });
                
                // 초기 데이터 로드
                setTimeout(() => {
                    const component = Livewire.all()[0];
                    if (component && component.data) {
                        this.updateFromLivewire(component.data);
                    }
                }, 500);
            }
            
            // 폼 변경 감지
            document.addEventListener('input', (e) => {
                setTimeout(() => this.updateFromForm(), 100);
            });
            
            document.addEventListener('change', (e) => {
                setTimeout(() => this.updateFromForm(), 100);
            });
            
            // 초기 렌더링
            this.updateFromForm();
            this.updatePreview();
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
        
        updateFromLivewire(data) {
            if (data.title !== undefined) this.title = data.title || '';
            if (data.subtitle !== undefined) this.subtitle = data.subtitle || '';
            if (data.description !== undefined) this.description = data.description || '';
            if (data.button_text !== undefined) this.buttonText = data.button_text || '';
            
            if (data.hero_settings) {
                const settings = data.hero_settings;
                if (settings.title) {
                    this.titleColor = settings.title.color || '#FFFFFF';
                    this.titleSize = settings.title.size || 'text-5xl';
                }
                if (settings.subtitle) {
                    this.subtitleColor = settings.subtitle.color || '#E5E7EB';
                    this.subtitleSize = settings.subtitle.size || 'text-sm';
                }
                if (settings.description) {
                    this.descriptionColor = settings.description.color || '#D1D5DB';
                    this.descriptionSize = settings.description.size || 'text-lg';
                }
                if (settings.button) {
                    this.buttonTextColor = settings.button.text_color || '#FFFFFF';
                    this.buttonBgColor = settings.button.bg_color || '#3B82F6';
                    this.buttonStyle = settings.button.style || 'filled';
                }
                if (settings.content_alignment !== undefined) {
                    this.contentAlignment = settings.content_alignment || 'left';
                }
                if (settings.overlay) {
                    this.overlayEnabled = settings.overlay.enabled !== false;
                    this.overlayColor = settings.overlay.color || '#000000';
                    this.overlayOpacity = settings.overlay.opacity || 60;
                }
            }
            
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
</script>
