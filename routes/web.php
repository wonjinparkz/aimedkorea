<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Hero;

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
