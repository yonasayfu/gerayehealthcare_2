<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        $patient = Patient::where('user_id', $user->id)->first();

        return $patient && $invoice->patient_id === $patient->id;
    }
}
