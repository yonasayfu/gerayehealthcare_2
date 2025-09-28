<?php

namespace App\Policies;

use App\Models\InsurancePolicy;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InsurancePolicyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view insurance policies');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->can('view insurance policies');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create insurance policies');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->can('edit insurance policies');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->can('delete insurance policies');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->can('edit insurance policies');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InsurancePolicy $insurancePolicy): bool
    {
        return $user->can('delete insurance policies');
    }
}
