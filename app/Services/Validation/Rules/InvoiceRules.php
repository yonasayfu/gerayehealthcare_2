<?php

namespace App\Services\Validation\Rules;

class InvoiceRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'visit_ids' => 'required|array|min:1',
            'visit_ids.*' => 'required|exists:visit_services,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
        ];
    }

    public static function update($item): array
    {
        // Invoices are typically not updated via a simple form, but rather through payments or adjustments.
        // If update functionality is needed, define rules here.
        return [];
    }
}