<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\Validation\Rules\RoleRules;
use Inertia\Inertia;

class RoleController extends BaseController
{
    public function __construct(RoleService $roleService)
    {
        parent::__construct(
            $roleService,
            RoleRules::class,
            'Admin/Roles',
            'roles',
            Role::class
        );
    }

    public function create()
    {
        return Inertia::render('Admin/Roles/Create', [
            'permissions' => Permission::all()->pluck('name'),
        ]);
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role,
            'allPermissions' => Permission::all()->pluck('name'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        return parent::update($request, $role->id);
    }

    public function destroy(Role $role)
    {
        return parent::destroy($role->id);
    }
}
