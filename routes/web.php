<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Models\Post;
use App\Models\Hero;
use Illuminate\Http\Request;
use App\Http\Controllers\SearchLogController;

Route::get('/', function (Request $request) {
    // 현재 언어 가져오기
    $currentLang = session('locale', 'kor');
    
    // Cache homepage data for 30 minutes to improve performance
    $cacheKey = 'homepage_data_' . $currentLang;
    $data = Cache::remember($cacheKey, 1800, function () use ($currentLang) {
        // Optimize Hero query - only select needed columns
        $heroes = Hero::active()
            ->select('id', 'title', 'title_translations', 
                    'subtitle', 'subtitle_translations',
                    'description', 'description_translations',
                    'button_text', 'button_text_translations', 
                    'button_url', 'button_post_id', 
                    'background_image', 'background_video', 'background_type',
                    'hero_settings', 'order', 'is_active')
            ->with(['buttonPost' => function($query) {
                $query->select('id', 'title', 'slug', 'type');
            }])
            ->get();
        
        // Optimize Post queries - select only necessary columns
        $featuredPost = Post::where('type', 'featured')
            ->select('id', 'title', 'slug', 'summary', 'image', 'type', 'language', 'created_at')
            ->inLanguage($currentLang)
            ->latest()
            ->first();
        
        $routinePosts = Post::where('type', 'routine')
            ->select('id', 'title', 'slug', 'summary', 'image', 'type', 'language', 'created_at')
            ->inLanguage($currentLang)
            ->latest()
            ->take(3)
            ->get();
        
        $blogPosts = Post::where('type', 'blog')
            ->select('id', 'title', 'slug', 'summary', 'image', 'type', 'language', 'created_at')
            ->inLanguage($currentLang)
            ->latest()
            ->take(3)
            ->get();
        
        $tabPosts = Post::where('type', 'tab')
            ->select('id', 'title', 'slug', 'summary', 'image', 'type', 'language', 'created_at')
            ->inLanguage($currentLang)
            ->orderBy('created_at', 'desc')
            ->limit(10)  // Limit tab posts to prevent loading too many
            ->get();
        
        return compact('heroes', 'featuredPost', 'routinePosts', 'blogPosts', 'tabPosts');
    });
    
    // 모바일 감지 (User Agent 직접 확인)
    $userAgent = $request->header('User-Agent');
    $isMobile = false;
    
    if ($userAgent) {
        $mobilePatterns = '/Mobile|Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i';
        $tabletPatterns = '/iPad|Android.*Chrome.*Mobile|Android.*(?!Mobile)/i';
        
        $isMobile = preg_match($mobilePatterns, $userAgent) && !preg_match($tabletPatterns, $userAgent);
    }
    
    // 강제 모바일 뷰 파라미터 체크 (테스트용)
    if ($request->has('mobile')) {
        $isMobile = $request->get('mobile') === 'true';
    }
    
    // 모바일 기기인 경우 모바일 전용 뷰 반환
    if ($isMobile) {
        return view('welcome-mobile', $data);
    }
    
    return view('welcome', $data);
});

// Hero 프리뷰 라우트
Route::get('/admin/hero-preview', function () {
    return view('filament.hero-preview');
})->name('filament.hero-preview');

// 설문 관련 라우트
Route::get('/surveys', [App\Http\Controllers\SurveyController::class, 'index'])->name('surveys.index');
Route::get('/surveys/{survey}', [App\Http\Controllers\SurveyController::class, 'show'])->name('surveys.show');
Route::post('/surveys/{survey}/responses', [App\Http\Controllers\SurveyController::class, 'store'])->name('surveys.store');
Route::get('/surveys/{survey}/results/{response}', [App\Http\Controllers\SurveyController::class, 'results'])->name('surveys.results');

