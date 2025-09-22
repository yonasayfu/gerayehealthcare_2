<?php

namespace App\Policies;

use App\Models\MedicalDocument;
use App\Models\Patient;
use App\Models\User;

class MedicalDocumentPolicy
{
    public function view(User $user, MedicalDocument $doc): bool
    {
        if ($user->can('view medical documents')) {
            return true;
        }
        if ($user->staff && $doc->created_by_staff_id === $user->staff->id) {
            return true;
        }
        $patient = Patient::where('user_id', $user->id)->first();

        return $patient && $doc->patient_id === $patient->id;
    }

    public function create(User $user): bool
    {
        if ($user->can('create medical documents')) {
            return true;
        }

        return (bool) $user->staff;
    }
}
