<?php

namespace App\Policies;

use App\Models\InsuranceClaim;
use App\Models\Patient;
use App\Models\User;

class InsuranceClaimPolicy
{
    public function view(User $user, InsuranceClaim $claim): bool
    {
        $patient = Patient::where('user_id', $user->id)->first();

        return $patient && $claim->patient_id === $patient->id;
    }
}
