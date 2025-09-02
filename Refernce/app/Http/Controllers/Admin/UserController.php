<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Http\Controllers\OptimizedBaseController;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends OptimizedBaseController
{
    /**
     * The user service instance
     */
    protected UserService $userService;

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    /**
     * Display a listing of users
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->getAllUsers($request, ['roles']);

        return inertia('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->all(['search', 'is_active', 'role']),
        ]);
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);

        return inertia('Admin/Users/Create');
    }

    /**
     * Store a newly created user
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        try {
            $dto = CreateUserDTO::fromRequest($request);
            $user = $this->userService->createFromDTO($dto);

            $this->flashSuccess('User created successfully.');

            return redirect()->route('admin.users.index');
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified user
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = $this->userService->findUserById($id, ['roles', 'staff']);

        if (! $user) {
            $this->flashError('User not found.');

            return redirect()->route('admin.users.index');
        }

        $this->authorize('view', $user);

        return inertia('Admin/Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $user = $this->userService->findUserById($id);

        if (! $user) {
            $this->flashError('User not found.');

            return redirect()->route('admin.users.index');
        }

        $this->authorize('update', $user);

        return inertia('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $user = $this->userService->findUserById($id);

        if (! $user) {
            $this->flashError('User not found.');

            return redirect()->route('admin.users.index');
        }

        $this->authorize('update', $user);

        try {
            $dto = UpdateUserDTO::fromRequest($request);
            $this->userService->updateFromDTO($id, $dto);

            $this->flashSuccess('User updated successfully.');

            return redirect()->route('admin.users.index');
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = $this->userService->findUserById($id);

        if (! $user) {
            $this->flashError('User not found.');

            return redirect()->route('admin.users.index');
        }

        $this->authorize('delete', $user);

        try {
            $this->userService->deleteUser($id);

            $this->flashSuccess('User deleted successfully.');

            return redirect()->route('admin.users.index');
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());

            return redirect()->route('admin.users.index');
        }
    }

    /**
     * Export users to specified format
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->getAllUsers($request);

        return $this->exportData($request, function () use ($users) {
            return $users;
        }, 'users');
    }
}
