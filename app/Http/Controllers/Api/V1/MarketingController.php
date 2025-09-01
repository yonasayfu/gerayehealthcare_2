<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\MarketingCampaignResource;
use App\Http\Resources\LeadResource;
use App\Models\MarketingCampaign;
use App\Models\Lead;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MarketingController extends BaseApiController
{
    /**
     * Get all marketing campaigns
     */
    public function campaigns(Request $request): JsonResponse
    {
        try {
            $query = MarketingCampaign::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                      ->orWhere('description', 'ILIKE', "%{$search}%")
                      ->orWhere('utm_source', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->where('start_date', '>=', $request->get('start_date'));
            }
            if ($request->has('end_date')) {
                $query->where('end_date', '<=', $request->get('end_date'));
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $campaigns = $query->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'campaigns' => MarketingCampaignResource::collection($campaigns->items()),
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'last_page' => $campaigns->lastPage(),
                    'per_page' => $campaigns->perPage(),
                    'total' => $campaigns->total(),
                    'has_more' => $campaigns->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch campaigns', 500);
        }
    }

    /**
     * Create a new marketing campaign
     */
    public function createCampaign(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'utm_source' => 'required|string|max:100',
                'utm_medium' => 'nullable|string|max:100',
                'utm_campaign' => 'nullable|string|max:100',
                'utm_term' => 'nullable|string|max:100',
                'utm_content' => 'nullable|string|max:100',
                'budget' => 'nullable|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after:start_date',
                'status' => 'required|in:draft,active,paused,completed',
                'target_audience' => 'nullable|string',
                'goals' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $campaign = MarketingCampaign::create($validator->validated());

            return $this->successResponse([
                'campaign' => new MarketingCampaignResource($campaign),
                'message' => 'Campaign created successfully'
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create campaign', 500);
        }
    }

    /**
     * Update a marketing campaign
     */
    public function updateCampaign(Request $request, MarketingCampaign $campaign): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'utm_source' => 'sometimes|required|string|max:100',
                'utm_medium' => 'nullable|string|max:100',
                'utm_campaign' => 'nullable|string|max:100',
                'utm_term' => 'nullable|string|max:100',
                'utm_content' => 'nullable|string|max:100',
                'budget' => 'nullable|numeric|min:0',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'nullable|date|after:start_date',
                'status' => 'sometimes|required|in:draft,active,paused,completed',
                'target_audience' => 'nullable|string',
                'goals' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $campaign->update($validator->validated());

            return $this->successResponse([
                'campaign' => new MarketingCampaignResource($campaign),
                'message' => 'Campaign updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update campaign', 500);
        }
    }

    /**
     * Delete a marketing campaign
     */
    public function deleteCampaign(MarketingCampaign $campaign): JsonResponse
    {
        try {
            $campaign->delete();
            return $this->successResponse(['message' => 'Campaign deleted successfully']);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete campaign', 500);
        }
    }

    /**
     * Get campaign leads
     */
    public function campaignLeads(Request $request, MarketingCampaign $campaign): JsonResponse
    {
        try {
            $query = Lead::where('marketing_campaign_id', $campaign->id);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by conversion status
            if ($request->has('converted')) {
                $query->where('converted_to_patient', $request->boolean('converted'));
            }

            $leads = $query->with(['patient', 'assignedTo'])
                          ->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'leads' => LeadResource::collection($leads->items()),
                'pagination' => [
                    'current_page' => $leads->currentPage(),
                    'last_page' => $leads->lastPage(),
                    'per_page' => $leads->perPage(),
                    'total' => $leads->total(),
                    'has_more' => $leads->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch campaign leads', 500);
        }
    }

    /**
     * Get all leads
     */
    public function leads(Request $request): JsonResponse
    {
        try {
            $query = Lead::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                      ->orWhere('email', 'ILIKE', "%{$search}%")
                      ->orWhere('phone', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by campaign
            if ($request->has('campaign_id')) {
                $query->where('marketing_campaign_id', $request->get('campaign_id'));
            }

            // Filter by conversion status
            if ($request->has('converted')) {
                $query->where('converted_to_patient', $request->boolean('converted'));
            }

            $leads = $query->with(['marketingCampaign', 'patient', 'assignedTo'])
                          ->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'leads' => LeadResource::collection($leads->items()),
                'pagination' => [
                    'current_page' => $leads->currentPage(),
                    'last_page' => $leads->lastPage(),
                    'per_page' => $leads->perPage(),
                    'total' => $leads->total(),
                    'has_more' => $leads->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch leads', 500);
        }
    }

    /**
     * Create a new lead
     */
    public function createLead(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'marketing_campaign_id' => 'nullable|exists:marketing_campaigns,id',
                'source' => 'nullable|string|max:100',
                'status' => 'required|in:new,contacted,qualified,converted,lost',
                'notes' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'interest_level' => 'nullable|in:low,medium,high',
                'budget_range' => 'nullable|string|max:100',
                'timeline' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $lead = Lead::create($validator->validated());

            return $this->successResponse([
                'lead' => new LeadResource($lead->load(['marketingCampaign', 'assignedTo'])),
                'message' => 'Lead created successfully'
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create lead', 500);
        }
    }

    /**
     * Convert lead to patient
     */
    public function convertLead(Request $request, Lead $lead): JsonResponse
    {
        try {
            if ($lead->converted_to_patient) {
                return $this->errorResponse('Lead is already converted to patient', 400);
            }

            DB::beginTransaction();

            // Create patient from lead data
            $patient = Patient::create([
                'full_name' => $lead->name,
                'email' => $lead->email,
                'phone_number' => $lead->phone,
                'lead_source' => $lead->source,
                'marketing_campaign_id' => $lead->marketing_campaign_id,
                'notes' => $lead->notes,
                'status' => 'active',
            ]);

            // Update lead
            $lead->update([
                'converted_to_patient' => true,
                'patient_id' => $patient->id,
                'status' => 'converted',
                'converted_at' => now(),
            ]);

            DB::commit();

            return $this->successResponse([
                'lead' => new LeadResource($lead->load(['patient', 'marketingCampaign'])),
                'patient' => $patient,
                'message' => 'Lead converted to patient successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to convert lead', 500);
        }
    }

    /**
     * Get marketing analytics
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
            $endDate = $request->get('end_date', now()->toDateString());

            $analytics = [
                'campaigns' => [
                    'total' => MarketingCampaign::count(),
                    'active' => MarketingCampaign::where('status', 'active')->count(),
                    'completed' => MarketingCampaign::where('status', 'completed')->count(),
                ],
                'leads' => [
                    'total' => Lead::whereBetween('created_at', [$startDate, $endDate])->count(),
                    'new' => Lead::where('status', 'new')->whereBetween('created_at', [$startDate, $endDate])->count(),
                    'converted' => Lead::where('converted_to_patient', true)->whereBetween('created_at', [$startDate, $endDate])->count(),
                    'conversion_rate' => $this->calculateConversionRate($startDate, $endDate),
                ],
                'top_campaigns' => $this->getTopCampaigns($startDate, $endDate),
                'lead_sources' => $this->getLeadSources($startDate, $endDate),
                'monthly_trends' => $this->getMonthlyTrends(),
            ];

            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch analytics', 500);
        }
    }

    private function calculateConversionRate($startDate, $endDate): float
    {
        $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();
        $convertedLeads = Lead::where('converted_to_patient', true)
                             ->whereBetween('created_at', [$startDate, $endDate])
                             ->count();

        return $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;
    }

    private function getTopCampaigns($startDate, $endDate): array
    {
        return MarketingCampaign::withCount(['leads' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->orderBy('leads_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'utm_source'])
            ->toArray();
    }

    private function getLeadSources($startDate, $endDate): array
    {
        return Lead::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    private function getMonthlyTrends(): array
    {
        return Lead::selectRaw('DATE_TRUNC(\'month\', created_at) as month, COUNT(*) as leads_count, SUM(CASE WHEN converted_to_patient THEN 1 ELSE 0 END) as conversions_count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }
}
