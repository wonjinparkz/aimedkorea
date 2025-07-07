<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class QnaController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('type', Post::TYPE_QNA)
                    ->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());

        // 검색 기능
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        // 자주 묻는 질문 필터
        if ($request->has('featured') && $request->featured == '1') {
            $query->where('is_featured', true);
        }

        // 정렬
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('published_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            default: // latest
                $query->orderBy('published_at', 'desc');
                break;
        }

        $qnas = $query->paginate(10)->withQueryString();
        
        // 자주 묻는 질문 상위 5개
        $featuredQnas = Post::where('type', Post::TYPE_QNA)
                           ->where('is_published', true)
                           ->where('is_featured', true)
                           ->whereNotNull('published_at')
                           ->where('published_at', '<=', now())
                           ->orderBy('published_at', 'desc')
                           ->limit(5)
                           ->get();

        return view('qna.index', compact('qnas', 'featuredQnas'));
    }

    public function show($id)
    {
        $qna = Post::where('type', Post::TYPE_QNA)
                   ->where('is_published', true)
                   ->whereNotNull('published_at')
                   ->where('published_at', '<=', now())
                   ->findOrFail($id);

        // 관련 Q&A (같은 카테고리나 유사한 내용)
        $relatedQnas = Post::where('type', Post::TYPE_QNA)
                          ->where('is_published', true)
                          ->whereNotNull('published_at')
                          ->where('published_at', '<=', now())
                          ->where('id', '!=', $qna->id)
                          ->inRandomOrder()
                          ->limit(5)
                          ->get();

        return view('qna.show', compact('qna', 'relatedQnas'));
    }
}