<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DenyRoles
{
    /**
     * Deny access if the user has ANY of the given roles.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Super admin bypass
        if (method_exists($user, 'hasRole') && $user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                abort(403, 'Access denied for role: '.$role);
            }
        }

        return $next($request);
    }
}

