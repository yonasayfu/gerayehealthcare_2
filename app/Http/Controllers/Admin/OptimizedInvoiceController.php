<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateInvoiceDTO;
use App\Http\Controllers\Base\OptimizedBaseController;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\Optimized\Invoice\InvoiceService;
use App\Services\Validation\Rules\InvoiceRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class OptimizedInvoiceController extends OptimizedBaseController
{
    protected array $with = [
        'index' => ['patient:id,full_name', 'insuranceCompany:id,name'],
        'show' => ['patient', 'items.visitService.staff', 'insuranceCompany'],
        'create' => [],
        'edit' => ['patient', 'items.visitService'],
    ];

    public function __construct(\App\Services\Optimized\Invoice\InvoiceService $invoiceService)
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
        $cacheKey = 'invoice_create_data';
        $formData = Cache::remember($cacheKey, 300, function () {
            return [
                'patients' => Patient::select('id', 'full_name')->orderBy('full_name')->get(),
            ];
        });

        $selectedPatientId = request('patient_id');
        $billableVisits = [];

        if ($selectedPatientId) {
            $visitsCacheKey = "billable_visits_{$selectedPatientId}";
            $billableVisits = Cache::remember($visitsCacheKey, 300, function () use ($selectedPatientId) {
                return VisitService::where('patient_id', $selectedPatientId)
                    ->whereDoesntHave('invoiceItems')
                    ->select('id', 'service_description', 'scheduled_at', 'cost')
                    ->orderByDesc('scheduled_at')
                    ->get();
            });
        }

        return Inertia::render($this->viewName.'/Create', array_merge($formData, [
            'selectedPatientId' => $selectedPatientId,
            'billableVisits' => $billableVisits,
        ]));
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }

    public function printAll()
    {
        return $this->service->printAll(request());
    }

    public function printCurrent()
    {
        return $this->service->printCurrent(request());
    }

    public function printSingle(Invoice $invoice)
    {
        return $this->service->printSingle($invoice, request());
    }

    // Public, signed URL PDF endpoint (no auth). Route is protected by 'signed' middleware.
    public function publicPdf(Invoice $invoice)
    {
        return $this->service->printSingle($invoice, request());
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
        $groups = $this->service->getPendingBillables();

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
        $invoice = $this->service->create([
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
        $invoice->update([
            'status' => 'Approved',
            'approved_at' => now(),
        ]);

        // Clear related caches
        Cache::forget("invoice_single_{$invoice->id}");
        Cache::flush(); // Clear pattern-based caches

        return redirect()->back()
            ->with('banner', 'Invoice approved')
            ->with('bannerStyle', 'success');
    }

    /**
     * Bulk approve multiple invoices
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'invoice_ids' => ['required', 'array', 'min:1'],
            'invoice_ids.*' => ['integer', 'exists:invoices,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $count = $this->service->bulkApprove($request->input('invoice_ids'));

        return response()->json([
            'success' => true,
            'message' => "Successfully approved {$count} invoices",
            'approved_count' => $count,
        ]);
    }

    /**
     * Get financial dashboard data
     */
    public function dashboardData(Request $request): JsonResponse
    {
        $stats = $this->service->getFinancialStats($request);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Quick search for invoices (API endpoint)
     */
    public function quickSearch(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $cacheKey = 'invoice_search_'.md5($query);

        $results = Cache::remember($cacheKey, 300, function () use ($query) {
            return Invoice::with(['patient:id,full_name'])
                ->where(function ($q) use ($query) {
                    $q->where('invoice_number', 'ilike', "%{$query}%")
                        ->orWhereHas('patient', function ($subQ) use ($query) {
                            $subQ->where('full_name', 'ilike', "%{$query}%");
                        });
                })
                ->select('id', 'invoice_number', 'patient_id', 'grand_total', 'status', 'invoice_date')
                ->limit(10)
                ->get();
        });

        return response()->json(['data' => $results]);
    }

    /**
     * Get overdue invoices for follow-up
     */
    public function overdueInvoices(): JsonResponse
    {
        $cacheKey = 'overdue_invoices';

        $overdueInvoices = Cache::remember($cacheKey, 900, function () {
            return Invoice::with(['patient:id,full_name'])
                ->where('due_date', '<', now())
                ->whereNotIn('status', ['Paid', 'Cancelled'])
                ->select('id', 'invoice_number', 'patient_id', 'grand_total', 'due_date', 'status')
                ->orderBy('due_date')
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $overdueInvoices,
            'count' => $overdueInvoices->count(),
        ]);
    }

    /**
     * Monthly revenue report
     */
    public function monthlyRevenue(Request $request): JsonResponse
    {
        $year = $request->input('year', now()->year);
        $cacheKey = "monthly_revenue_{$year}";

        $monthlyData = Cache::remember($cacheKey, 3600, function () use ($year) {
            return Invoice::selectRaw('
                    EXTRACT(MONTH FROM invoice_date) as month,
                    SUM(grand_total) as total_revenue,
                    COUNT(*) as invoice_count,
                    AVG(grand_total) as avg_invoice_amount
                ')
                ->whereYear('invoice_date', $year)
                ->where('status', '!=', 'Cancelled')
                ->groupByRaw('EXTRACT(MONTH FROM invoice_date)')
                ->orderByRaw('EXTRACT(MONTH FROM invoice_date)')
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $monthlyData,
            'year' => $year,
        ]);
    }
}
