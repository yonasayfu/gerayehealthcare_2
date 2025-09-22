<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use App\Support\ModuleAccess;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function me(Request $request)
    {
        $user = $request->user()->loadMissing(['roles', 'permissions']);

        return (new UserResource($user))
            ->additional([
                'modules' => ModuleAccess::forUser($user),
            ]);
    }

    public function update(UpdateUserRequest $request)
    {
        $user = $this->userService->update($request->user()->id, $request->validated());
        $user->loadMissing(['roles', 'permissions']);

        return (new UserResource($user))
            ->additional([
                'modules' => ModuleAccess::forUser($user),
            ]);
    }

    public function index()
    {
        $users = User::with(['roles', 'permissions'])->get();

        return UserResource::collection($users);
    }
}
