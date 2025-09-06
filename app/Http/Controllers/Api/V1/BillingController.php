<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InsuranceClaimResource;
use App\Http\Resources\InsurancePolicyResource;
use App\Http\Resources\InvoiceResource;
use App\Models\EmployeeInsuranceRecord;
use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    protected function patientForUser($user): ?Patient
    {
        return Patient::where('user_id', $user->id)->first();
    }

    public function myInvoices(Request $request)
    {
        $patient = $this->patientForUser($request->user());
        if (! $patient) {
            return response()->json(['data' => []]);
        }
        $invoices = Invoice::with(['patient', 'items', 'insuranceCompany'])
            ->where('patient_id', $patient->id)
            ->orderByDesc('invoice_date')
            ->paginate($request->integer('per_page', 20));

        return InvoiceResource::collection($invoices);
    }

    public function showInvoice(Request $request, Invoice $invoice)
    {
        $patient = $this->patientForUser($request->user());
        if (! $patient || $invoice->patient_id !== $patient->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return new InvoiceResource($invoice->load(['patient', 'items', 'insuranceCompany']));
    }

    public function myPolicies(Request $request)
    {
        $patient = $this->patientForUser($request->user());
        if (! $patient) {
            return response()->json(['data' => []]);
        }
        $policies = InsurancePolicy::with(['insuranceCompany', 'corporateClient'])
            ->whereIn('id', EmployeeInsuranceRecord::where('patient_id', $patient->id)->pluck('policy_id'))
            ->get();

        return InsurancePolicyResource::collection($policies);
    }

    public function myClaims(Request $request)
    {
        $patient = $this->patientForUser($request->user());
        if (! $patient) {
            return response()->json(['data' => []]);
        }
        $claims = InsuranceClaim::with(['invoice', 'insuranceCompany', 'policy'])
            ->where('patient_id', $patient->id)
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        return InsuranceClaimResource::collection($claims);
    }

    public function showClaim(Request $request, InsuranceClaim $claim)
    {
        $patient = $this->patientForUser($request->user());
        if (! $patient || $claim->patient_id !== $patient->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return new InsuranceClaimResource($claim->load(['invoice', 'insuranceCompany', 'policy']));
    }
}
