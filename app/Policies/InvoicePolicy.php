<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        $patient = Patient::where('email', $user->email)->first();
        return $patient && $invoice->patient_id === $patient->id;
    }
}

