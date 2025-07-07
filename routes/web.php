<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Hero;
use Illuminate\Http\Request;

Route::get('/', function () {
    // Hero 슬라이드
    $heroes = Hero::active()->with('buttonPost')->get();
    
    // 특징 게시물 - featured 타입의 가장 최신 게시물 1개
    $featuredPost = Post::where('type', 'featured')
        ->latest()
        ->first();
    
    $routinePosts = Post::where('type', 'routine')
        ->latest()
        ->take(3)
        ->get();
    
    $blogPosts = Post::where('type', 'blog')
        ->latest()
        ->take(3)
        ->get();
    
    $tabPosts = Post::where('type', 'tab')
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('welcome', compact('heroes', 'featuredPost', 'routinePosts', 'blogPosts', 'tabPosts'));
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
    Route::get('/recovery-dashboard/history', [App\Http\Controllers\RecoveryDashboardController::class, 'history'])->name('recovery.history');
    Route::match(['get', 'post'], '/recovery-dashboard/compare', [App\Http\Controllers\RecoveryDashboardController::class, 'compare'])->name('recovery.compare');
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
    $posts = Post::where('type', 'news')
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => 'News',
        'type' => 'news'
    ]);
})->name('posts.news.index');

Route::get('/blog', function () {
    $posts = Post::where('type', 'blog')
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => 'Blog',
        'type' => 'blog'
    ]);
})->name('posts.blog.index');

Route::get('/routine', function () {
    $posts = Post::where('type', 'routine')
        ->latest()
        ->paginate(9);
    
    return view('posts.list', [
        'posts' => $posts,
        'title' => 'Routines',
        'type' => 'routine'
    ]);
})->name('posts.routine.index');

Route::get('/featured', function () {
    $posts = Post::where('type', 'featured')
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
    $posts = Post::where('type', 'product')
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
    $posts = Post::where('type', 'food')
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
    $posts = Post::where('type', 'service')
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
    $posts = Post::where('type', 'promotion')
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
    $posts = Post::where('type', 'video')
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
