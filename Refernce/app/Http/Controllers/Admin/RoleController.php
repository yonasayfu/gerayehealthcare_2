<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateRoleDTO;
use App\DTOs\UpdateRoleDTO;
use App\Http\Controllers\OptimizedBaseController;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RoleController extends OptimizedBaseController
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
        $this->middleware('can:view-roles')->only(['index', 'show']);
        $this->middleware('can:create-roles')->only(['create', 'store']);
        $this->middleware('can:edit-roles')->only(['edit', 'update']);
        $this->middleware('can:delete-roles')->only(['destroy']);
    }

    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        try {
            $query = Role::with(['permissions', 'users']);

            // Apply search
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where('name', 'LIKE', "%{$search}%");
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $roles = $query->paginate($request->get('per_page', 15));

            if ($request->wantsJson()) {
                return $this->success($roles, 'Roles retrieved successfully');
            }

            return Inertia::render('Admin/Roles/Index', [
                'roles' => $roles,
                'filters' => $request->only(['search', 'sort_by', 'sort_order']),
                'permissions' => $this->roleService->getPermissionsGrouped(),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve roles');
        }
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        return Inertia::render('Admin/Roles/Create', [
            'permissions' => $this->roleService->getPermissionsGrouped(),
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        try {
            $dto = CreateRoleDTO::fromRequest($request);
            $role = $this->roleService->create($dto);

            if ($request->wantsJson()) {
                return $this->success($role, 'Role created successfully', 201);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to create role');
        }
    }

    /**
     * Display the specified role
     */
    public function show(Request $request, int $id)
    {
        try {
            $role = $this->roleService->getRoleWithPermissions($id);

            if ($request->wantsJson()) {
                return $this->success($role, 'Role retrieved successfully');
            }

            return Inertia::render('Admin/Roles/Show', [
                'role' => $role,
                'users' => $role->users()->paginate(10),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve role');
        }
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(int $id)
    {
        try {
            $role = $this->roleService->getRoleWithPermissions($id);

            return Inertia::render('Admin/Roles/Edit', [
                'role' => $role,
                'permissions' => $this->roleService->getPermissionsGrouped(),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to load role for editing');
        }
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, int $id)
    {
        try {
            $dto = UpdateRoleDTO::fromRequest($request);
            $role = $this->roleService->update($id, $dto);

            if ($request->wantsJson()) {
                return $this->success($role, 'Role updated successfully');
            }

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to update role');
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Request $request, int $id)
    {
        try {
            $this->roleService->delete($id);

            if ($request->wantsJson()) {
                return $this->success(null, 'Role deleted successfully');
            }

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to delete role');
        }
    }

    /**
     * Assign role to user
     */
    public function assignToUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'role_name' => 'required|exists:roles,name',
            ]);

            $this->roleService->assignRoleToUser(
                $request->get('user_id'),
                $request->get('role_name')
            );

            return $this->success(null, 'Role assigned successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to assign role');
        }
    }

    /**
     * Remove role from user
     */
    public function removeFromUser(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'role_name' => 'required|exists:roles,name',
            ]);

            $this->roleService->removeRoleFromUser(
                $request->get('user_id'),
                $request->get('role_name')
            );

            return $this->success(null, 'Role removed successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to remove role');
        }
    }

    /**
     * Initialize default roles and permissions
     */
    public function initializeDefaults(Request $request)
    {
        try {
            $this->roleService->initializeDefaultRoles();

            return $this->success(null, 'Default roles and permissions initialized successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to initialize default roles');
        }
    }
}
