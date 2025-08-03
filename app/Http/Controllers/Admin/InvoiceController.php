<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InvoiceService;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\Validation\Rules\InvoiceRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends BaseController
{
    public function __construct(InvoiceService $invoiceService)
    {
        parent::__construct(
            $invoiceService,
            InvoiceRules::class,
            'Admin/Invoices',
            'invoices',
            Invoice::class
        );
    }

    public function create(Request $request)
    {
        $billableVisits = [];
        if ($request->has('patient_id')) {
            $billableVisits = VisitService::where('patient_id', $request->patient_id)
                ->where('status', 'Completed')
                ->where('is_invoiced', false)
                ->get();
        }

        return Inertia::render('Admin/Invoices/Create', [
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            'selectedPatientId' => $request->input('patient_id'),
            'billableVisits' => $billableVisits,
        ]);
    }

    public function show(Invoice $invoice)
    {
        return parent::show($invoice->id);
    }
}
