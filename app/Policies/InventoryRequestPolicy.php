<?php

namespace App\Policies;

use App\Models\InventoryRequest;
use App\Models\User;

class InventoryRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view inventory requests');
    }

    public function view(User $user, InventoryRequest $model): bool
    {
        return $user->can('view inventory requests');
    }

    public function create(User $user): bool
    {
        return $user->can('create inventory requests');
    }

    public function update(User $user, InventoryRequest $model): bool
    {
        return $user->can('edit inventory requests');
    }

    public function delete(User $user, InventoryRequest $model): bool
    {
        return $user->can('delete inventory requests');
    }

    public function restore(User $user, InventoryRequest $model): bool
    {
        return $user->can('edit inventory requests');
    }

    public function forceDelete(User $user, InventoryRequest $model): bool
    {
        return $user->can('delete inventory requests');
    }
}
