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

    public function edit(User $user)
    {
        $user->load('roles');
        
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => Role::all()->pluck('name'),
        ]);
    }

    public function update(Request $request, User $user)
    {
        return parent::update($request, $user->id);
    }

    public function destroy(User $user)
    {
        return parent::destroy($user->id);
    }
}
