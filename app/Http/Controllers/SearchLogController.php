<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchLogController extends Controller
{
    /**
     * Log search events for analytics
     */
    public function logSearchEvent(Request $request)
    {
        $data = $request->validate([
            'timestamp' => 'required|string',
            'event' => 'required|string',
            'query' => 'nullable|string',
            'results_count' => 'nullable|integer',
            'shortcut' => 'nullable|string',
            'source' => 'nullable|string',
            'userAgent' => 'nullable|string',
            'platform' => 'nullable|string',
            'url' => 'nullable|string',
        ]);

        // 로그 파일에 기록
        Log::channel('search')->info('Search Event', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'ip' => $request->ip(),
            'session_id' => session()->getId(),
            ...$data
        ]);

        return response()->json(['success' => true]);
    }
}