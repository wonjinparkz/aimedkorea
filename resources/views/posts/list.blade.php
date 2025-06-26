<x-app-layout>
    {{-- 헤더 섹션 --}}
    <div class="relative h-64 bg-gradient-to-r from-blue-900 to-blue-700 flex items-center justify-center overflow-hidden">
        {{-- 배경 패턴 --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
        </div>
        
        {{-- 중앙 컨텐츠 --}}
        <div class="relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ $title }}</h1>
            <div class="w-24 h-1 bg-white mx-auto"></div>
        </div>
    </div>

    {{-- 게시물 리스트 섹션 --}}
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- 게시물이 있을 때 --}}
            @if($posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>

                {{-- 페이지네이션 --}}
                <div class="mt-12">
                    {{ $posts->links('pagination::tailwind') }}
                </div>
            @else
                {{-- 게시물이 없을 때 --}}
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">게시물이 없습니다</h3>
                    <p class="text-gray-500">아직 등록된 {{ $title }} 게시물이 없습니다.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
