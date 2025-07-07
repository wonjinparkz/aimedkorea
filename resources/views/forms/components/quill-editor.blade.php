@php
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div 
        wire:ignore
        x-data="{
            state: $wire.$entangle('{{ $statePath }}'),
            quill: null,
            isUploading: false,
            
            init() {
                const self = this;
                
                // Register custom sizes
                const Size = Quill.import('attributors/style/size');
                Size.whitelist = ['12px', '14px', '16px', '18px', '20px', '24px', '30px', '36px', '48px'];
                Quill.register(Size, true);
                
                // Initialize Quill
                this.quill = new Quill(this.$refs.editor, {
                    theme: 'snow',
                    modules: {
                        toolbar: {{ Js::from($getModules()['toolbar']) }}
                    },
                    placeholder: 'Write something...'
                });
                
                // Add Korean tooltips
                this.addKoreanTooltips();
                
                // Set initial content with a small delay to ensure state is loaded
                this.$nextTick(() => {
                    if (this.state) {
                        this.quill.root.innerHTML = this.state;
                    }
                });
                
                // Handle text changes
                this.quill.on('text-change', () => {
                    this.state = this.quill.root.innerHTML;
                });
                
                // Handle image uploads
                this.quill.getModule('toolbar').addHandler('image', () => {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.click();
                    
                    input.onchange = async () => {
                        const file = input.files[0];
                        if (file) {
                            self.uploadImage(file);
                        }
                    };
                });
                
                // Enable image resizing
                this.enableImageResize();
                
                // Watch for external state changes
                this.$watch('state', (value) => {
                    if (this.quill && this.quill.root.innerHTML !== value && value !== null) {
                        this.quill.root.innerHTML = value;
                    }
                });
            },
            
            enableImageResize() {
                let resizing = false;
                let currentImage = null;
                let startX = 0;
                let startY = 0;
                let startWidth = 0;
                const self = this;
                
                // Make images resizable by adding resize cursor on hover
                this.quill.root.addEventListener('mousemove', (e) => {
                    if (e.target.tagName === 'IMG' && !resizing) {
                        const rect = e.target.getBoundingClientRect();
                        const nearRightEdge = e.clientX > rect.right - 50;
                        const nearLeftEdge = e.clientX < rect.left + 50;
                        const nearBottomEdge = e.clientY > rect.bottom - 50;
                        
                        if (nearRightEdge || nearLeftEdge || nearBottomEdge) {
                            e.target.style.cursor = 'nwse-resize';
                        } else {
                            e.target.style.cursor = 'pointer';
                        }
                    }
                });
                
                // Add click handler to images
                this.quill.root.addEventListener('click', (e) => {
                    if (e.target.tagName === 'IMG') {
                        // Remove previous selection
                        self.quill.root.querySelectorAll('img.selected').forEach(img => {
                            img.classList.remove('selected');
                        });
                        
                        // Select clicked image
                        e.target.classList.add('selected');
                        currentImage = e.target;
                        
                        // Show float options
                        self.showImageFloatOptions(e.target);
                    } else if (!e.target.closest('.image-float-toolbar')) {
                        // Remove selection
                        self.quill.root.querySelectorAll('img.selected').forEach(img => {
                            img.classList.remove('selected');
                        });
                        currentImage = null;
                        self.hideImageFloatOptions();
                    }
                });
                
                // Handle resize
                this.quill.root.addEventListener('mousedown', (e) => {
                    if (e.target.tagName === 'IMG') {
                        const rect = e.target.getBoundingClientRect();
                        const nearRightEdge = e.clientX > rect.right - 50;
                        const nearLeftEdge = e.clientX < rect.left + 50;
                        const nearBottomEdge = e.clientY > rect.bottom - 50;
                        
                        if (nearRightEdge || nearLeftEdge || nearBottomEdge) {
                            resizing = true;
                            currentImage = e.target;
                            startX = e.clientX;
                            startY = e.clientY;
                            startWidth = rect.width;
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    }
                });
                
                // Handle mouse move for resizing
                document.addEventListener('mousemove', (e) => {
                    if (resizing && currentImage) {
                        const deltaX = e.clientX - startX;
                        const newWidth = Math.max(50, startWidth + deltaX);
                        currentImage.style.width = newWidth + 'px';
                        currentImage.style.height = 'auto';
                    }
                });
                
                // Stop resizing on mouse up
                document.addEventListener('mouseup', () => {
                    if (resizing && currentImage) {
                        resizing = false;
                        // Update state after resize
                        self.state = self.quill.root.innerHTML;
                    }
                });
            },
            
            uploadImage(file) {
                this.isUploading = true;
                const range = this.quill.getSelection(true);
                const self = this;
                
                // Create FormData and upload manually
                const reader = new FileReader();
                reader.onload = (e) => {
                    // Insert image as base64 temporarily
                    const tempId = 'temp-' + Date.now();
                    self.quill.insertEmbed(range.index, 'image', e.target.result);
                    const tempImg = self.quill.root.querySelector('img[src=\'' + e.target.result + '\']');
                    if (tempImg) {
                        tempImg.setAttribute('data-temp-id', tempId);
                    }
                };
                reader.readAsDataURL(file);
                
                // Upload to server
                $wire.upload('{{ $statePath }}-upload', file,
                    (uploadedFilename) => {
                        $wire.getFormComponentFileAttachmentUrl('{{ $statePath }}', uploadedFilename).then(url => {
                            // Replace temporary image with actual URL
                            const tempImg = self.quill.root.querySelector('img[data-temp-id]');
                            if (tempImg) {
                                tempImg.src = url;
                                tempImg.removeAttribute('data-temp-id');
                            }
                            self.isUploading = false;
                            // Update state with new content
                            self.state = self.quill.root.innerHTML;
                        });
                    },
                    () => {
                        self.isUploading = false;
                        // Remove temporary image on failure
                        const tempImg = self.quill.root.querySelector('img[data-temp-id]');
                        if (tempImg) {
                            tempImg.remove();
                        }
                        alert('Upload failed');
                    }
                );
            },
            
            showImageFloatOptions(img) {
                // Remove existing toolbar if any
                this.hideImageFloatOptions();
                
                // Create float toolbar
                const toolbar = document.createElement('div');
                toolbar.className = 'image-float-toolbar';
                toolbar.style.position = 'absolute';
                toolbar.style.background = '#fff';
                toolbar.style.border = '1px solid #ccc';
                toolbar.style.borderRadius = '4px';
                toolbar.style.padding = '5px';
                toolbar.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                toolbar.style.zIndex = '1000';
                
                // Position toolbar above the image
                const rect = img.getBoundingClientRect();
                const editorRect = this.$refs.editor.getBoundingClientRect();
                toolbar.style.left = (rect.left - editorRect.left) + 'px';
                toolbar.style.top = (rect.top - editorRect.top - 40) + 'px';
                
                // Create float buttons
                const floatOptions = [
                    { class: 'float-left', icon: '⬅', title: '왼쪽 정렬' },
                    { class: 'float-center', icon: '⬌', title: '가운데 정렬' },
                    { class: 'float-right', icon: '➡', title: '오른쪽 정렬' }
                ];
                
                floatOptions.forEach(option => {
                    const btn = document.createElement('button');
                    btn.className = 'ql-float';
                    btn.innerHTML = option.icon;
                    btn.title = option.title;
                    btn.style.fontSize = '16px';
                    
                    // Check if this option is currently active
                    if (img.classList.contains(option.class)) {
                        btn.classList.add('ql-active');
                    }
                    
                    btn.onclick = (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Remove all float classes
                        img.classList.remove('float-left', 'float-center', 'float-right');
                        
                        // Add the selected float class
                        img.classList.add(option.class);
                        
                        // Update state
                        this.state = this.quill.root.innerHTML;
                        
                        // Update toolbar
                        this.showImageFloatOptions(img);
                    };
                    
                    toolbar.appendChild(btn);
                });
                
                // Add toolbar to editor
                this.$refs.editor.appendChild(toolbar);
            },
            
            hideImageFloatOptions() {
                const toolbar = this.$refs.editor.querySelector('.image-float-toolbar');
                if (toolbar) {
                    toolbar.remove();
                }
            },
            
            addKoreanTooltips() {
                const tooltips = {
                    'ql-bold': '굵게 - 텍스트를 굵은 글씨로 표시합니다',
                    'ql-italic': '기울임꼴 - 텍스트를 기울임꼴로 표시합니다',
                    'ql-underline': '밑줄 - 텍스트에 밑줄을 표시합니다',
                    'ql-strike': '취소선 - 텍스트에 취소선을 표시합니다',
                    'ql-header': '제목 크기 - 제목 스타일을 선택합니다',
                    'ql-size': '글자 크기 - 텍스트 크기를 조절합니다',
                    'ql-list[value=&quot;ordered&quot;]': '번호 목록 - 번호가 매겨진 목록을 만듭니다',
                    'ql-list[value=&quot;bullet&quot;]': '글머리 기호 - 글머리 기호 목록을 만듭니다',
                    'ql-color': '글자색 - 텍스트 색상을 변경합니다',
                    'ql-background': '배경색 - 텍스트 배경색을 변경합니다',
                    'ql-align': '정렬 - 텍스트 정렬 방식을 선택합니다',
                    'ql-link': '링크 - 하이퍼링크를 추가합니다',
                    'ql-image': '이미지 - 이미지를 삽입합니다',
                    'ql-video': '동영상 - 동영상을 삽입합니다'
                };
                
                // Add tooltips to buttons
                setTimeout(() => {
                    Object.keys(tooltips).forEach(selector => {
                        try {
                            let elements;
                            if (selector.includes('[')) {
                                // Handle attribute selectors
                                const baseClass = selector.split('[')[0];
                                const attrPart = selector.split('[')[1].replace(']', '');
                                const [attr, value] = attrPart.split('=');
                                const cleanValue = value.replace(/&quot;/g, '');
                                elements = this.$refs.editor.parentElement.querySelectorAll('.' + baseClass + '[' + attr + '=&quot;' + cleanValue + '&quot;]');
                            } else {
                                elements = this.$refs.editor.parentElement.querySelectorAll('.' + selector);
                            }
                            elements.forEach(el => {
                                el.setAttribute('title', tooltips[selector]);
                            });
                        } catch (e) {
                            console.log('Error adding tooltip for', selector);
                        }
                    });
                    
                    // Special handling for dropdown selects
                    const sizeSelect = this.$refs.editor.parentElement.querySelector('.ql-size');
                    if (sizeSelect) {
                        sizeSelect.setAttribute('title', '글자 크기 - 텍스트 크기를 조절합니다');
                    }
                    
                    const headerSelect = this.$refs.editor.parentElement.querySelector('.ql-header');
                    if (headerSelect) {
                        headerSelect.setAttribute('title', '제목 크기 - 제목 스타일을 선택합니다');
                    }
                }, 100);
            }
        }"
        class="quill-editor-container relative"
    >
        <div class="relative">
            <div
                x-ref="editor"
                class="bg-white"
                style="min-height: {{ $getMinHeight() }}px"
            ></div>
            
            <div x-show="isUploading" x-cloak class="absolute inset-0 bg-gray-50 bg-opacity-75 flex items-center justify-center z-10">
                <div class="text-gray-500">
                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="mt-2 block text-sm">Uploading image...</span>
                </div>
            </div>
        </div>
    </div>
    
    @once
        @push('styles')
            <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
            <style>
                [x-cloak] { display: none !important; }
                /* Default editor styles */
                .ql-editor {
                    font-size: 1.125rem; /* 18px - LG */
                    line-height: 1.5;
                    position: relative;
                }
                .ql-editor img {
                    max-width: 100%;
                    cursor: pointer;
                    display: inline-block;
                    margin: 10px;
                }
                .ql-editor img:hover {
                    opacity: 0.9;
                }
                .ql-editor img.selected {
                    outline: 3px solid #3b82f6;
                    outline-offset: 2px;
                }
                
                /* Visual feedback for resize areas */
                .ql-editor img.selected::after {
                    content: '';
                    position: absolute;
                    right: -10px;
                    bottom: -10px;
                    width: 20px;
                    height: 20px;
                    background: #3b82f6;
                    border-radius: 50%;
                    cursor: nwse-resize;
                }
                .ql-editor img.float-left {
                    float: left;
                    margin: 10px 20px 10px 0;
                }
                .ql-editor img.float-right {
                    float: right;
                    margin: 10px 0 10px 20px;
                }
                .ql-editor img.float-center {
                    display: block;
                    margin: 10px auto;
                    float: none;
                }
                /* Float toolbar buttons */
                .ql-snow .ql-tooltip.ql-editing {
                    left: 0 !important;
                }
                .ql-float {
                    display: inline-block;
                    width: 27px;
                    height: 27px;
                    margin: 3px;
                    background: #fff;
                    border: 1px solid #ccc;
                    cursor: pointer;
                }
                .ql-float:hover {
                    background: #f0f0f0;
                }
                .ql-float.ql-active {
                    background: #06c;
                    color: #fff;
                }
                /* Enhanced tooltips */
                .ql-toolbar button[title]:hover::after,
                .ql-toolbar .ql-picker-label[title]:hover::after {
                    content: attr(title);
                    position: absolute;
                    bottom: -30px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: rgba(0, 0, 0, 0.8);
                    color: white;
                    padding: 5px 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    white-space: nowrap;
                    z-index: 1000;
                    pointer-events: none;
                }
                .ql-toolbar button,
                .ql-toolbar .ql-picker-label {
                    position: relative;
                }
                
                /* Tailwind-based font sizes */
                .ql-snow .ql-picker.ql-size .ql-picker-label::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item::before {
                    content: 'Base';
                }
                .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="12px"]::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="12px"]::before {
                    content: 'XS (12px)';
                }
                .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="14px"]::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="14px"]::before {
                    content: 'SM (14px)';
                }
                .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="16px"]::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="16px"]::before {
                    content: 'Base (16px)';
                }
                .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="18px"]::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="18px"]::before {
                    content: 'LG (18px)';
                }
                .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="20px"]::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="20px"]::before {
                    content: 'XL (20px)';
                }
                .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="24px"]::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="24px"]::before {
                    content: '2XL (24px)';
                }
                .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="30px"]::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="30px"]::before {
                    content: '3XL (30px)';
                }
                .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="36px"]::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="36px"]::before {
                    content: '4XL (36px)';
                }
                .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="48px"]::before,
                .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="48px"]::before {
                    content: '5XL (48px)';
                }
                
                /* Apply actual sizes with line-height */
                .ql-editor .ql-size-12px {
                    font-size: 0.75rem; /* 12px */
                    line-height: 1.5;
                }
                .ql-editor .ql-size-14px {
                    font-size: 0.875rem; /* 14px */
                    line-height: 1.5;
                }
                .ql-editor .ql-size-16px {
                    font-size: 1rem; /* 16px - default */
                    line-height: 1.5;
                }
                .ql-editor .ql-size-18px {
                    font-size: 1.125rem; /* 18px */
                    line-height: 1.5;
                }
                .ql-editor .ql-size-20px {
                    font-size: 1.25rem; /* 20px */
                    line-height: 1.5;
                }
                .ql-editor .ql-size-24px {
                    font-size: 1.5rem; /* 24px */
                    line-height: 1.5;
                }
                .ql-editor .ql-size-30px {
                    font-size: 1.875rem; /* 30px */
                    line-height: 1.5;
                }
                .ql-editor .ql-size-36px {
                    font-size: 2.25rem; /* 36px */
                    line-height: 1.5;
                }
                .ql-editor .ql-size-48px {
                    font-size: 3rem; /* 48px */
                    line-height: 1.5;
                }
                
                /* Apply line-height to all elements */
                .ql-editor p,
                .ql-editor h1,
                .ql-editor h2,
                .ql-editor h3,
                .ql-editor h4,
                .ql-editor h5,
                .ql-editor h6,
                .ql-editor li,
                .ql-editor blockquote {
                    line-height: 1.5;
                }
                
                /* Apply prose-lg paragraph spacing */
                .ql-editor p {
                    margin-top: 1.3333333em;
                    margin-bottom: 1.3333333em;
                }
                
                /* First paragraph no top margin */
                .ql-editor p:first-child {
                    margin-top: 0;
                }
                
                /* Last paragraph no bottom margin */
                .ql-editor p:last-child {
                    margin-bottom: 0;
                }
                
                /* Spacing for headings (prose-lg style) */
                .ql-editor h1 {
                    margin-top: 0;
                    margin-bottom: 0.8888889em;
                }
                
                .ql-editor h2 {
                    margin-top: 1.8666667em;
                    margin-bottom: 1.0666667em;
                }
                
                .ql-editor h3 {
                    margin-top: 1.6em;
                    margin-bottom: 0.6em;
                }
                
                .ql-editor h4 {
                    margin-top: 1.7777778em;
                    margin-bottom: 0.4444444em;
                }
                
                /* List spacing */
                .ql-editor ul,
                .ql-editor ol {
                    margin-top: 1.3333333em;
                    margin-bottom: 1.3333333em;
                }
                
                .ql-editor li {
                    margin-top: 0.5333333em;
                    margin-bottom: 0.5333333em;
                }
                
                /* Blockquote spacing */
                .ql-editor blockquote {
                    margin-top: 1.6666667em;
                    margin-bottom: 1.6666667em;
                }
                
                /* Adjacent sibling spacing */
                .ql-editor * + * {
                    margin-top: 0;
                }
                
                .ql-editor p + p {
                    margin-top: 1.3333333em;
                }
                
                .ql-editor h1 + *,
                .ql-editor h2 + *,
                .ql-editor h3 + *,
                .ql-editor h4 + *,
                .ql-editor h5 + *,
                .ql-editor h6 + * {
                    margin-top: 0;
                }
                
                .ql-editor * + h2 {
                    margin-top: 1.8666667em;
                }
                
                .ql-editor * + h3 {
                    margin-top: 1.6em;
                }
                
                .ql-editor * + h4 {
                    margin-top: 1.7777778em;
                }
            </style>
        @endpush
        
        @push('scripts')
            <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        @endpush
    @endonce
</x-dynamic-component>