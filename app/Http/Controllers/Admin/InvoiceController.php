<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateInvoiceDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\InvoiceService;
use App\Services\Validation\Rules\InvoiceRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
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

        return Inertia::render($this->viewName.'/Create', [
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

    /**
     * Incoming Invoices queue: billable visit services completed by staff but not invoiced yet
     */
    public function incoming()
    {
        $raw = VisitService::with(['patient:id,full_name'])
            ->where('status', 'Completed')
            ->whereDoesntHave('invoiceItems')
            ->select('id', 'patient_id', 'service_description', 'scheduled_at', 'cost')
            ->orderByDesc('scheduled_at')
            ->get();

        // Group by patient for batching
        $groups = $raw->groupBy('patient_id')->map(function ($items, $patientId) {
            $total = $items->sum('cost');
            $dates = $items->pluck('scheduled_at')->filter();

            return [
                'patient_id' => $patientId,
                'patient_name' => optional($items->first()->patient)->full_name,
                'count' => $items->count(),
                'first_date' => optional($dates->min())->format('Y-m-d H:i:s'),
                'last_date' => optional($dates->max())->format('Y-m-d H:i:s'),
                'estimated_total' => (float) $total,
                'visit_ids' => $items->pluck('id')->values()->all(),
            ];
        })->values();

        return Inertia::render($this->viewName.'/Incoming', [
            'groups' => $groups,
        ]);
    }

    /**
     * Generate an invoice from selected visit services (multi-visit design)
     */
    public function generate(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'visit_ids' => ['required', 'array', 'min:1'],
            'visit_ids.*' => ['integer', 'exists:visit_services,id'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create invoice via service
        $invoice = app(InvoiceService::class)->create([
            'patient_id' => (int) $data['patient_id'],
            'visit_ids' => $data['visit_ids'],
            'status' => 'Issued',
        ]);

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('banner', 'Invoice generated successfully')->with('bannerStyle', 'success');
    }

    /**
     * Approve an invoice (admin action)
     */
    public function approve(Invoice $invoice)
    {
        $invoice->update(['status' => 'Approved']);

        return redirect()->back()->with('banner', 'Invoice approved')->with('bannerStyle', 'success');
    }
}
