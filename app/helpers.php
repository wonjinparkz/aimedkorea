<?php

use App\Helpers\PermissionHelper;

if (!function_exists('hasPermission')) {
    function hasPermission(string $permission): bool {
        return PermissionHelper::hasPermission($permission);
    }
}

if (!function_exists('hasModulePermission')) {
    function hasModulePermission(string $module, string $action = null): bool {
        return PermissionHelper::hasModulePermission($module, $action);
    }
}

if (!function_exists('requirePermission')) {
    function requirePermission(string $permission): void {
        PermissionHelper::requirePermission($permission);
    }
}