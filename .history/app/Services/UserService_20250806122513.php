<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserService extends BaseService
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhereHas('staff', function ($q) use ($search) {
                  $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
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

            $user->assignRole('Staff');

            return $user;
        });
    }

    public function update(int $id, array|object $data): User
    {
        $data = is_object($data) ? (array) $data : $data;
        
        $user = $this->getById($id);
        $user->syncRoles([$data['role']]);
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
