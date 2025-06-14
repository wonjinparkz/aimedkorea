<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Hero;

Route::get('/', function () {
    // Hero 슬라이드
    $heroes = Hero::active()->get();
    
    // 특징 게시물 - featured 타입의 가장 최신 게시물 1개
    $featuredPost = Post::where('type', 'featured')
        ->where('is_published', true)
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
        ->where('is_published', true)
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('welcome', compact('heroes', 'featuredPost', 'routinePosts', 'blogPosts', 'tabPosts'));
});

Route::get('/posts/{post}', function (Post $post) {
    return view('posts.show', compact('post'));
})->name('posts.show');

// Hero 프리뷰 라우트
Route::get('/admin/hero-preview', function () {
    return view('filament.hero-preview');
})->name('filament.hero-preview');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
