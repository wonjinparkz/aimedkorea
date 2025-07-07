<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}
        
        <div class="mt-8 flex justify-end gap-3">
            <x-filament::button 
                type="button" 
                color="gray" 
                size="lg"
                wire:click="$dispatch('open-preview-modal')"
                icon="heroicon-o-eye">
                <span class="text-lg">👁 미리보기</span>
            </x-filament::button>
            
            <x-filament::button 
                type="submit" 
                size="lg"
                icon="heroicon-o-check">
                <span class="text-lg">💾 메뉴 저장</span>
            </x-filament::button>
        </div>
    </form>

    <!-- 미리보기 모달 -->
    <x-filament::modal id="preview-modal" width="7xl">
        <x-slot name="header">
            <h3 class="text-xl font-bold">메뉴 미리보기</h3>
        </x-slot>
        
        <div class="preview-container">
            <div class="bg-gray-900 p-4 rounded-lg">
                <nav class="flex items-center justify-between">
                    <div class="flex items-center space-x-8">
                        <span class="text-white font-bold text-xl">LOGO</span>
                        <ul class="flex space-x-6" id="preview-menu">
                            <!-- JavaScript로 동적 생성 -->
                        </ul>
                    </div>
                </nav>
            </div>
            
            <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                <h4 class="font-semibold mb-2">메뉴 구조 트리뷰</h4>
                <div id="menu-tree" class="text-sm">
                    <!-- JavaScript로 동적 생성 -->
                </div>
            </div>
        </div>
    </x-filament::modal>

    <style>
        /* 고령자 친화적인 큰 폰트와 명확한 스타일 */
        .fi-fo-field-wrp label {
            font-size: 1.15rem !important;
            font-weight: 600 !important;
            color: #1f2937 !important;
        }
        
        .fi-fo-field-wrp input,
        .fi-fo-field-wrp select {
            font-size: 1.1rem !important;
            padding: 0.875rem !important;
            border-width: 2px !important;
        }
        
        .fi-fo-field-wrp input:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        
        .fi-fo-help-text {
            font-size: 1rem !important;
            color: #6b7280 !important;
            margin-top: 0.5rem !important;
            line-height: 1.5 !important;
        }
        
        .fi-fo-repeater-item {
            border: 3px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            margin-bottom: 1.5rem !important;
            background-color: #fafafa !important;
            transition: all 0.2s !important;
        }
        
        .fi-fo-repeater-item:hover {
            border-color: #93c5fd !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }
        
        .fi-fo-repeater-item-label {
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
            padding: 0.75rem !important;
        }
        
        /* 버튼 크기 증가 */
        .fi-btn {
            padding: 1rem 2rem !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            border-radius: 0.75rem !important;
        }
        
        .fi-btn-icon {
            width: 1.5rem !important;
            height: 1.5rem !important;
        }
        
        /* 드래그 핸들 크기 증가 */
        .fi-fo-repeater-item-handle {
            width: 3rem !important;
            height: 3rem !important;
            cursor: grab !important;
        }
        
        .fi-fo-repeater-item-handle:active {
            cursor: grabbing !important;
        }
        
        .fi-fo-repeater-item-handle svg {
            width: 2rem !important;
            height: 2rem !important;
        }
        
        /* 추가/삭제 버튼 크기 증가 */
        .fi-fo-repeater-add-action,
        .fi-fo-repeater-delete-action {
            padding: 0.75rem 1.5rem !important;
            font-size: 1.05rem !important;
        }
        
        /* 토글 스위치 크기 증가 */
        .fi-fo-toggle input[type="checkbox"] {
            width: 3rem !important;
            height: 1.75rem !important;
        }
        
        .fi-fo-toggle input[type="checkbox"]:after {
            width: 1.25rem !important;
            height: 1.25rem !important;
        }
        
        /* 섹션 헤더 스타일 */
        .fi-section-header {
            padding: 1rem !important;
            background-color: #f3f4f6 !important;
            border-radius: 0.5rem !important;
            margin-bottom: 1rem !important;
        }
        
        .fi-section-header-heading {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
        }
        
        /* 미리보기 스타일 */
        #preview-menu li {
            position: relative;
        }
        
        #preview-menu li:hover > ul {
            display: block;
        }
        
        #preview-menu ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            min-width: 200px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            padding: 0.5rem 0;
        }
        
        #preview-menu ul li {
            padding: 0.5rem 1rem;
            color: #374151;
            hover: background-color: #f3f4f6;
        }
        
        #menu-tree ul {
            margin-left: 1.5rem;
            border-left: 2px dotted #e5e7eb;
        }
        
        #menu-tree li {
            padding: 0.5rem 0;
            position: relative;
        }
        
        #menu-tree li:before {
            content: "└─";
            position: absolute;
            left: -1.5rem;
            color: #9ca3af;
        }
        
        /* 반응형 조정 */
        @media (max-width: 768px) {
            .fi-fo-field-wrp label {
                font-size: 1.05rem !important;
            }
            
            .fi-fo-field-wrp input {
                font-size: 1rem !important;
            }
        }
    </style>

    <script>
        document.addEventListener('livewire:init', function () {
            // 미리보기 모달 열기 이벤트 리스너
            Livewire.on('open-preview-modal', () => {
                updatePreview();
                document.getElementById('preview-modal').showModal();
            });
            
            // 폼 데이터 변경 감지
            Livewire.on('form-updated', () => {
                if (document.getElementById('preview-modal').open) {
                    updatePreview();
                }
            });
        });
        
        function updatePreview() {
            const menuData = @json($this->getPreviewData());
            
            // 메뉴 미리보기 생성
            const previewMenu = document.getElementById('preview-menu');
            previewMenu.innerHTML = '';
            
            menuData.forEach(item => {
                if (!item.active) return;
                
                const li = document.createElement('li');
                li.className = 'relative group';
                
                const a = document.createElement('a');
                a.href = item.url || '#';
                a.className = 'text-white hover:text-gray-300 transition-colors px-3 py-2 text-lg font-medium';
                a.textContent = item.label;
                li.appendChild(a);
                
                // 하위 메뉴가 있는 경우
                if (item.children && item.children.length > 0) {
                    const submenu = document.createElement('ul');
                    submenu.className = 'hidden group-hover:block absolute top-full left-0 bg-white shadow-lg rounded-lg py-2 min-w-[200px]';
                    
                    item.children.forEach(child => {
                        const subLi = document.createElement('li');
                        const subA = document.createElement('a');
                        subA.href = child.url || '#';
                        subA.className = 'block px-4 py-2 text-gray-700 hover:bg-gray-100 text-base';
                        subA.textContent = child.label;
                        subLi.appendChild(subA);
                        submenu.appendChild(subLi);
                    });
                    
                    li.appendChild(submenu);
                }
                
                previewMenu.appendChild(li);
            });
            
            // 트리뷰 생성
            const menuTree = document.getElementById('menu-tree');
            menuTree.innerHTML = '';
            
            const treeUl = document.createElement('ul');
            menuData.forEach((item, index) => {
                const treeLi = document.createElement('li');
                treeLi.className = 'font-medium';
                
                const icon = item.active ? '✅' : '❌';
                const hasChildren = item.children && item.children.length > 0;
                const folderIcon = hasChildren ? '📁' : '📄';
                
                treeLi.innerHTML = `${icon} ${folderIcon} ${item.label} ${item.url ? `<span class="text-gray-500 text-xs">(${item.url})</span>` : ''}`;
                
                if (hasChildren) {
                    const childUl = document.createElement('ul');
                    childUl.className = 'mt-2';
                    
                    item.children.forEach(child => {
                        const childLi = document.createElement('li');
                        childLi.className = 'text-gray-600';
                        childLi.innerHTML = `└─ ${child.label} <span class="text-gray-500 text-xs">(${child.url})</span>`;
                        childUl.appendChild(childLi);
                    });
                    
                    treeLi.appendChild(childUl);
                }
                
                treeUl.appendChild(treeLi);
            });
            
            menuTree.appendChild(treeUl);
        }
    </script>
</x-filament-panels::page>