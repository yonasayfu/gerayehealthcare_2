<?php

namespace App\Policies;

use App\Models\ReferralDocument;
use App\Models\User;

class ReferralDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_referral_documents');
    }

    public function view(User $user, ReferralDocument $referralDocument): bool
    {
        return $user->hasPermissionTo('view_referral_documents');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_referral_documents');
    }

    public function update(User $user, ReferralDocument $referralDocument): bool
    {
        return $user->hasPermissionTo('update_referral_documents');
    }

    public function delete(User $user, ReferralDocument $referralDocument): bool
    {
        return $user->hasPermissionTo('delete_referral_documents');
    }

    public function restore(User $user, ReferralDocument $referralDocument): bool
    {
        return $user->hasPermissionTo('restore_referral_documents');
    }

    public function forceDelete(User $user, ReferralDocument $referralDocument): bool
    {
        return $user->hasPermissionTo('force_delete_referral_documents');
    }
}
