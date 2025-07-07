<div class="qna-post-article p-6 border rounded-lg hover:shadow-md transition-shadow duration-200 w-full">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
            </div>
        </div>
        
        <div class="flex-1">
            {{-- 자주 묻는 질문 뱃지 --}}
            @if($getRecord()->is_featured)
                <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-amber-700 bg-amber-100 dark:bg-amber-900 dark:text-amber-300 rounded-full mb-2">
                    자주 묻는 질문
                </span>
            @endif
            
            {{-- 질문 제목 --}}
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                Q. {{ $getRecord()->title }}
            </h3>
            
            {{-- 답변 요약 또는 본문 일부 --}}
            <div class="text-gray-600 dark:text-gray-400 mb-3">
                <span class="font-medium text-primary-600 dark:text-primary-400">A.</span>
                @if($getRecord()->summary)
                    {{ $getRecord()->summary }}
                @else
                    {!! Str::limit(strip_tags($getRecord()->content), 150) !!}
                @endif
            </div>
            
            {{-- 관련 이미지 (있을 경우) --}}
            @if($getRecord()->image)
                <div class="mb-3">
                    <img src="{{ Storage::url($getRecord()->image) }}" 
                         alt="{{ $getRecord()->title }}" 
                         class="h-32 w-auto rounded-md object-cover">
                </div>
            @endif
            
            {{-- 메타 정보 및 액션 버튼 --}}
            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    {{-- 게시 상태 --}}
                    @if($getRecord()->is_published)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            게시됨
                        </span>
                    @else
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            임시저장
                        </span>
                    @endif
                    
                    {{-- 게시일 --}}
                    <span>
                        {{ $getRecord()->published_at ? $getRecord()->published_at->format('Y.m.d') : $getRecord()->created_at->format('Y.m.d') }}
                    </span>
                </div>
                
                {{-- 액션 버튼들 --}}
                <div class="flex items-center gap-2">
                    <a href="{{ \App\Filament\Resources\QnaPostResource::getUrl('edit', ['record' => $getRecord()]) }}" 
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        편집
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .qna-post-article {
        background-color: rgba(var(--gray-50), var(--tw-bg-opacity));
        width: 100% !important;
        max-width: none !important;
    }
    
    .dark .qna-post-article {
        background-color: rgba(var(--gray-900), var(--tw-bg-opacity));
    }
    
    .qna-post-article:hover {
        border-color: rgba(var(--primary-500), 0.3);
    }
    
    /* Filament 테이블 컬럼 전체 너비 사용 */
    .fi-ta-col-wrp {
        width: 100% !important;
    }
    
    .fi-ta-text {
        width: 100% !important;
    }
</style>