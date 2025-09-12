<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // 관리자는 모든 권한을 가짐
        if ($user->isAdmin()) {
            return $next($request);
        }

        // 특정 권한이 요구되는 경우
        if ($permission && !$user->hasPermission($permission)) {
            abort(403, '이 작업에 대한 권한이 없습니다.');
        }

        return $next($request);
    }
}
