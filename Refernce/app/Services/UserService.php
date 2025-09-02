<?php

namespace App\Services;

use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService extends PerformanceOptimizedBaseService
{
    /**
     * The model class name that this service operates on
     */
    protected string $model = User::class;

    /**
     * Cache prefix for this service
     */
    protected string $cachePrefix = 'users';

    /**
     * Validation rules for user creation
     */
    protected array $validationRules = [
        'create' => [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone_number' => ['nullable', 'string', 'regex:/^[\+]?[1-9][\d]{0,15}$/'],
            'is_active' => ['boolean'],
        ],
        'update' => [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['sometimes', 'string', 'min:8'],
            'phone_number' => ['nullable', 'string', 'regex:/^[\+]?[1-9][\d]{0,15}$/'],
            'is_active' => ['boolean'],
        ],
    ];

    /**
     * Create a new user
     */
    public function createFromDTO(CreateUserDTO $dto): User
    {
        $data = $dto->toArray();
        $data['password'] = Hash::make($data['password']);

        return $this->create($data);
    }

    /**
     * Update an existing user
     */
    public function updateFromDTO(int $id, UpdateUserDTO $dto): User
    {
        $data = $dto->toArray();

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->update($id, $data);
    }

    /**
     * Get all users with optional filtering
     */
    public function getAllUsers(Request $request, array $with = []): LengthAwarePaginator
    {
        return $this->getAll($request, $with);
    }

    /**
     * Find a user by ID
     */
    public function findUserById(int $id, array $with = []): ?User
    {
        return $this->findById($id, $with);
    }

    /**
     * Find a user by email
     */
    public function findUserByEmail(string $email): ?User
    {
        $cacheKey = $this->generateCacheKey('email', ['email' => $email]);

        return $this->remember($cacheKey, function () use ($email) {
            return $this->newQuery()->where('email', $email)->first();
        });
    }

    /**
     * Get active users
     */
    public function getActiveUsers(Request $request): LengthAwarePaginator
    {
        $request->merge(['is_active' => true]);

        return $this->getAllUsers($request);
    }

    /**
     * Apply search filter to query
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    protected function applySearch($query, string $search): void
    {
        $query->search($search);
    }

    /**
     * Apply additional filters to query
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    protected function applyFilters($query, Request $request): void
    {
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('role')) {
            $query->role($request->input('role'));
        }
    }

    /**
     * Delete a user
     */
    public function deleteUser(int $id): ?bool
    {
        // Check if user has associated staff record
        $user = $this->findUserById($id, ['staff']);

        if ($user && $user->staff) {
            // Delete associated staff record first
            $user->staff()->delete();
        }

        return $this->delete($id);
    }
}
