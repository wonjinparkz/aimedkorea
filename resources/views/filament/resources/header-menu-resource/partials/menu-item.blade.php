<li class="menu-item {{ $level > 0 ? 'sub-menu-item' : '' }}" data-menu-id="{{ $item['id'] }}">
    <div class="flex items-center gap-3">
        <!-- 드래그 핸들 -->
        <div class="menu-handle">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
            </svg>
        </div>
        
        <!-- 메뉴 정보 -->
        <div class="flex-1 space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">메뉴 이름</label>
                    <input type="text" 
                           value="{{ $item['label'] }}"
                           wire:change="updateMenuItem('{{ $item['id'] }}', 'label', $event.target.value)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-colors"
                           placeholder="메뉴 이름">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">링크 주소</label>
                    <input type="text" 
                           value="{{ $item['url'] ?? '' }}"
                           wire:change="updateMenuItem('{{ $item['id'] }}', 'url', $event.target.value)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500 focus:outline-none transition-colors"
                           placeholder="/page-url">
                </div>
            </div>
            
            <!-- 하위 메뉴 -->
            @if($level === 0)
                <div class="sub-menu-container">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-600">하위 메뉴</span>
                        <button wire:click="addSubMenuItem('{{ $item['id'] }}')" 
                                class="text-sm text-primary-600 hover:text-primary-700 font-medium transition-colors">
                            + 하위 메뉴 추가
                        </button>
                    </div>
                    <ul class="sub-menu-list">
                        @if(!empty($item['children']))
                            @foreach($item['children'] as $child)
                                @include('filament.resources.header-menu-resource.partials.menu-item', ['item' => $child, 'level' => 1])
                            @endforeach
                        @endif
                    </ul>
                </div>
            @endif
        </div>
        
        <!-- 컨트롤 버튼 -->
        <div class="flex items-center gap-2">
            <!-- 활성화 토글 -->
            <label class="toggle-switch">
                <input type="checkbox" 
                       {{ ($item['active'] ?? true) ? 'checked' : '' }}
                       wire:change="updateMenuItem('{{ $item['id'] }}', 'active', $event.target.checked)">
                <span class="toggle-slider"></span>
            </label>
            
            <!-- 삭제 버튼 -->
            <button wire:click="deleteMenuItem('{{ $item['id'] }}')"
                    wire:confirm="정말로 이 메뉴를 삭제하시겠습니까?"
                    class="p-2 text-danger-600 hover:bg-danger-50 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    </div>
</li>