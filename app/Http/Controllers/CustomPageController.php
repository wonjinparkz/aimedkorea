<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class CustomPageController extends Controller
{
    public function show($id)
    {
        $page = Post::where('type', 'page')
            ->where('id', $id)
            ->where('is_published', true)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->firstOrFail();
        
        return view('custom-page.show', compact('page'));
    }
}