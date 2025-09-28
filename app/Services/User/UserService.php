<?php

namespace App\Services\User;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService extends BaseService
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'ilike', "%{$search}%")
            ->orWhere('email', 'ilike', "%{$search}%")
            ->orWhereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                    ->orWhere('last_name', 'ilike', "%{$search}%");
            });
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with('roles');

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        }

        return $query->paginate($request->input('per_page', 5));
    }

    public function create(array|object $data): User
    {
        $data = is_object($data) ? (array) $data : $data;

        return DB::transaction(function () use ($data) {
            $user = parent::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->staff()->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'position' => $data['position'] ?? null,
                'department' => $data['department'] ?? null,
                'hire_date' => $data['hire_date'] ?? null,
            ]);

            // Assign canonical staff role using enum value (lowercase)
            $user->assignRole(RoleEnum::STAFF->value);

            return $user;
        });
    }

    public function update(int $id, array|object $data): User
    {
        $data = is_object($data) ? (array) $data : $data;

        $user = $this->getById($id);
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->syncRoles($data['roles']);
        } elseif (isset($data['role'])) {
            // Backward compatibility
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function delete(int $id): void
    {
        $user = $this->getById($id);

        if ($user->hasRole('Super Admin') && $user->id === auth()->id()) {
            // In a real application, you might want to throw an exception here
            // and handle it in the controller to show an error message.
            return;
        }

        parent::delete($id);
    }
}
