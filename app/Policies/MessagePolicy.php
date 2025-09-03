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

        $authIsStaff = (bool) $authUser->staff;

        if ($authIsStaff) {
            return true;
        }

        $otherIsStaff = (bool) $other->staff;

        $authPatient = Patient::where('user_id', $authUser->id)->first();
        $otherPatient = Patient::where('user_id', $other->id)->first();

        if ($authPatient && $otherPatient) {
            return false;
        }

        if ($otherIsStaff && $authPatient) {
            return CaregiverAssignment::where('patient_id', $authPatient->id)
                ->where('staff_id', optional($other->staff)->id)
                ->where('status', 'Assigned')
                ->exists();
        }

        return false;
    }
}
