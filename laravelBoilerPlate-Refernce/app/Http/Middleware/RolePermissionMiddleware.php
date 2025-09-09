<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$rolesOrPermissions): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if any of the provided arguments are roles the user has
        foreach ($rolesOrPermissions as $roleOrPermission) {
            if ($user->hasRole($roleOrPermission) || $user->can($roleOrPermission)) {
                return $next($request);
            }
        }

        // If we reach here, the user doesn't have any of the required roles or permissions
        abort(403, 'Unauthorized access.');
    }
}
