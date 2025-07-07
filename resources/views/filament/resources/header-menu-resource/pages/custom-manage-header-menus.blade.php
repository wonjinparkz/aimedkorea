<x-filament-panels::page>
    <!-- CDN으로 SortableJS 로드 -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .menu-item-new {
            animation: fadeIn 0.3s ease-out;
        }
        .menu-header-box {
            transition: all 0.2s ease;
        }
        .menu-header-box.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }
        .active-menu {
            background-color: #fef3c7 !important;
            border-color: #f59e0b !important;
        }
        .active-menu span {
            color: #92400e !important;
        }
    </style>
    
    <div class="space-y-6">
        <!-- 상단 컨트롤 -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap gap-4">
                <button wire:click="addMenuItem" 
                        style="background-color: #f59e0b; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; transition: all 0.2s;"
                        onmouseover="this.style.backgroundColor='#d97706'" 
                        onmouseout="this.style.backgroundColor='#f59e0b'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    메인 메뉴 추가
                </button>
            </div>
        </div>
        
        <!-- 메뉴 편집 영역 -->
        <div class="space-y-6">
            <!-- 메뉴 구조 편집기 -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    메뉴 구조
                </h3>
                
                <div id="menu-editor">
                    @if(empty($menuItems))
                        <div class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <p class="text-lg">메뉴가 없습니다.</p>
                            <p class="mt-2">위의 "메인 메뉴 추가" 버튼을 클릭하여 시작하세요.</p>
                        </div>
                    @else
                        <!-- 가로형 헤더 박스 레이아웃 -->
                        <div id="main-menu-list" class="flex flex-wrap gap-3 mb-6">
                            @foreach($menuItems as $index => $item)
                                <div class="menu-header-box" data-menu-id="{{ $item['id'] }}" data-index="{{ $index }}">
                                    <div style="background-color: {{ $loop->first ? '#fef3c7' : '#f3f4f6' }}; padding: 16px; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 2px solid {{ $loop->first ? '#f59e0b' : 'transparent' }};"
                                         onmouseover="if(!this.classList.contains('active-menu')) { this.style.backgroundColor='#e5e7eb'; this.style.borderColor='#fbbf24'; }"
                                         onmouseout="if(!this.classList.contains('active-menu')) { this.style.backgroundColor='#f3f4f6'; this.style.borderColor='transparent'; }"
                                         class="{{ $loop->first ? 'active-menu' : '' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="menu-handle cursor-move" title="드래그하여 순서 변경">
                                                <svg class="w-6 h-6 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                                </svg>
                                            </div>
                                            <span style="font-weight: 600; color: {{ $loop->first ? '#92400e' : '#374151' }}; flex: 1;">{{ $item['label'] ?? '이름 없음' }}</span>
                                            <div class="flex items-center gap-2">
                                                @php
                                                    $childCount = 0;
                                                    if (!empty($item['children'])) {
                                                        $childCount = count($item['children']);
                                                    } elseif (!empty($item['groups'])) {
                                                        // mega 메뉴의 경우 groups 내의 모든 items 수 계산
                                                        foreach ($item['groups'] as $group) {
                                                            $childCount += count($group['items'] ?? []);
                                                        }
                                                    }
                                                @endphp
                                                @if($childCount > 0)
                                                    <span class="text-xs text-gray-500">({{ $childCount }})</span>
                                                @endif
                                                <button wire:click="deleteMenuItem('{{ $item['id'] }}')"
                                                        wire:confirm="정말로 이 메뉴를 삭제하시겠습니까?"
                                                        style="color: #dc2626; transition: color 0.2s;"
                                                        onmouseover="this.style.color='#b91c1c'"
                                                        onmouseout="this.style.color='#dc2626'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- 선택된 메뉴의 상세 편집 -->
                        <div id="menu-details" class="border-t pt-6">
                            @foreach($menuItems as $index => $item)
                                <div class="menu-detail-panel {{ $loop->first ? '' : 'hidden' }}" data-menu-id="{{ $item['id'] }}">
                                    <!-- 메뉴 타입 선택 (최상단) -->
                                    <div style="margin-bottom: 16px;">
                                        <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 12px;">메뉴 타입</h4>
                                        <div style="display: flex; gap: 16px;">
                                            <label style="display: flex; align-items: center; padding: 8px 16px; background-color: {{ ($item['type'] ?? 'single') == 'single' ? '#fef3c7' : '#ffffff' }}; border: 2px solid {{ ($item['type'] ?? 'single') == 'single' ? '#f59e0b' : '#e5e7eb' }}; border-radius: 8px; cursor: pointer; transition: all 0.2s;"
                                                   onmouseover="if(!this.querySelector('input').checked) { this.style.borderColor='#fbbf24'; this.style.backgroundColor='#fffbeb'; }"
                                                   onmouseout="if(!this.querySelector('input').checked) { this.style.borderColor='#e5e7eb'; this.style.backgroundColor='#ffffff'; }">
                                                <input type="radio" 
                                                       name="menu_type_{{ $item['id'] }}" 
                                                       value="single"
                                                       {{ ($item['type'] ?? 'single') == 'single' ? 'checked' : '' }}
                                                       wire:change="updateMenuItem('{{ $item['id'] }}', 'type', 'single')"
                                                       onclick="return true"
                                                       style="margin-right: 8px; accent-color: #f59e0b;">
                                                <span style="font-size: 0.875rem; color: {{ ($item['type'] ?? 'single') == 'single' ? '#92400e' : '#374151' }};">일반 메뉴</span>
                                            </label>
                                            <label style="display: flex; align-items: center; padding: 8px 16px; background-color: {{ ($item['type'] ?? 'single') == 'dropdown' ? '#fef3c7' : '#ffffff' }}; border: 2px solid {{ ($item['type'] ?? 'single') == 'dropdown' ? '#f59e0b' : '#e5e7eb' }}; border-radius: 8px; cursor: pointer; transition: all 0.2s;"
                                                   onmouseover="if(!this.querySelector('input').checked) { this.style.borderColor='#fbbf24'; this.style.backgroundColor='#fffbeb'; }"
                                                   onmouseout="if(!this.querySelector('input').checked) { this.style.borderColor='#e5e7eb'; this.style.backgroundColor='#ffffff'; }">
                                                <input type="radio" 
                                                       name="menu_type_{{ $item['id'] }}" 
                                                       value="dropdown"
                                                       {{ ($item['type'] ?? 'single') == 'dropdown' ? 'checked' : '' }}
                                                       wire:change="updateMenuItem('{{ $item['id'] }}', 'type', 'dropdown')"
                                                       onclick="return true"
                                                       style="margin-right: 8px; accent-color: #f59e0b;">
                                                <span style="font-size: 0.875rem; color: {{ ($item['type'] ?? 'single') == 'dropdown' ? '#92400e' : '#374151' }};">드롭다운 메뉴</span>
                                            </label>
                                            <label style="display: flex; align-items: center; padding: 8px 16px; background-color: {{ ($item['type'] ?? 'single') == 'mega' ? '#fef3c7' : '#ffffff' }}; border: 2px solid {{ ($item['type'] ?? 'single') == 'mega' ? '#f59e0b' : '#e5e7eb' }}; border-radius: 8px; cursor: pointer; transition: all 0.2s;"
                                                   onmouseover="if(!this.querySelector('input').checked) { this.style.borderColor='#fbbf24'; this.style.backgroundColor='#fffbeb'; }"
                                                   onmouseout="if(!this.querySelector('input').checked) { this.style.borderColor='#e5e7eb'; this.style.backgroundColor='#ffffff'; }">
                                                <input type="radio" 
                                                       name="menu_type_{{ $item['id'] }}" 
                                                       value="mega"
                                                       {{ ($item['type'] ?? 'single') == 'mega' ? 'checked' : '' }}
                                                       wire:change="updateMenuItem('{{ $item['id'] }}', 'type', 'mega')"
                                                       onclick="return true"
                                                       style="margin-right: 8px; accent-color: #f59e0b;">
                                                <span style="font-size: 0.875rem; color: {{ ($item['type'] ?? 'single') == 'mega' ? '#92400e' : '#374151' }};">메가 메뉴</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- 메뉴 설정 -->
                                    <div class="mb-4">
                                        <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 12px;">메뉴 이름 (다국어)</h4>
                                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-3 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">🇰🇷 한국어</label>
                                                <input type="text" 
                                                       value="{{ $item['label'] ?? '' }}"
                                                       wire:change="updateMenuItem('{{ $item['id'] }}', 'label', $event.target.value)"
                                                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                       placeholder="예: 회사소개" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">🇬🇧 English</label>
                                                <input type="text" 
                                                       value="{{ $item['label_eng'] ?? '' }}"
                                                       wire:change="updateMenuItem('{{ $item['id'] }}', 'label_eng', $event.target.value)"
                                                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                       placeholder="ex: About Us">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">🇨🇳 中文</label>
                                                <input type="text" 
                                                       value="{{ $item['label_chn'] ?? '' }}"
                                                       wire:change="updateMenuItem('{{ $item['id'] }}', 'label_chn', $event.target.value)"
                                                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                       placeholder="例: 关于我们">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">🇮🇳 हिन्दी</label>
                                                <input type="text" 
                                                       value="{{ $item['label_hin'] ?? '' }}"
                                                       wire:change="updateMenuItem('{{ $item['id'] }}', 'label_hin', $event.target.value)"
                                                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                       placeholder="उदा: हमारे बारे में">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">🇸🇦 العربية</label>
                                                <input type="text" 
                                                       value="{{ $item['label_arb'] ?? '' }}"
                                                       wire:change="updateMenuItem('{{ $item['id'] }}', 'label_arb', $event.target.value)"
                                                       style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                       placeholder="مثال: معلومات عنا">
                                            </div>
                                        </div>
                                        
                                        <div class="url-field-container" data-menu-id="{{ $item['id'] }}" style="{{ ($item['type'] ?? 'single') == 'single' ? '' : 'display: none;' }}">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">링크 URL</label>
                                            <input type="text" 
                                                   value="{{ $item['url'] ?? '' }}"
                                                   wire:change="updateMenuItem('{{ $item['id'] }}', 'url', $event.target.value)"
                                                   style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                                                   onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                                                   onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                   placeholder="메뉴 클릭 시 이동할 URL">
                                        </div>
                                    </div>
                                    
                                    <!-- 타입별 하위 메뉴 편집 -->
                                    @if(($item['type'] ?? 'single') == 'single')
                                        <!-- 일반 메뉴: 하위 메뉴 없음 -->
                                        <div class="bg-gray-50 rounded-lg p-4 text-center text-sm text-gray-600">
                                            일반 메뉴는 하위 메뉴가 없습니다.
                                        </div>
                                    @elseif(($item['type'] ?? 'single') == 'dropdown')
                                        <!-- 드롭다운 메뉴 -->
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-md font-semibold">하위 메뉴</h4>
                                                <button wire:click="addSubMenuItem('{{ $item['id'] }}')" 
                                                        style="color: #f59e0b; font-weight: 500; font-size: 0.875rem; transition: color 0.2s;"
                                                        onmouseover="this.style.color='#d97706'"
                                                        onmouseout="this.style.color='#f59e0b'">
                                                    + 하위 메뉴 추가
                                                </button>
                                            </div>
                                            <div class="sub-menu-list space-y-2" data-parent-id="{{ $item['id'] }}">
                                                @if(!empty($item['children']))
                                                    @foreach($item['children'] as $child)
                                                        @include('filament.resources.header-menu-resource.partials.submenu-item', ['item' => $child, 'parentId' => $item['id']])
                                                    @endforeach
                                                @else
                                                    <p class="text-sm text-gray-500 text-center py-4 bg-gray-50 rounded-lg">하위 메뉴가 없습니다.</p>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif(($item['type'] ?? 'single') == 'mega')
                                        <!-- 메가 메뉴 -->
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-md font-semibold">메가 메뉴 그룹</h4>
                                                <button wire:click="addMegaMenuGroup('{{ $item['id'] }}')" 
                                                        style="color: #f59e0b; font-weight: 500; font-size: 0.875rem; transition: color 0.2s;"
                                                        onmouseover="this.style.color='#d97706'"
                                                        onmouseout="this.style.color='#f59e0b'">
                                                    + 그룹 추가
                                                </button>
                                            </div>
                                            <div class="space-y-4">
                                                @if(!empty($item['groups']))
                                                    @foreach($item['groups'] as $groupIndex => $group)
                                                        <div style="background-color: #f9fafb; border-radius: 8px; padding: 16px; border: 1px solid #e5e7eb;">
                                                            <!-- 그룹 이름 다국어 설정 -->
                                                            <div style="margin-bottom: 12px;">
                                                                <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 8px; color: #374151;">그룹명 (다국어)</h5>
                                                                <div class="grid grid-cols-1 md:grid-cols-5 gap-2 mb-2">
                                                                    <div class="relative">
                                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                            <span class="text-lg">🇰🇷</span>
                                                                        </div>
                                                                        <input type="text" 
                                                                               value="{{ $group['group_label'] ?? $group['label'] ?? '' }}"
                                                                               wire:blur="updateMegaMenuGroup('{{ $item['id'] }}', {{ $groupIndex }}, 'group_label', $event.target.value)"
                                                                               style="font-weight: 600; font-size: 0.875rem; padding: 8px 12px 8px 40px; border: 1px solid #d1d5db; border-radius: 4px; background-color: white; width: 100%;"
                                                                               onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                               placeholder="그룹 이름">
                                                                    </div>
                                                                    <div class="relative">
                                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                            <span class="text-lg">🇬🇧</span>
                                                                        </div>
                                                                        <input type="text" 
                                                                               value="{{ $group['group_label_eng'] ?? '' }}"
                                                                               wire:blur="updateMegaMenuGroup('{{ $item['id'] }}', {{ $groupIndex }}, 'group_label_eng', $event.target.value)"
                                                                               style="font-weight: 600; font-size: 0.875rem; padding: 8px 12px 8px 40px; border: 1px solid #d1d5db; border-radius: 4px; background-color: white; width: 100%;"
                                                                               onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                               placeholder="Group Name">
                                                                    </div>
                                                                    <div class="relative">
                                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                            <span class="text-lg">🇨🇳</span>
                                                                        </div>
                                                                        <input type="text" 
                                                                               value="{{ $group['group_label_chn'] ?? '' }}"
                                                                               wire:blur="updateMegaMenuGroup('{{ $item['id'] }}', {{ $groupIndex }}, 'group_label_chn', $event.target.value)"
                                                                               style="font-weight: 600; font-size: 0.875rem; padding: 8px 12px 8px 40px; border: 1px solid #d1d5db; border-radius: 4px; background-color: white; width: 100%;"
                                                                               onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                               placeholder="组名">
                                                                    </div>
                                                                    <div class="relative">
                                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                            <span class="text-lg">🇮🇳</span>
                                                                        </div>
                                                                        <input type="text" 
                                                                               value="{{ $group['group_label_hin'] ?? '' }}"
                                                                               wire:blur="updateMegaMenuGroup('{{ $item['id'] }}', {{ $groupIndex }}, 'group_label_hin', $event.target.value)"
                                                                               style="font-weight: 600; font-size: 0.875rem; padding: 8px 12px 8px 40px; border: 1px solid #d1d5db; border-radius: 4px; background-color: white; width: 100%;"
                                                                               onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                               placeholder="समूह नाम">
                                                                    </div>
                                                                    <div class="relative">
                                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                            <span class="text-lg">🇸🇦</span>
                                                                        </div>
                                                                        <input type="text" 
                                                                               value="{{ $group['group_label_arb'] ?? '' }}"
                                                                               wire:blur="updateMegaMenuGroup('{{ $item['id'] }}', {{ $groupIndex }}, 'group_label_arb', $event.target.value)"
                                                                               style="font-weight: 600; font-size: 0.875rem; padding: 8px 12px 8px 40px; border: 1px solid #d1d5db; border-radius: 4px; background-color: white; width: 100%;"
                                                                               onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                               onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                               placeholder="اسم المجموعة">
                                                                    </div>
                                                                </div>
                                                                <div style="display: flex; justify-content: flex-end;">
                                                                    <button wire:click="deleteMegaMenuGroup('{{ $item['id'] }}', {{ $groupIndex }})"
                                                                            wire:confirm="이 그룹을 삭제하시겠습니까?"
                                                                            style="color: #dc2626; transition: color 0.2s;"
                                                                            onmouseover="this.style.color='#b91c1c'"
                                                                            onmouseout="this.style.color='#dc2626'">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                                                @foreach($group['items'] as $itemIndex => $groupItem)
                                                                    <div style="background-color: white; border-radius: 4px; padding: 12px; border: 1px solid #e5e7eb;">
                                                                        <h6 style="font-size: 0.75rem; font-weight: 600; margin-bottom: 8px; color: #374151;">메뉴 아이템 (다국어)</h6>
                                                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 mb-2">
                                                                            <div class="relative">
                                                                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                                                    <span class="text-sm">🇰🇷</span>
                                                                                </div>
                                                                                <input type="text" 
                                                                                       value="{{ $groupItem['label'] ?? '' }}"
                                                                                       wire:change="updateMegaMenuItem('{{ $item['id'] }}', {{ $groupIndex }}, {{ $itemIndex }}, 'label', $event.target.value)"
                                                                                       style="font-size: 0.75rem; padding: 4px 8px 4px 32px; border: 1px solid #d1d5db; border-radius: 4px; width: 100%;"
                                                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                                       placeholder="한국어">
                                                                            </div>
                                                                            <div class="relative">
                                                                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                                                    <span class="text-sm">🇬🇧</span>
                                                                                </div>
                                                                                <input type="text" 
                                                                                       value="{{ $groupItem['label_eng'] ?? '' }}"
                                                                                       wire:change="updateMegaMenuItem('{{ $item['id'] }}', {{ $groupIndex }}, {{ $itemIndex }}, 'label_eng', $event.target.value)"
                                                                                       style="font-size: 0.75rem; padding: 4px 8px 4px 32px; border: 1px solid #d1d5db; border-radius: 4px; width: 100%;"
                                                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                                       placeholder="English">
                                                                            </div>
                                                                            <div class="relative">
                                                                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                                                    <span class="text-sm">🇨🇳</span>
                                                                                </div>
                                                                                <input type="text" 
                                                                                       value="{{ $groupItem['label_chn'] ?? '' }}"
                                                                                       wire:change="updateMegaMenuItem('{{ $item['id'] }}', {{ $groupIndex }}, {{ $itemIndex }}, 'label_chn', $event.target.value)"
                                                                                       style="font-size: 0.75rem; padding: 4px 8px 4px 32px; border: 1px solid #d1d5db; border-radius: 4px; width: 100%;"
                                                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                                       placeholder="中文">
                                                                            </div>
                                                                            <div class="relative">
                                                                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                                                    <span class="text-sm">🇮🇳</span>
                                                                                </div>
                                                                                <input type="text" 
                                                                                       value="{{ $groupItem['label_hin'] ?? '' }}"
                                                                                       wire:change="updateMegaMenuItem('{{ $item['id'] }}', {{ $groupIndex }}, {{ $itemIndex }}, 'label_hin', $event.target.value)"
                                                                                       style="font-size: 0.75rem; padding: 4px 8px 4px 32px; border: 1px solid #d1d5db; border-radius: 4px; width: 100%;"
                                                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                                       placeholder="हिन्दी">
                                                                            </div>
                                                                            <div class="relative">
                                                                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                                                    <span class="text-sm">🇸🇦</span>
                                                                                </div>
                                                                                <input type="text" 
                                                                                       value="{{ $groupItem['label_arb'] ?? '' }}"
                                                                                       wire:change="updateMegaMenuItem('{{ $item['id'] }}', {{ $groupIndex }}, {{ $itemIndex }}, 'label_arb', $event.target.value)"
                                                                                       style="font-size: 0.75rem; padding: 4px 8px 4px 32px; border: 1px solid #d1d5db; border-radius: 4px; width: 100%;"
                                                                                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                                       placeholder="العربية">
                                                                            </div>
                                                                        </div>
                                                                        <div style="display: flex; align-items: center; gap: 8px;">
                                                                            <input type="text" 
                                                                                   value="{{ $groupItem['url'] ?? '' }}"
                                                                                   wire:change="updateMegaMenuItem('{{ $item['id'] }}', {{ $groupIndex }}, {{ $itemIndex }}, 'url', $event.target.value)"
                                                                                   style="font-size: 0.875rem; padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 4px; flex: 1;"
                                                                                   onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 2px rgba(245, 158, 11, 0.1)';"
                                                                                   onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                                                                                   placeholder="URL">
                                                                            <button wire:click="deleteMegaMenuItem('{{ $item['id'] }}', {{ $groupIndex }}, {{ $itemIndex }})"
                                                                                    style="color: #dc2626; transition: color 0.2s;"
                                                                                    onmouseover="this.style.color='#b91c1c'"
                                                                                    onmouseout="this.style.color='#dc2626'">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <button wire:click="addMegaMenuItem('{{ $item['id'] }}', {{ $groupIndex }})" 
                                                                        style="font-size: 0.75rem; color: #f59e0b; transition: color 0.2s;"
                                                                        onmouseover="this.style.color='#d97706'"
                                                                        onmouseout="this.style.color='#f59e0b'">
                                                                    + 메뉴 추가
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-sm text-gray-500 text-center py-4 bg-gray-50 rounded-lg">메가 메뉴 그룹이 없습니다.</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
        
    </div>
    
    <style>
        .menu-list {
            min-height: 50px;
        }
        
        .menu-item {
            background: white;
            border: 1px solid rgb(229, 231, 235);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            cursor: move;
            transition: all 0.2s;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .menu-item:hover {
            border-color: rgb(147, 197, 253);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .menu-item.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }
        
        .menu-item.drag-over {
            border-color: rgb(59, 130, 246);
            background: rgb(239, 246, 255);
        }
        
        .sub-menu-list {
            margin-top: 1rem;
            margin-left: 2rem;
            padding-left: 1rem;
            border-left: 2px solid rgb(229, 231, 235);
            min-height: 40px;
        }
        
        .sub-menu-item {
            background: rgb(249, 250, 251);
            border: 1px solid rgb(229, 231, 235);
            margin-bottom: 0.5rem;
        }
        
        .menu-handle {
            cursor: grab;
            color: rgb(156, 163, 175);
            transition: color 0.2s;
        }
        
        .menu-handle:hover {
            color: rgb(107, 114, 128);
        }
        
        .menu-handle:active {
            cursor: grabbing;
        }
        
        .sortable-ghost {
            opacity: 0.3;
        }
        
        .sortable-chosen {
            background: rgb(219, 234, 254) !important;
            border-color: rgb(96, 165, 250) !important;
        }
        
        /* 헤더 박스 드래그 관련 스타일 추가 */
        .menu-header-box.sortable-ghost {
            opacity: 0.4;
        }
        
        .menu-header-box.sortable-chosen > div {
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgb(203, 213, 225);
            transition: .3s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: rgb(34, 197, 94);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
        
        .preview-menu {
            transition: all 0.3s ease;
        }
    </style>
    
    <script>
        // 현재 선택된 메뉴 ID 저장
        window.currentActiveMenuId = null;
        
        // 타입 변경 확인 함수
        function confirmTypeChange(input, currentType, newType, message) {
            // 같은 타입이면 확인 불필요
            if (currentType === newType) {
                return true;
            }
            
            // 데이터가 있을 때만 확인
            const hasData = false; // 추후 데이터 존재 여부 체크 로직 추가 가능
            
            // single에서 다른 타입으로 변경 시에는 확인 불필요 (데이터가 없으므로)
            if (currentType === 'single') {
                return true;
            }
            
            // 확인 메시지 표시
            if (!confirm(message + '\n\n(데이터는 숨겨지며, 다시 돌아올 때 복원됩니다)')) {
                // 취소 시 이전 값으로 되돌리기
                setTimeout(() => {
                    const prevInput = document.querySelector(`input[name="${input.name}"][value="${currentType}"]`);
                    if (prevInput) {
                        prevInput.checked = true;
                        updateMenuTypeStyles();
                    }
                }, 10);
                return false;
            }
            
            return true;
        }
        
        // 전역으로 노출
        window.confirmTypeChange = confirmTypeChange;
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeSortable();
            initializeMenuHeaderBoxes();
            updateMenuTypeStyles();
            updateUrlFieldVisibility();
            
            // 첫 번째 메뉴 자동 선택
            const firstMenuBox = document.querySelector('.menu-header-box');
            if (firstMenuBox && !window.currentActiveMenuId) {
                firstMenuBox.click();
            }
        });
        
        document.addEventListener('livewire:navigated', function() {
            initializeSortable();
            initializeMenuHeaderBoxes();
        });
        
        window.addEventListener('menuUpdated', function(event) {
            const activeMenuId = event.detail?.activeMenuId || window.currentActiveMenuId;
            
            setTimeout(() => {
                initializeSortable();
                initializeMenuHeaderBoxes();
                updateMenuTypeStyles();
                updateUrlFieldVisibility();
                
                // 이전에 선택된 메뉴 복원
                if (activeMenuId) {
                    const menuBox = document.querySelector(`.menu-header-box[data-menu-id="${activeMenuId}"]`);
                    if (menuBox) {
                        menuBox.click();
                    }
                }
            }, 100);
        });
        
        function updateUrlFieldVisibility() {
            // 모든 URL 필드의 표시/숨김 상태 업데이트
            document.querySelectorAll('.url-field-container').forEach(container => {
                const menuId = container.dataset.menuId;
                const checkedRadio = document.querySelector(`input[type="radio"][name="menu_type_${menuId}"]:checked`);
                if (checkedRadio) {
                    if (checkedRadio.value === 'single') {
                        container.style.display = '';
                    } else {
                        container.style.display = 'none';
                    }
                }
            });
        }
        
        function updateMenuTypeStyles() {
            // 모든 메뉴 타입 라디오 버튼 업데이트
            document.querySelectorAll('input[type="radio"][name^="menu_type_"]').forEach(radio => {
                const label = radio.parentElement;
                const span = label.querySelector('span');
                
                if (radio.checked) {
                    label.style.backgroundColor = '#fef3c7';
                    label.style.borderColor = '#f59e0b';
                    if (span) span.style.color = '#92400e';
                } else {
                    label.style.backgroundColor = '#ffffff';
                    label.style.borderColor = '#e5e7eb';
                    if (span) span.style.color = '#374151';
                }
                
                // 라디오 버튼 변경 이벤트 리스너
                radio.addEventListener('change', function() {
                    // 같은 그룹의 모든 라디오 버튼 스타일 업데이트
                    document.querySelectorAll(`input[name="${this.name}"]`).forEach(r => {
                        const l = r.parentElement;
                        const s = l.querySelector('span');
                        if (r.checked) {
                            l.style.backgroundColor = '#fef3c7';
                            l.style.borderColor = '#f59e0b';
                            if (s) s.style.color = '#92400e';
                        } else {
                            l.style.backgroundColor = '#ffffff';
                            l.style.borderColor = '#e5e7eb';
                            if (s) s.style.color = '#374151';
                        }
                    });
                    
                    // URL 필드 표시/숨김
                    if (this.checked) {
                        const menuId = this.name.replace('menu_type_', '');
                        const urlField = document.querySelector(`.url-field-container[data-menu-id="${menuId}"]`);
                        if (urlField) {
                            if (this.value === 'single') {
                                urlField.style.display = '';
                            } else {
                                urlField.style.display = 'none';
                            }
                        }
                    }
                });
            });
        }
        
        function initializeMenuHeaderBoxes() {
            // 헤더 박스 클릭 이벤트
            document.querySelectorAll('.menu-header-box').forEach(box => {
                box.addEventListener('click', function(e) {
                    // 드래그 핸들이나 삭제 버튼 클릭 시 무시
                    if (e.target.closest('.menu-handle') || e.target.closest('button')) {
                        return;
                    }
                    
                    // 모든 박스에서 active 클래스 제거 및 스타일 초기화
                    document.querySelectorAll('.menu-header-box > div').forEach(b => {
                        b.classList.remove('active-menu');
                        b.style.backgroundColor = '#f3f4f6';
                        b.style.borderColor = 'transparent';
                        const span = b.querySelector('span');
                        if (span) span.style.color = '#374151';
                    });
                    
                    // 클릭한 박스에 active 클래스 추가 및 스타일 적용
                    const activeBox = this.querySelector('div');
                    activeBox.classList.add('active-menu');
                    activeBox.style.backgroundColor = '#fef3c7';
                    activeBox.style.borderColor = '#f59e0b';
                    const activeSpan = activeBox.querySelector('span');
                    if (activeSpan) activeSpan.style.color = '#92400e';
                    
                    // 모든 상세 패널 숨기기
                    document.querySelectorAll('.menu-detail-panel').forEach(panel => {
                        panel.classList.add('hidden');
                    });
                    
                    // 해당 메뉴의 상세 패널 표시
                    const menuId = this.dataset.menuId;
                    window.currentActiveMenuId = menuId; // 현재 선택된 메뉴 ID 저장
                    const detailPanel = document.querySelector(`.menu-detail-panel[data-menu-id="${menuId}"]`);
                    if (detailPanel) {
                        detailPanel.classList.remove('hidden');
                    }
                });
            });
        }
        
        function initializeSortable() {
            // 메인 메뉴 헤더 박스 Sortable
            const mainMenuList = document.getElementById('main-menu-list');
            if (mainMenuList && typeof Sortable !== 'undefined') {
                // 기존 인스턴스 제거
                if (mainMenuList._sortable) {
                    mainMenuList._sortable.destroy();
                }
                
                mainMenuList._sortable = new Sortable(mainMenuList, {
                    animation: 150,
                    handle: '.menu-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'dragging',
                    forceFallback: true,
                    onEnd: function (evt) {
                        updateMenuOrder();
                    }
                });
            }
            
            // 서브메뉴 Sortable
            document.querySelectorAll('.sub-menu-list').forEach(subList => {
                if (subList && typeof Sortable !== 'undefined') {
                    // 기존 인스턴스 제거
                    if (subList._sortable) {
                        subList._sortable.destroy();
                    }
                    
                    subList._sortable = new Sortable(subList, {
                        animation: 150,
                        handle: '.submenu-handle',
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'dragging',
                        forceFallback: true,
                        onEnd: function (evt) {
                            updateMenuOrder();
                        }
                    });
                }
            });
        }
        
        function updateMenuOrder() {
            const menuStructure = getMenuStructure();
            Livewire.dispatch('menu-items-sorted', { sortedIds: menuStructure });
        }
        
        function getMenuStructure() {
            const mainList = document.getElementById('main-menu-list');
            if (!mainList) return [];
            
            const structure = [];
            mainList.querySelectorAll('.menu-header-box').forEach(box => {
                const menuId = box.dataset.menuId;
                const menuData = {
                    id: menuId,
                    children: []
                };
                
                // 해당 메뉴의 서브메뉴 찾기
                const subList = document.querySelector(`.sub-menu-list[data-parent-id="${menuId}"]`);
                if (subList) {
                    subList.querySelectorAll('.submenu-item').forEach(subItem => {
                        menuData.children.push({
                            id: subItem.dataset.menuId
                        });
                    });
                }
                
                structure.push(menuData);
            });
            
            return structure;
        }
    </script>
</x-filament-panels::page>