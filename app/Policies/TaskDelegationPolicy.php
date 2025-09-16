<?php

namespace App\Policies;

use App\Models\TaskDelegation;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskDelegationPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskDelegation $taskDelegation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskDelegation $taskDelegation): bool
    {
        if ($user->hasRole(RoleEnum::ADMIN->value)) {
            return true;
        }

        if ($taskDelegation->status === 'Completed') {
            return false;
        }

        return $user->id === $taskDelegation->created_by || $user->staff->id === $taskDelegation->assigned_to;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskDelegation $taskDelegation): bool
    {
        return $user->hasRole(RoleEnum::ADMIN->value);
    }
}