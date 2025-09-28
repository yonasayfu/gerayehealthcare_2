<?php

namespace App\Policies;

use App\Models\CaregiverAssignment;
use App\Models\Patient;
use App\Models\User;

class MessagePolicy
{
    public function communicate(User $authUser, User $other): bool
    {
        if ($authUser->id === $other->id) {
            return false;
        }

        return true;
    }
}
