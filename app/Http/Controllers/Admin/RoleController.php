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

    

   
}