// 회복 점수 대시보드 라우트
Route::middleware(['auth'])->group(function () {
    Route::get('/recovery-dashboard', [App\Http\Controllers\RecoveryDashboardController::class, 'index'])->name('recovery.dashboard');
    Route::get('/recovery-dashboard/check', [App\Http\Controllers\RecoveryDashboardController::class, 'check'])->name('recovery.check');
    Route::post('/recovery-dashboard/timeline/create', [App\Http\Controllers\RecoveryDashboardController::class, 'createTimeline'])->name('recovery.timeline.create');
    Route::patch('/recovery-dashboard/timeline/{timeline}/status', [App\Http\Controllers\RecoveryDashboardController::class, 'updateTimelineStatus'])->name('recovery.timeline.status');
    Route::get('/recovery-dashboard/history', [App\Http\Controllers\RecoveryDashboardController::class, 'history'])->name('recovery.history');
    Route::match(['get', 'post'], '/recovery-dashboard/compare', [App\Http\Controllers\RecoveryDashboardController::class, 'compare'])->name('recovery.compare');
    Route::post('/recovery/checkpoint/{checkpoint}/complete', [App\Http\Controllers\RecoveryDashboardController::class, 'completeCheckpoint'])->name('recovery.checkpoint.complete');
});

// 게시물 상세 페이지
Route::get('/posts/{type}/{post}', function ($type, Post $post) {
    // 탭 타입인 경우 별도의 뷰 파일 사용
    if ($post->type === 'tab') {
        return view('posts.show-tab', compact('post'));
    }
    
    // 그 외의 경우 기본 뷰 파일 사용
    return view('posts.show', compact('post'));
})->name('posts.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// 명시적 라우트 추가 (웹서버 설정 문제 해결)
Route::get('/news', function () {
    $currentLang = session('locale', 'kor');
    $posts = Post::where('type', 'news')
        ->inLanguage($currentLang)
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => 'News',
        'type' => 'news'
    ]);
})->name('posts.news.index');

Route::get('/blog', function () {
    $currentLang = session('locale', 'kor');
    $posts = Post::where('type', 'blog')
        ->inLanguage($currentLang)
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => 'Blog',
        'type' => 'blog'
    ]);
})->name('posts.blog.index');

Route::get('/routine', function () {
    $currentLang = session('locale', 'kor');
    $posts = Post::where('type', 'routine')
        ->inLanguage($currentLang)
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => 'Routines',
        'type' => 'routine'
    ]);
})->name('posts.routine.index');

Route::get('/featured', function () {
    $currentLang = session('locale', 'kor');
    $posts = Post::where('type', 'featured')
        ->inLanguage($currentLang)
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => 'Featured',
        'type' => 'featured'
    ]);
})->name('posts.featured.index');

// 상품 페이지
Route::get('/products', function () {
    $currentLang = session('locale', 'kor');
    $posts = Post::where('type', 'product')
        ->inLanguage($currentLang)
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => '상품',
        'type' => 'product'
    ]);
})->name('posts.product.index');

// 식품 페이지
Route::get('/foods', function () {
    $currentLang = session('locale', 'kor');
    $posts = Post::where('type', 'food')
        ->inLanguage($currentLang)
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => '식품',
        'type' => 'food'
    ]);
})->name('posts.food.index');

// 서비스 페이지
Route::get('/services', function () {
    $currentLang = session('locale', 'kor');
    $posts = Post::where('type', 'service')
        ->inLanguage($currentLang)
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => '서비스',
        'type' => 'service'
    ]);
})->name('posts.service.index');

// 홍보 페이지
Route::get('/promotions', function () {
    $currentLang = session('locale', 'kor');
    $posts = Post::where('type', 'promotion')
        ->inLanguage($currentLang)
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => '홍보',
        'type' => 'promotion'
    ]);
})->name('posts.promotion.index');

// 파트너사 페이지
Route::get('/partners', [App\Http\Controllers\PartnersController::class, 'index'])->name('partners.index');

// 논문 페이지
Route::get('/papers', [App\Http\Controllers\PaperController::class, 'index'])->name('papers.index');
Route::get('/papers/{slug}', [App\Http\Controllers\PaperController::class, 'show'])->name('papers.show');

// 맞춤형 페이지
Route::get('/page/{id}', [App\Http\Controllers\CustomPageController::class, 'show'])->name('custom-page.show');

// Q&A 페이지
Route::get('/qna', [App\Http\Controllers\QnaController::class, 'index'])->name('qna.index');
Route::get('/qna/{id}', [App\Http\Controllers\QnaController::class, 'show'])->name('qna.show');

