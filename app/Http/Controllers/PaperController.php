<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PaperController extends Controller
{
    /**
     * Display a listing of papers.
     */
    public function index(Request $request)
    {
        $query = Post::where('type', Post::TYPE_PAPER)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc');

        // Get papers for the list
        $papers = $query->paginate(12);

        // Get unique publishers for filters
        $publishers = Post::where('type', Post::TYPE_PAPER)
            ->where('is_published', true)
            ->whereNotNull('publisher')
            ->distinct()
            ->pluck('publisher');

        // Get years for filters
        $years = Post::where('type', Post::TYPE_PAPER)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->selectRaw('YEAR(published_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('papers.index', compact('papers', 'publishers', 'years'));
    }

    /**
     * Display the specified paper.
     */
    public function show($slug)
    {
        $paper = Post::where('type', Post::TYPE_PAPER)
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('papers.show', compact('paper'));
    }
}