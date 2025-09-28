<?php

namespace App\Policies;

use App\Models\InventoryTransaction;
use App\Models\User;

class InventoryTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view inventory transactions');
    }

    public function view(User $user, InventoryTransaction $model): bool
    {
        return $user->can('view inventory transactions');
    }

    public function create(User $user): bool
    {
        return $user->can('create inventory transactions');
    }

    public function update(User $user, InventoryTransaction $model): bool
    {
        return $user->can('edit inventory transactions');
    }

    public function delete(User $user, InventoryTransaction $model): bool
    {
        return $user->can('delete inventory transactions');
    }

    public function restore(User $user, InventoryTransaction $model): bool
    {
        return $user->can('edit inventory transactions');
    }

    public function forceDelete(User $user, InventoryTransaction $model): bool
    {
        return $user->can('delete inventory transactions');
    }
}
