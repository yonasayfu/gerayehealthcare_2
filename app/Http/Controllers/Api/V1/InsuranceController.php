<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\InsuranceCompanyResource;
use App\Http\Resources\InsurancePolicyResource;
use App\Http\Resources\InsuranceClaimResource;
use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;
use App\Models\InsuranceClaim;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InsuranceController extends BaseApiController
{
    /**
     * Get all insurance companies
     */
    public function companies(Request $request): JsonResponse
    {
        try {
            $query = InsuranceCompany::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                      ->orWhere('contact_person', 'ILIKE', "%{$search}%")
                      ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            $companies = $query->withCount(['policies', 'claims'])
                              ->orderBy('name')
                              ->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'companies' => InsuranceCompanyResource::collection($companies->items()),
                'pagination' => [
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                    'per_page' => $companies->perPage(),
                    'total' => $companies->total(),
                    'has_more' => $companies->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch insurance companies', 500);
        }
    }

    /**
     * Create insurance company
     */
    public function createCompany(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'coverage_types' => 'nullable|array',
                'payment_terms' => 'nullable|string',
                'commission_rate' => 'nullable|numeric|min:0|max:100',
                'status' => 'required|in:active,inactive',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $company = InsuranceCompany::create($validator->validated());

            return $this->successResponse([
                'company' => new InsuranceCompanyResource($company),
                'message' => 'Insurance company created successfully'
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create insurance company', 500);
        }
    }

    /**
     * Get insurance policies
     */
    public function policies(Request $request): JsonResponse
    {
        try {
            $query = InsurancePolicy::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('policy_number', 'ILIKE', "%{$search}%")
                      ->orWhereHas('patient', function ($pq) use ($search) {
                          $pq->where('full_name', 'ILIKE', "%{$search}%");
                      });
                });
            }

            // Filter by company
            if ($request->has('company_id')) {
                $query->where('insurance_company_id', $request->get('company_id'));
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by patient
            if ($request->has('patient_id')) {
                $query->where('patient_id', $request->get('patient_id'));
            }

            // Filter by expiry
            if ($request->boolean('expiring_soon')) {
                $query->where('end_date', '<=', now()->addDays(30))
                      ->where('end_date', '>=', now());
            }

            $policies = $query->with(['patient', 'insuranceCompany', 'claims'])
                             ->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'policies' => InsurancePolicyResource::collection($policies->items()),
                'pagination' => [
                    'current_page' => $policies->currentPage(),
                    'last_page' => $policies->lastPage(),
                    'per_page' => $policies->perPage(),
                    'total' => $policies->total(),
                    'has_more' => $policies->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch insurance policies', 500);
        }
    }

    /**
     * Create insurance policy
     */
    public function createPolicy(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
                'insurance_company_id' => 'required|exists:insurance_companies,id',
                'policy_number' => 'required|string|max:100|unique:insurance_policies',
                'policy_type' => 'required|string|max:100',
                'coverage_amount' => 'required|numeric|min:0',
                'deductible' => 'nullable|numeric|min:0',
                'copay_percentage' => 'nullable|numeric|min:0|max:100',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|in:active,inactive,expired,cancelled',
                'covered_services' => 'nullable|array',
                'exclusions' => 'nullable|array',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $policy = InsurancePolicy::create($validator->validated());

            return $this->successResponse([
                'policy' => new InsurancePolicyResource($policy->load(['patient', 'insuranceCompany'])),
                'message' => 'Insurance policy created successfully'
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create insurance policy', 500);
        }
    }

    /**
     * Get insurance claims
     */
    public function claims(Request $request): JsonResponse
    {
        try {
            $query = InsuranceClaim::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('claim_number', 'ILIKE', "%{$search}%")
                      ->orWhereHas('patient', function ($pq) use ($search) {
                          $pq->where('full_name', 'ILIKE', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by policy
            if ($request->has('policy_id')) {
                $query->where('insurance_policy_id', $request->get('policy_id'));
            }

            // Filter by patient
            if ($request->has('patient_id')) {
                $query->where('patient_id', $request->get('patient_id'));
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->where('service_date', '>=', $request->get('start_date'));
            }
            if ($request->has('end_date')) {
                $query->where('service_date', '<=', $request->get('end_date'));
            }

            $claims = $query->with(['patient', 'insurancePolicy.insuranceCompany', 'visitService'])
                           ->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'claims' => InsuranceClaimResource::collection($claims->items()),
                'pagination' => [
                    'current_page' => $claims->currentPage(),
                    'last_page' => $claims->lastPage(),
                    'per_page' => $claims->perPage(),
                    'total' => $claims->total(),
                    'has_more' => $claims->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch insurance claims', 500);
        }
    }

    /**
     * Create insurance claim
     */
    public function createClaim(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
                'insurance_policy_id' => 'required|exists:insurance_policies,id',
                'visit_service_id' => 'nullable|exists:visit_services,id',
                'claim_number' => 'required|string|max:100|unique:insurance_claims',
                'service_date' => 'required|date',
                'diagnosis_code' => 'nullable|string|max:20',
                'procedure_code' => 'nullable|string|max:20',
                'service_description' => 'required|string',
                'billed_amount' => 'required|numeric|min:0',
                'covered_amount' => 'nullable|numeric|min:0',
                'deductible_amount' => 'nullable|numeric|min:0',
                'copay_amount' => 'nullable|numeric|min:0',
                'status' => 'required|in:submitted,under_review,approved,denied,paid,rejected',
                'submission_date' => 'nullable|date',
                'response_date' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $data = $validator->validated();
            $data['submitted_by'] = Auth::id();

            $claim = InsuranceClaim::create($data);

            return $this->successResponse([
                'claim' => new InsuranceClaimResource($claim->load(['patient', 'insurancePolicy.insuranceCompany'])),
                'message' => 'Insurance claim created successfully'
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create insurance claim', 500);
        }
    }

    /**
     * Update claim status
     */
    public function updateClaimStatus(Request $request, InsuranceClaim $claim): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:submitted,under_review,approved,denied,paid,rejected',
                'approved_amount' => 'nullable|numeric|min:0',
                'denial_reason' => 'nullable|string',
                'response_date' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $data = $validator->validated();
            $data['processed_by'] = Auth::id();

            $claim->update($data);

            return $this->successResponse([
                'claim' => new InsuranceClaimResource($claim->fresh(['patient', 'insurancePolicy.insuranceCompany'])),
                'message' => 'Claim status updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update claim status', 500);
        }
    }

    /**
     * Get insurance analytics
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
            $endDate = $request->get('end_date', now()->toDateString());

            $analytics = [
                'summary' => [
                    'total_companies' => InsuranceCompany::where('status', 'active')->count(),
                    'total_policies' => InsurancePolicy::where('status', 'active')->count(),
                    'total_claims' => InsuranceClaim::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'pending_claims' => InsuranceClaim::whereIn('status', ['submitted', 'under_review'])->count(),
                ],
                'claims_by_status' => InsuranceClaim::whereBetween('created_at', [$startDate, $endDate])
                    ->selectRaw('status, COUNT(*) as count, SUM(billed_amount) as total_amount')
                    ->groupBy('status')
                    ->get(),
                'top_companies' => InsuranceCompany::withCount(['claims' => function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    }])
                    ->orderBy('claims_count', 'desc')
                    ->limit(5)
                    ->get(),
                'monthly_trends' => InsuranceClaim::selectRaw('DATE_TRUNC(\'month\', created_at) as month, COUNT(*) as claims_count, SUM(billed_amount) as total_billed, SUM(approved_amount) as total_approved')
                    ->where('created_at', '>=', now()->subMonths(12))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get(),
                'expiring_policies' => InsurancePolicy::where('end_date', '<=', now()->addDays(30))
                    ->where('end_date', '>=', now())
                    ->where('status', 'active')
                    ->with(['patient', 'insuranceCompany'])
                    ->get(),
            ];

            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch insurance analytics', 500);
        }
    }
}
