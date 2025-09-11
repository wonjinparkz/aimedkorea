<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Map session language codes to Laravel locale codes
     */
    private $localeMap = [
        'kor' => 'ko',
        'eng' => 'en',
        'chn' => 'zh',
        'hin' => 'hi',
        'arb' => 'ar',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the language from session
        $sessionLang = session('locale', 'kor');
        
        // Map to Laravel locale code
        $locale = $this->localeMap[$sessionLang] ?? 'ko';
        
        // Set the application locale
        app()->setLocale($locale);
        
        return $next($request);
    }
}
