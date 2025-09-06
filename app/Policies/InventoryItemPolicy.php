<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

class InventoryItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view inventory items');
    }

    public function view(User $user, InventoryItem $model): bool
    {
        return $user->can('view inventory items');
    }

    public function create(User $user): bool
    {
        return $user->can('create inventory items');
    }

    public function update(User $user, InventoryItem $model): bool
    {
        return $user->can('edit inventory items');
    }

    public function delete(User $user, InventoryItem $model): bool
    {
        return $user->can('delete inventory items');
    }

    public function restore(User $user, InventoryItem $model): bool
    {
        return $user->can('edit inventory items');
    }

    public function forceDelete(User $user, InventoryItem $model): bool
    {
        return $user->can('delete inventory items');
    }
}
