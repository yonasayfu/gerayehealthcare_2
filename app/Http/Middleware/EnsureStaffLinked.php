<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffLinked
{
    /**
     * Handle an incoming request.
     *
     * Ensures the authenticated user has an associated Staff record.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Bypass for admin roles
        if ($user->hasAnyRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value])) {
            return $next($request);
        }

        $hasStaff = Staff::where('user_id', $user->id)->exists();
        if (!$hasStaff) {
            return back()->withErrors([
                'error' => 'Your account is not linked to a staff profile. Please contact an administrator to link your user to a Staff record.'
            ])->withInput();
        }

        return $next($request);
    }
}
