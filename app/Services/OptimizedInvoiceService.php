<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use App\Models\InsuranceClaim;
use App\Models\EmployeeInsuranceRecord;
use App\Services\Insurance\InsuranceClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\ExportConfig;

class OptimizedInvoiceService extends OptimizedBaseService
{
    use ExportableTrait;
    protected $insuranceClaimService;
    protected $cacheTtl = 900; // 15 minutes for financial data

    public function __construct(Invoice $invoice, InsuranceClaimService $insuranceClaimService)
    {
        parent::__construct($invoice);
        $this->insuranceClaimService = $insuranceClaimService;
    }

    protected function applySearch($query, $search)
    {
        $query->where('invoice_number', 'ilike', "%{$search}%")
              ->orWhere('status', 'ilike', "%{$search}%")
              ->orWhereHas('patient', function ($q) use ($search) {
                  $q->where('full_name', 'ilike', "%{$search}%");
              });
    }

    public function getAll(Request $request, array $with = [])
    {
        $cacheKey = $this->generateCacheKey('all', $request->all(), $with);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request, $with) {
            $query = $this->model->with(array_merge(['patient:id,full_name'], $with));

            // Hide Pending invoices by default (Incoming items will be handled in a separate queue)
            if (!$request->boolean('include_pending', false)) {
                $query->whereNotIn('status', ['Pending']);
            }

            if ($request->has('search')) {
                $this->applySearch($query, $request->input('search'));
            }

            if ($request->has('sort')) {
                $direction = $request->input('direction', 'asc');
                $query->orderBy($request->input('sort'), $direction);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            return $query->paginate($request->input('per_page', 10));
        });
    }

    public function getById(int $id, array $with = []): Invoice
    {
        $cacheKey = $this->generateCacheKey('single', ['id' => $id], $with);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $with) {
            $with = array_unique(array_merge(['patient', 'items.visitService.staff', 'insuranceCompany'], $with));
            return $this->model->with($with)->findOrFail($id);
        });
    }

    public function create(array|object $data): Invoice
    {
        $data = is_object($data) ? (array) $data : $data;

        return DB::transaction(function () use ($data) {
            $visitIds = $data['visit_ids'] ?? [];

            // Load visit services for items with eager loading
            $visits = VisitService::with(['patient', 'staff'])
                ->whereIn('id', $visitIds)
                ->get();

            // Compute totals
            $subtotal = $visits->sum(function ($v) {
                return (float) $v->cost;
            });
            
            // Tax calculation with configurable rate
            if (array_key_exists('tax_amount', $data)) {
                $taxAmount = (float) $data['tax_amount'];
            } else {
                $taxRate = config('billing.default_tax_rate', 0.15);
                $taxAmount = round($subtotal * $taxRate, 2);
            }
            $grandTotal = $subtotal + $taxAmount;

            // Prepare invoice payload
            $invoicePayload = [
                'patient_id'   => $data['patient_id'],
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'due_date'     => $data['due_date'] ?? now()->addDays(30)->toDateString(),
                'subtotal'     => $subtotal,
                'tax_amount'   => $taxAmount,
                'grand_total'  => $grandTotal,
                'amount'       => $grandTotal, // amount due at creation
                'status'       => $data['status'] ?? 'Issued',
            ];

            // Create invoice
            $invoice = parent::create($invoicePayload);

            // Create invoice items in batch
            if ($visits->isNotEmpty()) {
                $items = $visits->map(function ($visit) {
                    return [
                        'visit_service_id' => $visit->id,
                        'description'      => $visit->service_description,
                        'cost'             => $visit->cost,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];
                })->all();
                $invoice->items()->insert($items);
            }

            // Auto-create insurance claim if patient has active insurance
            $this->createInsuranceClaimIfEligible($invoice);

            // Clear related caches
            $this->clearRelatedCaches($invoice);

            return $invoice->load(['items.visitService', 'patient']);
        });
    }

    /**
     * Bulk approve invoices for administrative efficiency
     */
    public function bulkApprove(array $invoiceIds): int
    {
        $updated = DB::transaction(function () use ($invoiceIds) {
            $count = $this->model->whereIn('id', $invoiceIds)
                ->where('status', '!=', 'Approved')
                ->update([
                    'status' => 'Approved',
                    'approved_at' => now(),
                    'updated_at' => now(),
                ]);

            // Clear cache for updated invoices
            $this->clearCachePattern('invoice_*');
            
            return $count;
        });

        return $updated;
    }

    /**
     * Get financial statistics with caching
     */
    public function getFinancialStats(Request $request): array
    {
        $cacheKey = $this->generateCacheKey('financial_stats', $request->only(['date_from', 'date_to']));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request) {
            $query = $this->model->newQuery();
            
            if ($request->has('date_from')) {
                $query->where('invoice_date', '>=', $request->input('date_from'));
            }
            if ($request->has('date_to')) {
                $query->where('invoice_date', '<=', $request->input('date_to'));
            }

            return [
                'total_invoices' => $query->count(),
                'total_revenue' => $query->sum('grand_total'),
                'pending_amount' => $query->where('status', 'Pending')->sum('amount'),
                'approved_amount' => $query->where('status', 'Approved')->sum('grand_total'),
                'overdue_invoices' => $query->where('due_date', '<', now())->where('status', '!=', 'Paid')->count(),
                'avg_invoice_amount' => $query->avg('grand_total'),
            ];
        });
    }

    /**
     * Get pending billable services with caching
     */
    public function getPendingBillables(): array
    {
        $cacheKey = $this->generateCacheKey('pending_billables');
        
        return Cache::remember($cacheKey, 300, function () { // 5 minutes for real-time data
            $raw = VisitService::with(['patient:id,full_name'])
                ->where('status', 'Completed')
                ->whereDoesntHave('invoiceItems')
                ->select('id', 'patient_id', 'service_description', 'scheduled_at', 'cost')
                ->orderByDesc('scheduled_at')
                ->get();

            // Group by patient for batching
            return $raw->groupBy('patient_id')->map(function ($items, $patientId) {
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
            })->values()->all();
        });
    }

    /**
     * Create insurance claim automatically if patient has active insurance coverage
     */
    protected function createInsuranceClaimIfEligible(Invoice $invoice): void
    {
        try {
            $patient = $invoice->patient;
            if (!$patient) {
                return;
            }

            // Get active employee insurance record with caching
            $cacheKey = "patient_insurance_{$patient->id}";
            $insuranceRecord = Cache::remember($cacheKey, 3600, function () use ($patient) {
                return $patient->employeeInsuranceRecords()
                    ->where('verified', true)
                    ->with(['policy.insuranceCompany'])
                    ->latest()
                    ->first();
            });

            if (!$insuranceRecord || !$insuranceRecord->policy) {
                Log::info("No active insurance found for patient {$patient->id}");
                return;
            }

            $policy = $insuranceRecord->policy;
            
            // Check if policy is active and covers the service type
            if (!$policy->is_active) {
                Log::info("Insurance policy {$policy->id} is not active");
                return;
            }

            // Calculate coverage amount based on policy percentage
            $coveragePercentage = $policy->coverage_percentage / 100;
            $coverageAmount = $invoice->grand_total * $coveragePercentage;

            // Create insurance claim
            $claimData = [
                'patient_id' => $patient->id,
                'invoice_id' => $invoice->id,
                'insurance_company_id' => $policy->insurance_company_id,
                'policy_id' => $policy->id,
                'claim_status' => 'Submitted',
                'coverage_amount' => $coverageAmount,
                'paid_amount' => 0,
                'submitted_at' => now(),
                'reimbursement_required' => $coveragePercentage < 1.0, // True if not 100% coverage
            ];

            $this->insuranceClaimService->create($claimData);
            
            // Update invoice with insurance company reference
            $invoice->update(['insurance_company_id' => $policy->insurance_company_id]);

            Log::info("Insurance claim created for invoice {$invoice->id}, coverage: {$coverageAmount} ETB");

        } catch (\Exception $e) {
            Log::error("Failed to create insurance claim for invoice {$invoice->id}: " . $e->getMessage());
            // Don't throw exception to avoid breaking invoice creation
        }
    }

    /**
     * Create invoice from completed visit service
     */
    public function createFromVisitService(VisitService $visitService): Invoice
    {
        $invoiceData = [
            'patient_id'   => $visitService->patient_id,
            'invoice_date' => now()->toDateString(),
            'due_date'     => now()->addDays(30)->toDateString(),
            'tax_amount'   => 0,
            'status'       => 'Pending',
            'visit_ids'    => [$visitService->id],
        ];

        return $this->create($invoiceData);
    }

    /**
     * Clear related caches when invoice data changes
     */
    protected function clearRelatedCaches(Invoice $invoice): void
    {
        $patterns = [
            'invoice_*',
            'financial_stats_*',
            'pending_billables_*',
            "patient_insurance_{$invoice->patient_id}",
        ];

        foreach ($patterns as $pattern) {
            $this->clearCachePattern($pattern);
        }
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, Invoice::class, ExportConfig::getInvoiceConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, Invoice::class, ExportConfig::getInvoiceConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, Invoice::class, ExportConfig::getInvoiceConfig());
    }

    public function printSingle(Invoice $invoice, Request $request)
    {
        return $this->handlePrintSingle($request, $invoice, ExportConfig::getInvoiceConfig());
    }
}