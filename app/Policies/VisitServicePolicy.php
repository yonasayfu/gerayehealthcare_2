<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VisitService;

class VisitServicePolicy
{
    public function view(User $user, VisitService $visit): bool
    {
        return $this->isRelated($user, $visit);
    }

    public function update(User $user, VisitService $visit): bool
    {
        return $this->isRelated($user, $visit);
    }

    public function checkIn(User $user, VisitService $visit): bool
    {
        return $user->staff && $visit->staff_id === $user->staff->id;
    }

    public function checkOut(User $user, VisitService $visit): bool
    {
        return $user->staff && $visit->staff_id === $user->staff->id;
    }

    public function create(User $user, ?int $patientId = null): bool
    {
        // Staff can create for any patient; patients can create only for themselves
        if ($user->staff) return true;
        if ($patientId === null) return false;
        return optional($user)->email === optional(\App\Models\Patient::find($patientId))->email;
    }

    public function cancel(User $user, VisitService $visit): bool
    {
        return $this->isRelated($user, $visit);
    }

    protected function isRelated(User $user, VisitService $visit): bool
    {
        if ($user->staff && $visit->staff_id === $user->staff->id) {
            return true;
        }
        // If user is a patient, match by email
        return optional($visit->patient)->email === $user->email;
    }
}
