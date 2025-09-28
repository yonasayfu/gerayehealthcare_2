<?php

namespace App\Policies;

use App\Models\Referral;
use App\Models\User;

class ReferralPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view referrals');
    }

    public function view(User $user, Referral $model): bool
    {
        return $user->can('view referrals');
    }

    public function create(User $user): bool
    {
        return $user->can('create referrals');
    }

    public function update(User $user, Referral $model): bool
    {
        return $user->can('edit referrals');
    }

    public function delete(User $user, Referral $model): bool
    {
        return $user->can('delete referrals');
    }

    public function restore(User $user, Referral $model): bool
    {
        return $user->can('edit referrals');
    }

    public function forceDelete(User $user, Referral $model): bool
    {
        return $user->can('delete referrals');
    }
}
