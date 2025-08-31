<?php

namespace App\Policies;

use App\Models\InsuranceClaim;
use App\Models\Patient;
use App\Models\User;

class InsuranceClaimPolicy
{
    public function view(User $user, InsuranceClaim $claim): bool
    {
        $patient = Patient::where('email', $user->email)->first();
        return $patient && $claim->patient_id === $patient->id;
    }
}

