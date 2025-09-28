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
        return $user->can('view task delegations');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskDelegation $taskDelegation): bool
    {
        if ($user->can('view task delegations')) {
            return true;
        }

        return (bool) ($user->staff && $user->staff->id === $taskDelegation->assigned_to);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create task delegations');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskDelegation $taskDelegation): bool
    {
        if ($user->can('edit task delegations')) {
            return true;
        }

        if ($taskDelegation->status === 'Completed') {
            return false;
        }

        return $user->staff && $user->staff->id === $taskDelegation->assigned_to;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskDelegation $taskDelegation): bool
    {
        return $user->can('delete task delegations');
    }
}
