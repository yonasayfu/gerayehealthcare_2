<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserApiController extends BaseApiController
{
    /**
     * The user service instance
     *
     * @var \App\Services\UserService
     */
    protected UserService $userService;

    /**
     * Create a new controller instance
     *
     * @param \App\Services\UserService $userService
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        try {
            $users = $this->userService->getAllUsers($request, ['roles']);
            return $this->paginated($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve users', 500, [$e->getMessage()]);
        }
    }

    /**
     * Store a newly created user
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        try {
            $dto = CreateUserDTO::fromRequest($request);
            $user = $this->userService->createFromDTO($dto);

            return $this->success($user, 'User created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create user', 422, [$e->getMessage()]);
        }
    }

    /**
     * Display the specified user
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $user = $this->userService->findUserById($id, ['roles', 'staff']);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $this->authorize('view', $user);

        return $this->success($user, 'User retrieved successfully');
    }

    /**
     * Update the specified user
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $user = $this->userService->findUserById($id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $this->authorize('update', $user);

        try {
            $dto = UpdateUserDTO::fromRequest($request);
            $updatedUser = $this->userService->updateFromDTO($id, $dto);

            return $this->success($updatedUser, 'User updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update user', 422, [$e->getMessage()]);
        }
    }

    /**
     * Remove the specified user
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $user = $this->userService->findUserById($id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $this->authorize('delete', $user);

        try {
            $this->userService->deleteUser($id);

            return $this->success(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete user', 500, [$e->getMessage()]);
        }
    }

    /**
     * Get active users
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function active(Request $request)
    {
        $this->authorize('viewAny', User::class);

        try {
            $users = $this->userService->getActiveUsers($request);
            return $this->paginated($users, 'Active users retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve active users', 500, [$e->getMessage()]);
        }
    }

    /**
     * Search users
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        try {
            $searchTerm = $request->input('query');
            $request->merge(['search' => $searchTerm]);

            $users = $this->userService->getAllUsers($request);
            return $this->paginated($users, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to search users', 500, [$e->getMessage()]);
        }
    }
}
