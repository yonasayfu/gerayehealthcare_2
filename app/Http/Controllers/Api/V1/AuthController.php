<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Enums\RoleEnum;
use App\Http\Resources\UserResource;
use App\Support\ModuleAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        $user = $request->user()->loadMissing(['roles', 'permissions']);

        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            $abilities = ['*'];
        } else {
            $abilities = $user->getAllPermissions()->pluck('name')->unique()->values()->all();
        }

        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'abilities' => $abilities,
            'user' => new UserResource($user),
            'modules' => ModuleAccess::forUser($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }
}
