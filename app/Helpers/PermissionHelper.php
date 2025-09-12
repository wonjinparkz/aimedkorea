<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * 안전한 권한 확인 - null 사용자 처리
     */
    public static function hasPermission(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        
        return $user->hasPermission($permission);
    }
    
    /**
     * 권한 확인 및 실패 시 로그 기록 (Filament 리소스용)
     */
    public static function checkPermissionWithLog(string $permission, string $action = 'access'): bool
    {
        $hasPermission = self::hasPermission($permission);
        
        if (!$hasPermission) {
            self::logAccessAttempt($permission, $action, false);
        }
        
        return $hasPermission;
    }
    
    /**
     * 안전한 모듈 권한 확인 - null 사용자 처리
     */
    public static function hasModulePermission(string $module, string $action = null): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        
        return $user->hasModulePermission($module, $action);
    }
    
    /**
     * 안전한 역할 확인 - null 사용자 처리
     */
    public static function hasRole(string $role): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        
        return $user->hasRole($role);
    }
    
    /**
     * 관리자 여부 확인
     */
    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }
    
    /**
     * 권한 체크 후 403 에러 발생 (로그 포함)
     */
    public static function requirePermission(string $permission): void
    {
        if (!self::hasPermission($permission)) {
            self::logAccessDenied($permission, 'permission');
            abort(403, '이 작업에 대한 권한이 없습니다.');
        }
    }
    
    /**
     * URL 직접 접근을 위한 route guard (로그 포함)
     */
    public static function requireRouteAccess(string $permission, string $routeName = null): void
    {
        if (!self::hasPermission($permission)) {
            self::logRouteGuardBlock($permission, $routeName ?? request()->route()?->getName());
            abort(403, '이 페이지에 접근할 권한이 없습니다.');
        }
    }
    
    /**
     * 모듈 권한 체크 후 403 에러 발생 (로그 포함)
     */
    public static function requireModulePermission(string $module, string $action = null): void
    {
        if (!self::hasModulePermission($module, $action)) {
            $permissionString = $action ? "{$module}-{$action}" : $module;
            self::logAccessDenied($permissionString, 'module_permission');
            abort(403, '이 기능에 대한 권한이 없습니다.');
        }
    }
    
    /**
     * 접근 거부 로그 기록
     */
    private static function logAccessDenied(string $permission, string $type): void
    {
        $user = Auth::user();
        $request = request();
        
        $logData = [
            'timestamp' => now()->toDateTimeString(),
            'user_id' => $user ? $user->id : null,
            'username' => $user ? $user->username : 'anonymous',
            'email' => $user ? $user->email : 'anonymous',
            'user_roles' => $user ? $user->roles->pluck('slug')->toArray() : [],
            'requested_permission' => $permission,
            'permission_type' => $type,
            'route_name' => $request ? $request->route()?->getName() : 'unknown',
            'route_uri' => $request ? $request->getPathInfo() : 'unknown',
            'method' => $request ? $request->getMethod() : 'unknown',
            'ip_address' => $request ? $request->ip() : 'unknown',
            'user_agent' => $request ? $request->header('User-Agent') : 'unknown',
        ];
        
        \Log::warning('Access denied - Permission check failed', $logData);
        
        // 별도의 보안 로그 파일에도 기록
        \Log::channel('security')->warning('PERMISSION_DENIED', $logData);
    }
    
    /**
     * Route Guard 차단 로그 기록
     */
    private static function logRouteGuardBlock(string $permission, string $routeName): void
    {
        $user = Auth::user();
        $request = request();
        
        $logData = [
            'event_type' => 'route_guard_block',
            'timestamp' => now()->toDateTimeString(),
            'user_id' => $user ? $user->id : null,
            'username' => $user ? $user->username : 'anonymous',
            'email' => $user ? $user->email : 'anonymous',
            'user_roles' => $user ? $user->roles->pluck('slug')->toArray() : [],
            'required_permission' => $permission,
            'blocked_route' => $routeName,
            'route_uri' => $request ? $request->getPathInfo() : 'unknown',
            'method' => $request ? $request->getMethod() : 'unknown',
            'ip_address' => $request ? $request->ip() : 'unknown',
            'user_agent' => $request ? $request->header('User-Agent') : 'unknown',
            'referer' => $request ? $request->header('Referer') : 'direct_access',
        ];
        
        \Log::warning('Route access blocked by RBAC guard', $logData);
        
        // 보안 로그 채널에 route_guard_block 이벤트 기록
        \Log::channel('security')->warning('ROUTE_GUARD_BLOCK', $logData);
    }
    
    /**
     * 접근 시도 로그 기록 (성공/실패 모두)
     */
    private static function logAccessAttempt(string $permission, string $action, bool $success): void
    {
        $user = Auth::user();
        $request = request();
        
        $logData = [
            'timestamp' => now()->toDateTimeString(),
            'user_id' => $user ? $user->id : null,
            'username' => $user ? $user->username : 'anonymous',
            'email' => $user ? $user->email : 'anonymous',
            'user_roles' => $user ? $user->roles->pluck('slug')->toArray() : [],
            'requested_permission' => $permission,
            'action' => $action,
            'success' => $success,
            'route_name' => $request ? $request->route()?->getName() : 'unknown',
            'route_uri' => $request ? $request->getPathInfo() : 'unknown',
            'method' => $request ? $request->getMethod() : 'unknown',
            'ip_address' => $request ? $request->ip() : 'unknown',
            'user_agent' => $request ? $request->header('User-Agent') : 'unknown',
        ];
        
        if ($success) {
            \Log::channel('security')->info('ACCESS_GRANTED', $logData);
        } else {
            \Log::channel('security')->warning('ACCESS_DENIED', $logData);
        }
    }
}