<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

class UserPolicy
{
    public function before(User $user, string $ability)
    {
        if (method_exists($user, 'hasRole') && $user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view-users') || $user->hasRole(RoleEnum::ADMIN->value);
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('view-users') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create-users');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('edit-users') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('delete-users') && $user->id !== $model->id;
    }
}

