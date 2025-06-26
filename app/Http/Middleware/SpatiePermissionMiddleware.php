<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ResponseHelper;
use Spatie\Permission\Middleware\PermissionMiddleware;

class SpatiePermissionMiddleware extends PermissionMiddleware
{
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        // Split the $permission string by '|' to get an array of permissions
        $permissions = explode('|', $permission);
        if (! $request->user()->hasAnyPermission($permissions)) {
            return ResponseHelper::error(
                ['required_permission' => $permissions],
                'Access denied: You do not have the required permission.',
                403
            );
        }

        return $next($request);
    }
}
