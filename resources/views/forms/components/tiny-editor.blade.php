@php
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
            tinymceInstance: null,
            isUploading: false,
            
            init() {
                const editorId = 'tiny-editor-' + Math.random().toString(36).substr(2, 9);
                this.$refs.editor.id = editorId;
                
                tinymce.init({
                    selector: '#' + editorId,
                    plugins: @js($getPlugins()),
                    toolbar: @js(implode(' ', $getToolbarButtons())),
                    min_height: {{ $getMinHeight() }},
                    max_height: {{ $getMaxHeight() }},
                    menubar: false,
                    branding: false,
                    relative_urls: false,
                    remove_script_host: false,
                    convert_urls: false,
                    image_caption: true,
                    image_advtab: true,
                    image_dimensions: true,
                    automatic_uploads: true,
                    file_picker_types: 'image',
                    images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        
                        this.isUploading = true;
                        
                        @this.upload('{{ $statePath }}-upload', blobInfo.blob(), 
                            (uploadedFilename) => {
                                @this.getFormComponentFileAttachmentUrl('{{ $statePath }}', uploadedFilename).then(url => {
                                    this.isUploading = false;
                                    resolve(url);
                                });
                            },
                            () => {
                                this.isUploading = false;
                                reject('Upload failed');
                            },
                            (event) => {
                                progress(event.detail.progress);
                            }
                        );
                    }),
                    setup: (editor) => {
                        this.tinymceInstance = editor;
                        
                        editor.on('init', () => {
                            editor.setContent(this.state || '');
                        });
                        
                        editor.on('change keyup', () => {
                            this.state = editor.getContent();
                        });
                        
                        editor.on('focus', () => {
                            this.$dispatch('tinymce-focus');
                        });
                        
                        editor.on('blur', () => {
                            this.$dispatch('tinymce-blur');
                        });
                        
                        this.$watch('state', (value) => {
                            if (editor.getContent() !== value) {
                                editor.setContent(value || '');
                            }
                        });
                    }
                });
            },
            
            destroy() {
                if (this.tinymceInstance) {
                    this.tinymceInstance.destroy();
                }
            }
        }"
        wire:ignore
        x-init="init()"
        x-on:destroy="destroy()"
        class="tiny-editor-container"
    >
        <div class="relative">
            <textarea
                x-ref="editor"
                {{ $isDisabled() ? 'disabled' : '' }}
                {!! $isRequired() ? 'required' : '' !!}
                class="hidden"
            ></textarea>
            
            <div x-show="isUploading" class="absolute inset-0 bg-gray-50 bg-opacity-75 flex items-center justify-center z-10">
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
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js"></script>
        @endpush
    @endonce
</x-dynamic-component>