<div class="submenu-item bg-gray-50 rounded-lg p-3 border border-gray-200" data-menu-id="{{ $item['id'] }}">
    <div class="flex items-center gap-3">
        <div class="submenu-handle cursor-move">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
            </svg>
        </div>
        
        <div class="flex-1 space-y-3">
            <!-- 다국어 메뉴 이름 -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-lg">🇰🇷</span>
                    </div>
                    <input type="text" 
                           value="{{ $item['label'] ?? '' }}"
                           wire:change="updateMenuItem('{{ $item['id'] }}', 'label', $event.target.value)"
                           style="width: 100%; padding: 6px 12px 6px 40px; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                           onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                           placeholder="한국어">
                </div>
                
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-lg">🇬🇧</span>
                    </div>
                    <input type="text" 
                           value="{{ $item['label_eng'] ?? '' }}"
                           wire:change="updateMenuItem('{{ $item['id'] }}', 'label_eng', $event.target.value)"
                           style="width: 100%; padding: 6px 12px 6px 40px; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                           onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                           placeholder="English">
                </div>
                
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-lg">🇨🇳</span>
                    </div>
                    <input type="text" 
                           value="{{ $item['label_chn'] ?? '' }}"
                           wire:change="updateMenuItem('{{ $item['id'] }}', 'label_chn', $event.target.value)"
                           style="width: 100%; padding: 6px 12px 6px 40px; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                           onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                           placeholder="中文">
                </div>
                
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-lg">🇮🇳</span>
                    </div>
                    <input type="text" 
                           value="{{ $item['label_hin'] ?? '' }}"
                           wire:change="updateMenuItem('{{ $item['id'] }}', 'label_hin', $event.target.value)"
                           style="width: 100%; padding: 6px 12px 6px 40px; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                           onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                           placeholder="हिन्दी">
                </div>
                
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-lg">🇸🇦</span>
                    </div>
                    <input type="text" 
                           value="{{ $item['label_arb'] ?? '' }}"
                           wire:change="updateMenuItem('{{ $item['id'] }}', 'label_arb', $event.target.value)"
                           style="width: 100%; padding: 6px 12px 6px 40px; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                           onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                           onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                           placeholder="العربية">
                </div>
            </div>
            
            <div class="flex gap-2">
                <select wire:change="handlePageSelect('{{ $item['id'] }}', $event.target.value)"
                        style="flex: 1; padding: 6px 12px; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 8px; outline: none; background-color: white;"
                        onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                        onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';">
                    <option value="">페이지 선택...</option>
                    <optgroup label="맞춤형 페이지">
                        @foreach(\App\Models\Post::where('type', 'page')->where('is_published', true)->get() as $page)
                            <option value="/page/{{ $page->id }}">{{ $page->title }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="게시물 목록">
                        <option value="/news">뉴스</option>
                        <option value="/blog">블로그</option>
                        <option value="/routine">루틴</option>
                        <option value="/featured">특징</option>
                        <option value="/products">상품</option>
                        <option value="/foods">식품</option>
                        <option value="/services">서비스</option>
                        <option value="/promotions">홍보</option>
                        <option value="/papers">논문</option>
                        <option value="/videos">영상 미디어</option>
                        <option value="/qna">Q&A</option>
                    </optgroup>
                    <optgroup label="기타 페이지">
                        <option value="/surveys">설문조사</option>
                        <option value="/partners">파트너사</option>
                        <option value="/recovery-dashboard">회복 점수 대시보드</option>
                    </optgroup>
                </select>
                
                <input type="text" 
                       value="{{ $item['url'] ?? '' }}"
                       wire:change="updateMenuItem('{{ $item['id'] }}', 'url', $event.target.value)"
                       style="flex: 1; padding: 6px 12px; font-size: 0.875rem; border: 1px solid #d1d5db; border-radius: 8px; outline: none;"
                       onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)';"
                       onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none';"
                       placeholder="직접 URL 입력">
            </div>
        </div>
        
        <button wire:click="deleteMenuItem('{{ $item['id'] }}')"
                wire:confirm="정말로 이 메뉴를 삭제하시겠습니까?"
                style="padding: 6px; color: #dc2626; border-radius: 8px; transition: all 0.2s;"
                onmouseover="this.style.backgroundColor='#fee2e2'; this.style.color='#b91c1c';"
                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#dc2626';">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    </div>
</div>