<x-app-layout>
    @push('scripts')
    <script>
        // GTM Preview Mode Helper for Main Page
        (function() {
            // Force GTM initialization
            window.dataLayer = window.dataLayer || [];
            
            // Check for GTM preview parameters
            const urlParams = new URLSearchParams(window.location.search);
            const gtmPreview = urlParams.get('gtm_preview');
            const gtmCookies = urlParams.get('gtm_cookies_win');
            
            if (gtmPreview || gtmCookies) {
                console.log('GTM Preview Mode Detected');
                // Set preview cookie
                document.cookie = 'gtm_preview=' + (gtmPreview || 'GTM-N8GJF2QW') + '; path=/; secure; samesite=none';
                document.cookie = 'gtm_debug=x; path=/; secure; samesite=none';
            }
            
            // Push initial event
            window.dataLayer.push({
                'event': 'gtm.init',
                'gtm.uniqueEventId': Date.now(),
                'page_type': 'homepage',
                'page_language': '{{ app()->getLocale() }}'
            });
            
            // Debug GTM status
            window.addEventListener('load', function() {
                if (typeof google_tag_manager !== 'undefined') {
                    console.log('✅ GTM Loaded on Homepage');
                    console.log('Container: GTM-N8GJF2QW');
                    if (google_tag_manager['GTM-N8GJF2QW']) {
                        console.log('✅ Container Active');
                    }
                } else {
                    console.log('❌ GTM Not Loaded');
                }
            });
        })();
    </script>
    @endpush
    
        <div class="relative min-h-screen">
            <!-- Hero Slider Section -->
            <x-hero-slider :heroes="$heroes" />

            <!-- Featured Post Section -->
            @if($featuredPost)
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <div class="text-center mb-12">
                        <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-4">{{ __('the_science_of_care') }}</h1>
                        <p class="text-xl lg:text-2xl text-gray-600">{{ __('ai_recovery_philosophy') }}</p>
                    </div>
                    <x-horizontal-post-card :post="$featuredPost" />
                </div>
            @endif

            <!-- Routines Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('routines') }}</h2>
                    <p class="mt-2 text-gray-600">{{ __('routines_description') }}</p>
                </div>
                
                @if($routinePosts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($routinePosts as $post)
                            <x-post-card :post="$post" />
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-100 rounded-lg">
                        <p class="text-gray-500">{{ __('no_routines_yet') }}</p>
                    </div>
                @endif
            </div>

            <!-- Blog Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('blogs') }}</h2>
                    <p class="mt-2 text-gray-600">{{ __('latest_blog_posts') }}</p>
                </div>
                
                @if($blogPosts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($blogPosts as $post)
                            <x-post-card :post="$post" />
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-100 rounded-lg">
                        <p class="text-gray-500">{{ __('no_blogs_yet') }}</p>
                    </div>
                @endif
            </div>

            <!-- Research Areas Section -->
            <div class="bg-white py-16">
                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-bold text-gray-900">{{ __('research_areas') }}</h2>
                        <p class="mt-4 text-lg text-gray-600">{{ __('research_areas_subtitle') }}</p>
                    </div>
                    
                    @if($tabPosts->count() > 0)
                        @php
                            $colors = [
                                '#5B8DEF', // Brain - Bright Blue
                                '#9F7AEA', // Visual - Purple
                                '#ED64A6', // Auditory - Pink
                                '#E53E3E', // Dermal - Rose/Red
                                '#DD6B20', // Musculoskeletal - Orange
                                '#38B2AC', // Digestive - Teal
                                '#F56565', // Circulatory - Red
                            ];
                        @endphp
                        
                        {{-- 모바일: 스크롤 가능한 레이아웃 --}}
                        <div class="xl:hidden overflow-x-auto pb-4 -mx-4 px-4">
                            <div class="inline-flex gap-4 min-w-max">
                                @foreach($tabPosts as $index => $post)
                                    @php
                                        $backgroundColor = $colors[$index % count($colors)];
                                    @endphp
                                    <div class="w-48 h-64 flex-shrink-0">
                                        <x-tab-card :post="$post" :backgroundColor="$backgroundColor" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        {{-- 데스크톱: 7열 그리드 --}}
                        <div class="hidden xl:grid xl:grid-cols-7 gap-4">
                            @foreach($tabPosts as $index => $post)
                                @php
                                    $backgroundColor = $colors[$index % count($colors)];
                                @endphp
                                <div class="h-80">
                                    <x-tab-card :post="$post" :backgroundColor="$backgroundColor" />
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-white rounded-lg">
                            <p class="text-gray-500">{{ __('no_research_areas_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>


        </div>
</x-app-layout>
