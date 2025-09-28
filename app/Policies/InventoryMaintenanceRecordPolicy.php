<?php

namespace App\Policies;

use App\Models\InventoryMaintenanceRecord;
use App\Models\User;

class InventoryMaintenanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view inventory maintenance records');
    }

    public function view(User $user, InventoryMaintenanceRecord $model): bool
    {
        return $user->can('view inventory maintenance records');
    }

    public function create(User $user): bool
    {
        return $user->can('create inventory maintenance records');
    }

    public function update(User $user, InventoryMaintenanceRecord $model): bool
    {
        return $user->can('edit inventory maintenance records');
    }

    public function delete(User $user, InventoryMaintenanceRecord $model): bool
    {
        return $user->can('delete inventory maintenance records');
    }

    public function restore(User $user, InventoryMaintenanceRecord $model): bool
    {
        return $user->can('edit inventory maintenance records');
    }

    public function forceDelete(User $user, InventoryMaintenanceRecord $model): bool
    {
        return $user->can('delete inventory maintenance records');
    }
}
