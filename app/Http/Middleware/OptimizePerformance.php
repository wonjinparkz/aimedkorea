<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizePerformance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply to HTML responses
        if ($response instanceof \Illuminate\Http\Response && 
            str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            
            // Add performance headers
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            
            // Cache control for static assets
            if ($request->is('css/*', 'js/*', 'images/*', 'fonts/*')) {
                $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            } else {
                // Dynamic content caching
                $response->headers->set('Cache-Control', 'public, max-age=3600, s-maxage=3600');
            }
            
            // Note: Content-Encoding should only be set if content is actually compressed
            // Remove this as it causes encoding errors when content is not gzipped
            // $response->headers->set('Content-Encoding', 'gzip');
            
            // Preload critical resources
            $preloads = [];
            
            // Preload critical CSS
            if ($request->is('/')) {
                $preloads[] = '</build/assets/app.*.css>; rel=preload; as=style';
                $preloads[] = '</build/assets/app.*.js>; rel=preload; as=script';
            }
            
            if (!empty($preloads)) {
                $response->headers->set('Link', implode(', ', $preloads));
            }
        }

        return $response;
    }
}