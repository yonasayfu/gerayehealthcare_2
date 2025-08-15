<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\InvoiceService;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\Validation\Rules\InvoiceRules;
use Illuminate\Http\Request;
use App\DTOs\CreateInvoiceDTO;
use Inertia\Inertia;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;

class InvoiceController extends BaseController
{
    public function __construct(InvoiceService $invoiceService)
    {
        parent::__construct(
            $invoiceService,
            InvoiceRules::class,
            'Admin/Invoices',
            'invoices',
            Invoice::class,
            CreateInvoiceDTO::class
        );
    }

    public function create()
    {
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->get();

        $selectedPatientId = request('patient_id');
        $billableVisits = [];

        if ($selectedPatientId) {
            $billableVisits = VisitService::where('patient_id', $selectedPatientId)
                ->select('id', 'service_description', 'scheduled_at', 'cost')
                ->orderByDesc('scheduled_at')
                ->get();
        }

        return Inertia::render($this->viewName . '/Create', [
            'patients' => $patients,
            'selectedPatientId' => $selectedPatientId,
            'billableVisits' => $billableVisits,
        ]);
    }

    public function export(Request $request)
    {
        return app(InvoiceService::class)->export($request);
    }

    public function printAll()
    {
        return app(InvoiceService::class)->printAll(request());
    }

    public function printCurrent()
    {
        return app(InvoiceService::class)->printCurrent(request());
    }

    public function printSingle(Invoice $invoice)
    {
        return app(InvoiceService::class)->printSingle($invoice, request());
    }

    // Public, signed URL PDF endpoint (no auth). Route is protected by 'signed' middleware.
    public function publicPdf(Invoice $invoice)
    {
        return app(InvoiceService::class)->printSingle($invoice, request());
    }

    // Returns a signed URL for public PDF access, to be shared via Telegram/email/etc.
    public function shareLink(Invoice $invoice): JsonResponse
    {
        $expiresAt = now()->addDays(3);
        $signedUrl = URL::signedRoute('invoices.public_pdf', ['invoice' => $invoice->id], $expiresAt);
        return response()->json([
            'url' => $signedUrl,
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }
}
