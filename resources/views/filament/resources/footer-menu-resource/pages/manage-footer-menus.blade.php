<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}
        
        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                <x-heroicon-o-check class="w-5 h-5 mr-2" />
                설정 저장
            </x-filament::button>
        </div>
    </form>

    <style>
        /* 고령자 친화적인 큰 폰트와 명확한 스타일 */
        .fi-fo-field-wrp label {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
        }
        
        .fi-fo-field-wrp input,
        .fi-fo-field-wrp select,
        .fi-fo-field-wrp textarea {
            font-size: 1.05rem !important;
            padding: 0.75rem !important;
        }
        
        .fi-fo-help-text {
            font-size: 0.95rem !important;
            color: #4b5563 !important;
            margin-top: 0.5rem !important;
        }
        
        .fi-fo-repeater-item {
            border: 2px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            margin-bottom: 1rem !important;
        }
        
        .fi-fo-repeater-item-label {
            font-size: 1.05rem !important;
            font-weight: 600 !important;
            color: #1f2937 !important;
        }
        
        /* 버튼 크기 증가 */
        .fi-btn {
            padding: 0.75rem 1.5rem !important;
            font-size: 1rem !important;
        }
        
        /* 드래그 핸들 크기 증가 */
        .fi-fo-repeater-item-handle {
            width: 2rem !important;
            height: 2rem !important;
        }
        
        /* 섹션 제목 스타일 */
        .fi-section-header-heading {
            font-size: 1.25rem !important;
            color: #111827 !important;
        }
        
        .fi-section-header-description {
            font-size: 1rem !important;
            color: #6b7280 !important;
        }
    </style>
</x-filament-panels::page>
