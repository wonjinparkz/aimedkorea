<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $records = $this->getFilteredSortedTableQuery()->paginate($this->getTableRecordsPerPage());
        @endphp
        
        @forelse ($records as $record)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                {{-- 이미지 --}}
                @if($record->image)
                    <div class="aspect-video w-full overflow-hidden">
                        <img src="{{ Storage::url($record->image) }}" 
                             alt="{{ $record->title }}" 
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="aspect-video w-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                
                <div class="p-6">
                    {{-- 제목 --}}
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                        {{ $record->title }}
                    </h3>
                    
                    {{-- 요약 --}}
                    @if($record->summary)
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ $record->summary }}
                        </p>
                    @else
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ Str::limit(strip_tags($record->content), 150) }}
                        </p>
                    @endif
                    
                    {{-- 더보기 문구와 액션 --}}
                    <div class="flex items-center justify-between">
                        <span class="text-blue-600 text-sm font-medium">
                            {{ $record->read_more_text ?? '자세히 보기' }}
                        </span>
                        
                        {{-- 액션 버튼들 --}}
                        <div class="flex items-center gap-2">
                            <a href="{{ $this->getResource()::getUrl('edit', ['record' => $record]) }}" 
                               class="text-gray-500 hover:text-gray-700 transition-colors"
                               title="수정">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            
                            <button wire:click="mountTableAction('delete', '{{ $record->getKey() }}')" 
                                    wire:loading.attr="disabled"
                                    class="text-red-500 hover:text-red-700 transition-colors"
                                    title="삭제">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">게시물이 없습니다</h3>
                    <p class="mt-1 text-sm text-gray-500">새로운 게시물을 작성해보세요.</p>
                    <div class="mt-6">
                        <a href="{{ $this->getResource()::getUrl('create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <div class="mt-6">
        {{ $records->links() }}
    </div>
</x-filament-panels::page>
