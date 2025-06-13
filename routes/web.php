<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;

Route::get('/', function () {
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
    
    return view('welcome', compact('routinePosts', 'blogPosts', 'tabPosts'));
});

Route::get('/posts/{post}', function (Post $post) {
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
