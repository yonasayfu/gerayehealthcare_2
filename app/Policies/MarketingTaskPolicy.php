<?php

namespace App\Policies;

use App\Models\MarketingTask;
use App\Models\User;

class MarketingTaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_marketing_tasks');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MarketingTask $marketingTask): bool
    {
        return $user->hasPermissionTo('view_marketing_tasks');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_marketing_tasks');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MarketingTask $marketingTask): bool
    {
        return $user->hasPermissionTo('update_marketing_tasks');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MarketingTask $marketingTask): bool
    {
        return $user->hasPermissionTo('delete_marketing_tasks');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MarketingTask $marketingTask): bool
    {
        return $user->hasPermissionTo('restore_marketing_tasks');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MarketingTask $marketingTask): bool
    {
        return $user->hasPermissionTo('force_delete_marketing_tasks');
    }
}
