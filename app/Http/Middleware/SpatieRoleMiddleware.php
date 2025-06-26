<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ResponseHelper;
use Spatie\Permission\Middleware\RoleMiddleware;

class SpatieRoleMiddleware extends RoleMiddleware
{
    public function handle($request, Closure $next, $role, $guard = null)
    {
        if (! $request->user()->hasRole($role)) {
            return ResponseHelper::error(
                ['required_role' => $role],
                'Access denied: You do not have the required role.',
                403
            );
        }

        return $next($request);
    }
}