// 영상 미디어 페이지
Route::get('/videos', function () {
    $currentLang = session('locale', 'kor');
    $posts = Post::where('type', 'video')
        ->inLanguage($currentLang)
        ->where('is_published', true)
        ->whereNotNull('published_at')
        ->where('published_at', '<=', now())
        ->latest('published_at')
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => '영상 미디어',
        'type' => 'video'
    ]);
})->name('posts.video.index');

// Debug route for menu data
Route::get('/debug-menu', function () {
    return view('debug-menu');
});

// Search API routes
Route::post('/api/log-search-event', [SearchLogController::class, 'logSearchEvent'])->name('api.log-search-event');
Route::get('/api/global-search', [App\Http\Controllers\Api\GlobalSearchController::class, 'search'])->name('api.global-search');

// Language change route
Route::post('/change-language', function (Request $request) {
    $language = $request->input('language', 'kor');
    
    // Validate language
    if (!in_array($language, ['kor', 'eng', 'chn', 'hin', 'arb'])) {
        $language = 'kor';
    }
    
    // Store in session
    session(['locale' => $language]);
    
    return response()->json(['success' => true]);
});

// PWA Offline page route
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// PWA 설치 페이지
Route::get('/install', function () {
    return view('pwa.install');
})->name('pwa.install');

// Terms and Policy pages
Route::get('/terms', function () {
    return view('terms');
})->name('terms.show');

Route::get('/policy', function () {
    return view('policy');
})->name('policy.show');

// TEST ROUTE: Hero translations debug
Route::get('/test-hero-translations', function () {
    $hero = Hero::first();
    
    if (!$hero) {
        return 'No Hero records found in database.';
    }
    
    $languages = ['kor', 'eng', 'chn', 'hin', 'arb'];
    $output = '<h1>Hero Translation Test</h1>';
    $output .= '<h2>Hero ID: ' . $hero->id . '</h2>';
    
    // Show raw data
    $output .= '<h3>Raw Database Values:</h3>';
    $output .= '<pre>';
    $output .= 'title: ' . htmlspecialchars($hero->title) . "\n";
    $output .= 'subtitle: ' . htmlspecialchars($hero->subtitle) . "\n";
    $output .= 'description: ' . htmlspecialchars($hero->description) . "\n";
    $output .= 'button_text: ' . htmlspecialchars($hero->button_text) . "\n\n";
    
    $output .= 'title_translations: ' . json_encode($hero->title_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    $output .= 'subtitle_translations: ' . json_encode($hero->subtitle_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    $output .= 'description_translations: ' . json_encode($hero->description_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    $output .= 'button_text_translations: ' . json_encode($hero->button_text_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    $output .= '</pre>';
    
    // Test getTitle() method for each language
    $output .= '<h3>Title by Language (using getTitle() method):</h3>';
    $output .= '<table border="1" cellpadding="10">';
    $output .= '<tr><th>Language</th><th>Title</th><th>Subtitle</th><th>Description</th><th>Button Text</th></tr>';
    
    foreach ($languages as $lang) {
        $output .= '<tr>';
        $output .= '<td>' . $lang . '</td>';
        $output .= '<td>' . htmlspecialchars($hero->getTitle($lang)) . '</td>';
        $output .= '<td>' . htmlspecialchars($hero->getSubtitle($lang)) . '</td>';
        $output .= '<td>' . htmlspecialchars($hero->getDescription($lang)) . '</td>';
        $output .= '<td>' . htmlspecialchars($hero->getButtonText($lang)) . '</td>';
        $output .= '</tr>';
    }
    
    $output .= '</table>';
    
    // Show current session locale
    $output .= '<h3>Current Session Info:</h3>';
    $output .= '<p>Session locale: ' . session('locale', 'not set') . '</p>';
    $output .= '<p>App locale: ' . app()->getLocale() . '</p>';
    
    // Show available languages for this hero
    $output .= '<h3>Available Languages for this Hero:</h3>';
    $output .= '<p>' . implode(', ', $hero->getAvailableLanguages()->toArray()) . '</p>';
    
    // Test hasTranslation method
    $output .= '<h3>Has Translation Check:</h3>';
    $output .= '<ul>';
    foreach ($languages as $lang) {
        $output .= '<li>' . $lang . ': ' . ($hero->hasTranslation($lang) ? 'YES' : 'NO') . '</li>';
    }
    $output .= '</ul>';
    
    return $output;
});
