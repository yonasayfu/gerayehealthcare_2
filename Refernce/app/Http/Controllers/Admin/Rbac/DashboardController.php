<?php

namespace App\Http\Controllers\Admin\Rbac;

use App\Http\Controllers\OptimizedBaseController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class DashboardController extends OptimizedBaseController
{
    /**
     * Display the RBAC dashboard.
     */
    public function index(Request $request)
    {
        // Check if user has permission to view the RBAC dashboard
        if (! $request->user()->can('view-reports')) {
            return $this->error('You do not have permission to access the RBAC dashboard.', 403);
        }

        // Get all roles for display with permission counts
        $roles = Role::withCount('permissions')->get();

        // Get user's current role
        $userRole = $request->user()->roles->first();

        return Inertia::render('rbac/Dashboard', [
            'roles' => $roles,
            'userRole' => $userRole,
            'user' => $request->user(),
        ]);
    }

    /**
     * Display a specific role's permissions.
     */
    public function showRole(Request $request, $roleName)
    {
        // Check if user has permission to view roles
        if (! $request->user()->can('view-roles')) {
            return $this->error('You do not have permission to view roles.', 403);
        }

        $role = Role::where('name', $roleName)->with('permissions')->firstOrFail();
        $permissions = $role->permissions;

        return $this->success([
            'role' => $role,
            'permissions' => $permissions,
        ], "Permissions for {$roleName} role");
    }
}
