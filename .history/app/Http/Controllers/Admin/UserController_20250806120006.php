<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\UserService;
use App\Models\User;
use App\Services\Validation\Rules\UserRules;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class UserController extends BaseController
{
    public function __construct(UserService $userService)
    {
        parent::__construct(
            $userService,
            UserRules::class,
            'Admin/Users',
            'users',
            User::class
        );
    }

    public function edit($id)
    {
        $user = $this->service->getById($id);
        $roles = Role::all()->pluck('name');
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }
}
