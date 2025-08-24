<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Base\BaseController;
use App\Models\InsuranceClaim;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\InsuranceClaimEmail;
use Inertia\Inertia;
use App\Http\Requests\StoreInsuranceClaimRequest;
use App\Http\Requests\UpdateInsuranceClaimRequest;
use App\Http\Requests\SendClaimEmailRequest;
use App\Services\Insurance\InsuranceClaimService;
use App\Models\Patient;
use App\Models\Invoice;
use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;
use App\Services\Validation\Rules\InsuranceClaimRules;
use App\DTOs\CreateInsuranceClaimDTO;

class InsuranceClaimController extends BaseController
{
    public function __construct(InsuranceClaimService $insuranceClaimService)
    {
        parent::__construct(
            $insuranceClaimService,
            InsuranceClaimRules::class,
            'Insurance/Claims',
            'insuranceClaims',
            InsuranceClaim::class,
            CreateInsuranceClaimDTO::class
        );
    }

    public function show($id)
    {
        $insuranceClaim = InsuranceClaim::with(['invoice.patient'])->findOrFail($id);
        return Inertia::render($this->viewName . '/Show', [
            'insuranceClaim' => $insuranceClaim,
        ]);
    }

    public function create()
    {
        // OPTIMIZED: Use select with pagination instead of all()
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->limit(1000)->get();
        $invoices = Invoice::select('id', 'invoice_number', 'grand_total', 'patient_id')
            ->with('patient:id,full_name')
            ->orderBy('invoice_number')
            ->limit(1000)
            ->get();
        $insuranceCompanies = InsuranceCompany::select('id', 'name')->orderBy('name')->get();
        $insurancePolicies = InsurancePolicy::select('id', 'service_type', 'coverage_percentage')
            ->orderBy('service_type')
            ->get();

        return Inertia::render($this->viewName . '/Create', [
            'patients' => $patients,
            'invoices' => $invoices,
            'insuranceCompanies' => $insuranceCompanies,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }

    public function edit($id)
    {
        $insuranceClaim = $this->service->getById($id);
        // OPTIMIZED: Use select with limits instead of all()
        $patients = Patient::select('id', 'full_name')->orderBy('full_name')->limit(1000)->get();
        $invoices = Invoice::select('id', 'invoice_number', 'grand_total', 'patient_id')
            ->with('patient:id,full_name')
            ->orderBy('invoice_number')
            ->limit(1000)
            ->get();
        $insuranceCompanies = InsuranceCompany::select('id', 'name')->orderBy('name')->get();
        $insurancePolicies = InsurancePolicy::select('id', 'service_type', 'coverage_percentage')
            ->orderBy('service_type')
            ->get();

        return Inertia::render($this->viewName . '/Edit', [
            'insuranceClaim' => $insuranceClaim,
            'patients' => $patients,
            'invoices' => $invoices,
            'insuranceCompanies' => $insuranceCompanies,
            'insurancePolicies' => $insurancePolicies,
        ]);
    }

    public function processPayment(Request $request, $id)
    {
        $validatedData = $request->validate([
            'paid_amount' => 'required|numeric|min:0',
            'payment_received_at' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'receipt_number' => 'nullable|string',
        ]);

        try {
            $claim = $this->service->processPayment($id, $validatedData);
            return Redirect::back()->with('banner', 'Payment processed successfully.')->with('bannerStyle', 'success');
        } catch (\Exception $e) {
            return Redirect::back()->with('banner', 'Failed to process payment: ' . $e->getMessage())->with('bannerStyle', 'danger');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validatedData = $request->validate([
            'status' => 'required|string|in:Submitted,Processing,Approved,Denied,Paid',
            'denial_reason' => 'nullable|string|required_if:status,Denied',
        ]);

        try {
            $claim = $this->service->updateClaimStatus($id, $validatedData['status'], $validatedData['denial_reason'] ?? null);
            return Redirect::back()->with('banner', 'Claim status updated successfully.')->with('bannerStyle', 'success');
        } catch (\Exception $e) {
            return Redirect::back()->with('banner', 'Failed to update claim status: ' . $e->getMessage())->with('bannerStyle', 'danger');
        }
    }
}
