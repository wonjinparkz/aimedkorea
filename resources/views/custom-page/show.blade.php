<x-app-layout>
    @push('meta')
        <meta name="description" content="{{ $page->summary }}">
        <meta property="og:title" content="{{ $page->title }}">
        <meta property="og:description" content="{{ $page->summary }}">
        @if($page->image)
            <meta property="og:image" content="{{ Storage::url($page->image) }}">
        @endif
    @endpush

    <div class="min-h-screen bg-white">
        <!-- Hero Section with Banner -->
        <div class="relative {{ $page->image ? '' : 'bg-gradient-to-r from-blue-900 via-blue-800 to-blue-600' }} overflow-hidden">
            @if($page->image)
                <!-- Banner Image -->
                <div class="absolute inset-0">
                    <img src="{{ Storage::url($page->image) }}" alt="{{ $page->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                </div>
            @else
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-size: 60px 60px;"></div>
                </div>
            @endif
            
            <!-- Content -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white text-center">
                    {{ $page->title }}
                </h1>
                @if($page->summary)
                    <p class="mt-6 text-xl md:text-2xl text-white text-center opacity-90 max-w-3xl mx-auto">
                        {{ $page->summary }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Page Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-8 sm:px-8 sm:py-10">
                    <div class="prose prose-lg max-w-none">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .prose h2 {
            @apply text-2xl font-bold mt-8 mb-4 text-gray-900;
        }
        .prose h3 {
            @apply text-xl font-semibold mt-6 mb-3 text-gray-800;
        }
        .prose p {
            @apply mb-4 text-gray-700 leading-relaxed;
        }
        .prose ul {
            @apply list-disc list-inside mb-4 space-y-2;
        }
        .prose ol {
            @apply list-decimal list-inside mb-4 space-y-2;
        }
        .prose li {
            @apply text-gray-700;
        }
        .prose a {
            @apply text-blue-600 hover:text-blue-800 underline;
        }
        .prose blockquote {
            @apply border-l-4 border-gray-300 pl-4 italic text-gray-600 my-4;
        }
        .prose table {
            @apply w-full border-collapse my-4;
        }
        .prose th {
            @apply border border-gray-300 px-4 py-2 bg-gray-100 text-left font-semibold;
        }
        .prose td {
            @apply border border-gray-300 px-4 py-2;
        }
        .prose img {
            @apply rounded-lg shadow-md my-6 mx-auto;
        }
        .prose pre {
            @apply bg-gray-100 rounded-lg p-4 overflow-x-auto my-4;
        }
        .prose code {
            @apply bg-gray-100 rounded px-1 py-0.5 text-sm;
        }
    </style>
    @endpush
</x-app-layout>