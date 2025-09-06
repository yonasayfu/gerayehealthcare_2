<?php

namespace App\Services;

use App\Http\Config\ExportConfig;
use App\Http\Traits\ExportableTrait;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\VisitService;
use App\Services\Insurance\InsuranceClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService extends BaseService
{
    use ExportableTrait;

    protected $insuranceClaimService;

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
        $query = $this->model->with(array_merge(['patient:id,full_name'], $with));

        // Hide Pending invoices by default (Incoming items will be handled in a separate queue)
        if (! $request->boolean('include_pending', false)) {
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
    }

    public function create(array|object $data): Invoice
    {
        $data = is_object($data) ? (array) $data : $data;

        return DB::transaction(function () use ($data) {
            $visitIds = $data['visit_ids'] ?? [];

            // Load visit services for items
            $visits = VisitService::whereIn('id', $visitIds)->get();

            // Compute totals
            $subtotal = $visits->sum(function ($v) {
                return (float) $v->cost;
            });
            // If tax_amount is not provided, default to 15% of subtotal
            if (array_key_exists('tax_amount', $data)) {
                $taxAmount = (float) $data['tax_amount'];
            } else {
                $taxAmount = round($subtotal * 0.15, 2);
            }
            $grandTotal = $subtotal + $taxAmount;

            // Prepare invoice payload
            $invoicePayload = [
                'patient_id' => $data['patient_id'],
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'due_date' => $data['due_date'] ?? now()->addDays(30)->toDateString(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'amount' => $grandTotal, // amount due at creation
                'status' => $data['status'] ?? 'Issued',
            ];

            // Create invoice
            $invoice = parent::create($invoicePayload);

            // Create invoice items
            if ($visits->isNotEmpty()) {
                $items = $visits->map(function ($visit) {
                    return [
                        'visit_service_id' => $visit->id,
                        'description' => $visit->service_description,
                        'cost' => $visit->cost,
                    ];
                })->all();
                $invoice->items()->createMany($items);
            }

            // Auto-create insurance claim if patient has active insurance
            $this->createInsuranceClaimIfEligible($invoice);

            return $invoice->load(['items.visitService']);
        });
    }

    /**
     * Create insurance claim automatically if patient has active insurance coverage
     */
    protected function createInsuranceClaimIfEligible(Invoice $invoice): void
    {
        try {
            $patient = $invoice->patient;
            if (! $patient) {
                return;
            }

            // Get active employee insurance record
            $insuranceRecord = $patient->employeeInsuranceRecords()
                ->where('verified', true)
                ->with(['policy.insuranceCompany'])
                ->latest()
                ->first();

            if (! $insuranceRecord || ! $insuranceRecord->policy) {
                Log::info("No active insurance found for patient {$patient->id}");

                return;
            }

            $policy = $insuranceRecord->policy;

            // Check if policy is active and covers the service type
            if (! $policy->is_active) {
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
            Log::error("Failed to create insurance claim for invoice {$invoice->id}: ".$e->getMessage());
            // Don't throw exception to avoid breaking invoice creation
        }
    }

    public function getById(int $id, array $with = []): Invoice
    {
        $with = array_unique(array_merge(['patient', 'items.visitService.staff', 'insuranceCompany'], $with));

        return $this->model->with($with)->findOrFail($id);
    }

    /**
     * Create invoice from completed visit service
     */
    public function createFromVisitService(VisitService $visitService): Invoice
    {
        $invoiceData = [
            'patient_id' => $visitService->patient_id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'tax_amount' => 0,
            'status' => 'Pending',
            'visit_ids' => [$visitService->id],
        ];

        return $this->create($invoiceData);
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
