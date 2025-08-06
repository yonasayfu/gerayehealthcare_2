<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\UserService;
use App\Models\User;
use App\Services\Validation\Rules\UserRules;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $users = User::with('roles')->paginate(10);
        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $this->service->getById($id);
        $validatedData = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user->syncRoles($validatedData['role']);

        return redirect()->route('admin.users.index')->with('success', 'User role updated successfully.');
    }
}
