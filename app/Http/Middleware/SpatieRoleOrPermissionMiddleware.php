<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ResponseHelper;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

class SpatieRoleOrPermissionMiddleware extends RoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $rolesOrPermissions, $guard = null)
    {
        foreach ($rolesOrPermissions as $roleOrPermission) {
            if ($request->user()->hasRole($roleOrPermission) || $request->user()->can($roleOrPermission)) {
                return $next($request);
            }
        }

        return ResponseHelper::error(
            ['required_roles_or_permissions' => $rolesOrPermissions],
            'Access denied: You do not have the required role or permission.',
            403
        );
    }
}
