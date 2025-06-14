<x-filament-panels::page>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
        @php
            $records = $this->getFilteredSortedTableQuery()->paginate($this->getTableRecordsPerPage());
        @endphp
        
        @forelse ($records as $record)
            <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden; transition: box-shadow 0.3s;" 
                 onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" 
                 onmouseout="this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.1)'">
                {{-- 이미지 --}}
                @if($record->image)
                    <div style="aspect-ratio: 4/3; width: 100%; overflow: hidden;">
                        <img src="{{ Storage::url($record->image) }}" 
                             alt="{{ $record->title }}" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @else
                    <div style="aspect-ratio: 4/3; width: 100%; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                
                <div style="padding: 1rem;">
                    {{-- 제목 --}}
                    <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $record->title }}
                    </h3>
                    
                    {{-- 요약 --}}
                    @if($record->summary)
                        <p style="color: #6b7280; font-size: 0.75rem; margin-bottom: 0.75rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $record->summary }}
                        </p>
                    @else
                        <p style="color: #6b7280; font-size: 0.75rem; margin-bottom: 0.75rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ Str::limit(strip_tags($record->content), 100) }}
                        </p>
                    @endif
                    
                    {{-- 더보기 문구 --}}
                    <div style="margin-bottom: 0.75rem;">
                        <span style="color: #2563eb; font-size: 0.75rem; font-weight: 500;">
                            {{ $record->read_more_text ?? '자세히 보기' }}
                        </span>
                    </div>
                    
                    {{-- 구분선 --}}
                    <hr style="border-color: #e5e7eb; margin-bottom: 0.75rem;">
                    
                    {{-- 액션 버튼들 --}}
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ $this->getResource()::getUrl('edit', ['record' => $record]) }}" 
                           style="flex: 1; background-color: #3b82f6; color: white; text-align: center; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; text-decoration: none; transition: background-color 0.2s;"
                           onmouseover="this.style.backgroundColor='#2563eb'" 
                           onmouseout="this.style.backgroundColor='#3b82f6'">
                            수정
                        </a>
                        
                        <button wire:click="mountTableAction('delete', '{{ $record->getKey() }}')" 
                                wire:loading.attr="disabled"
                                style="flex: 1; background-color: #ef4444; color: white; text-align: center; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; border: none; cursor: pointer; transition: background-color 0.2s;"
                                onmouseover="this.style.backgroundColor='#dc2626'" 
                                onmouseout="this.style.backgroundColor='#ef4444'">
                            삭제
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: span 3;">
                <div style="text-align: center; padding: 3rem 0;">
                    <svg style="margin: 0 auto; height: 3rem; width: 3rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 style="margin-top: 0.5rem; font-size: 0.875rem; font-weight: 500; color: #111827;">게시물이 없습니다</h3>
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280;">새로운 게시물을 작성해보세요.</p>
                    <div style="margin-top: 1.5rem;">
                        <a href="{{ $this->getResource()::getUrl('create') }}" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; border: 1px solid transparent; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; color: white; background-color: #4f46e5; text-decoration: none; transition: background-color 0.2s;"
                           onmouseover="this.style.backgroundColor='#4338ca'" 
                           onmouseout="this.style.backgroundColor='#4f46e5'">
                            <svg style="margin-left: -0.25rem; margin-right: 0.5rem; height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            새 게시물 작성
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    
    {{-- 페이지네이션 --}}
    <div style="margin-top: 1.5rem;">
        {{ $records->links() }}
    </div>
</x-filament-panels::page>
