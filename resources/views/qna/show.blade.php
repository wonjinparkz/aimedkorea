<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        {{-- 뒤로가기 및 헤더 --}}
        <div class="bg-white shadow-sm">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <a href="{{ route('qna.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Q&A 목록으로 돌아가기
                </a>
            </div>
        </div>

        {{-- 메인 콘텐츠 --}}
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <article class="bg-white rounded-lg shadow-sm overflow-hidden">
                {{-- Q&A 헤더 --}}
                <div class="p-8 border-b">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-blue-600">Q</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            @if($qna->is_featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800 mb-3">
                                자주 묻는 질문
                            </span>
                            @endif
                            <h1 class="text-2xl font-bold text-gray-900">{{ $qna->title }}</h1>
                            <div class="mt-3 flex items-center text-sm text-gray-500">
                                <time datetime="{{ $qna->published_at->toIso8601String() }}">
                                    {{ $qna->published_at->format('Y년 m월 d일') }} 게시
                                </time>
                                @if($qna->updated_at > $qna->published_at)
                                <span class="mx-2">·</span>
                                <time datetime="{{ $qna->updated_at->toIso8601String() }}">
                                    {{ $qna->updated_at->format('Y년 m월 d일') }} 수정
                                </time>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 답변 내용 --}}
                <div class="p-8">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-green-600">A</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            {{-- 이미지 (있을 경우) --}}
                            @if($qna->image)
                            <div class="mb-6">
                                <img src="{{ Storage::url($qna->image) }}" 
                                     alt="{{ $qna->title }}" 
                                     class="rounded-lg shadow-md max-w-full h-auto">
                            </div>
                            @endif

                            {{-- 답변 내용 --}}
                            <div class="prose prose-lg max-w-none text-gray-700">
                                {!! $qna->content !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 공유 버튼 --}}
                <div class="px-8 pb-8">
                    <div class="pt-6 border-t">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-900">이 답변이 도움이 되셨나요?</h3>
                            <div class="flex items-center gap-4">
                                <button type="button" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9 9 0 10-13.432 0m13.432 0A9 9 0 0112 21a9 9 0 01-5.432-2.316m13.432 0L21 21"></path>
                                    </svg>
                                    공유하기
                                </button>
                                <button type="button" onclick="window.print()" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    인쇄하기
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- 관련 Q&A --}}
            @if($relatedQnas->count() > 0)
            <div class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6">관련 Q&A</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($relatedQnas as $related)
                    <a href="{{ route('qna.show', $related->id) }}" 
                       class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">Q</span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                @if($related->is_featured)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 mb-2">
                                    자주 묻는 질문
                                </span>
                                @endif
                                <h3 class="text-base font-semibold text-gray-900 line-clamp-2">{{ $related->title }}</h3>
                                <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                    {{ $related->summary ?: strip_tags(Str::limit($related->content, 100)) }}
                                </p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>