<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\User;
use App\Services\AuthService;
use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends BaseApiController
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:sanctum')->except(['login', 'register', 'forgotPassword', 'resetPassword']);
    }

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
                'device_name' => 'string|max:255',
                'remember' => 'boolean',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            if (!$user->is_active) {
                return $this->error('Your account has been deactivated. Please contact support.', 403);
            }

            // Create token
            $deviceName = $request->device_name ?? $request->userAgent();
            $abilities = $this->getUserAbilities($user);
            $expiresAt = $request->remember ? now()->addDays(30) : now()->addHours(24);

            $token = $user->createToken($deviceName, $abilities, $expiresAt);

            return $this->success([
                'user' => new UserResource($user->load(['roles', 'permissions'])),
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->toISOString(),
                'abilities' => $abilities,
            ], 'Login successful');
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->handleException($e, 'Login failed');
        }
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone_number' => 'nullable|string|max:20',
                'device_name' => 'string|max:255',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'is_active' => true,
            ]);

            // Assign default role
            $user->assignRole('user');

            // Create token
            $deviceName = $request->device_name ?? $request->userAgent();
            $abilities = $this->getUserAbilities($user);
            $token = $user->createToken($deviceName, $abilities);

            return $this->success([
                'user' => new UserResource($user->load(['roles', 'permissions'])),
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'abilities' => $abilities,
            ], 'Registration successful', 201);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->handleException($e, 'Registration failed');
        }
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->success(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Logout failed');
        }
    }

    /**
     * Refresh token (create new token and revoke current)
     */
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            $currentToken = $request->user()->currentAccessToken();
            
            // Create new token with same abilities
            $deviceName = $currentToken->name;
            $abilities = $currentToken->abilities ?? $this->getUserAbilities($user);
            $newToken = $user->createToken($deviceName, $abilities);

            // Revoke current token
            $currentToken->delete();

            return $this->success([
                'token' => $newToken->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $newToken->accessToken->expires_at?->toISOString(),
                'abilities' => $abilities,
            ], 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Token refresh failed');
        }
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user()->load(['roles', 'permissions', 'staff']);

            return $this->success([
                'user' => new UserResource($user),
                'abilities' => $request->user()->currentAccessToken()->abilities ?? [],
            ], 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve user');
        }
    }

    /**
     * Get user's tokens
     */
    public function tokens(Request $request)
    {
        try {
            $tokens = $request->user()->tokens()->get()->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'last_used_at' => $token->last_used_at?->toISOString(),
                    'expires_at' => $token->expires_at?->toISOString(),
                    'created_at' => $token->created_at->toISOString(),
                ];
            });

            return $this->success([
                'tokens' => $tokens,
                'current_token_id' => $request->user()->currentAccessToken()->id,
            ], 'Tokens retrieved successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to retrieve tokens');
        }
    }

    /**
     * Revoke specific token
     */
    public function revokeToken(Request $request, PersonalAccessToken $token)
    {
        try {
            if ($token->tokenable_id !== $request->user()->id) {
                return $this->error('Unauthorized', 403);
            }

            $token->delete();

            return $this->success(null, 'Token revoked successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to revoke token');
        }
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return $this->success(null, 'Password reset link sent to your email');
            }

            return $this->error('Unable to send password reset link', 400);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->handleException($e, 'Failed to send password reset link');
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->success(null, 'Password reset successfully');
            }

            return $this->error('Invalid token or email', 400);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->handleException($e, 'Password reset failed');
        }
    }

    /**
     * Get user abilities based on roles and permissions
     */
    private function getUserAbilities(User $user): array
    {
        $abilities = ['*']; // Default abilities

        // Add role-based abilities
        foreach ($user->roles as $role) {
            $abilities[] = "role:{$role->name}";
        }

        // Add permission-based abilities
        foreach ($user->getAllPermissions() as $permission) {
            $abilities[] = "permission:{$permission->name}";
        }

        return array_unique($abilities);
    }
}
