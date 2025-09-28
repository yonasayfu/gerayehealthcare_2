<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $user = Auth::user();

        // Super Admin has all permissions
        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        $hasRequiredPermission = false;
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                $hasRequiredPermission = true;
                break;
            }
        }

        if (! $hasRequiredPermission) {
            abort(403, 'Access denied. You do not have the required permissions.');
        }

        return $next($request);
    }
}
