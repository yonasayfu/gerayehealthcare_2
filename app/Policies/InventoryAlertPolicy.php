<?php

namespace App\Policies;

use App\Models\InventoryAlert;
use App\Models\User;

class InventoryAlertPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view inventory alerts');
    }

    public function view(User $user, InventoryAlert $model): bool
    {
        return $user->can('view inventory alerts');
    }

    public function create(User $user): bool
    {
        return $user->can('create inventory alerts');
    }

    public function update(User $user, InventoryAlert $model): bool
    {
        return $user->can('edit inventory alerts');
    }

    public function delete(User $user, InventoryAlert $model): bool
    {
        return $user->can('delete inventory alerts');
    }

    public function restore(User $user, InventoryAlert $model): bool
    {
        return $user->can('edit inventory alerts');
    }

    public function forceDelete(User $user, InventoryAlert $model): bool
    {
        return $user->can('delete inventory alerts');
    }
}
