<?php

namespace App\Policies;

use App\Models\CaregiverAssignment;
use App\Models\Patient;
use App\Models\User;

class MessagePolicy
{
    public function communicate(User $authUser, User $other): bool
    {
        if ($authUser->id === $other->id) return false;

        $authIsStaff = (bool) $authUser->staff;
        $otherIsStaff = (bool) $other->staff;

        if ($authIsStaff && $otherIsStaff) return true;

        $authPatient = Patient::where('user_id', $authUser->id)->first();
        $otherPatient = Patient::where('user_id', $other->id)->first();

        if ($authPatient && $otherPatient) return false;

        if ($authIsStaff && $otherPatient) {
            return CaregiverAssignment::where('patient_id', $otherPatient->id)
                ->where('staff_id', optional($authUser->staff)->id)
                ->where('status', 'Assigned')
                ->exists();
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

