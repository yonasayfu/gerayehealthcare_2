<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\MarketingCampaignResource;
use App\Http\Resources\MarketingLeadResource;
use App\Http\Resources\PatientResource;
use App\Models\MarketingCampaign;
use App\Models\MarketingLead;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MarketingController extends BaseApiController
{
    private const CAMPAIGN_STATUS_MAP = [
        'draft' => 'Draft',
        'active' => 'Active',
        'paused' => 'Paused',
        'completed' => 'Completed',
    ];

    private const LEAD_STATUS_MAP = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'converted' => 'Converted',
        'lost' => 'Lost',
    ];

    /**
     * Get marketing campaigns with filters and pagination.
     */
    public function campaigns(Request $request): JsonResponse
    {
        try {
            $perPage = $this->resolvePerPage($request->get('per_page'));

            $query = MarketingCampaign::query()
                ->with([
                    'platform',
                    'assignedStaff',
                    'responsibleStaff',
                    'createdByStaff',
                ])
                ->withCount([
                    'leads',
                    'leads as converted_leads_count' => function ($q) {
                        $q->whereNotNull('converted_patient_id');
                    },
                ]);

            if ($request->filled('search')) {
                $search = trim((string) $request->get('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('campaign_name', 'ILIKE', "%{$search}%")
                        ->orWhere('campaign_code', 'ILIKE', "%{$search}%")
                        ->orWhere('utm_source', 'ILIKE', "%{$search}%")
                        ->orWhere('utm_campaign', 'ILIKE', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $status = $this->normalizeCampaignStatus($request->get('status'));
                if ($status) {
                    $query->where('status', $status);
                }
            }

            if ($request->filled('platform_id')) {
                $query->where('platform_id', (int) $request->get('platform_id'));
            }

            if ($request->filled('assigned_staff_id')) {
                $query->where('assigned_staff_id', (int) $request->get('assigned_staff_id'));
            }

            if ($request->filled('start_date')) {
                $query->whereDate('start_date', '>=', $request->get('start_date'));
            }

            if ($request->filled('end_date')) {
                $query->whereDate('end_date', '<=', $request->get('end_date'));
            }

            $sortBy = $request->get('sort_by', 'start_date');
            $allowedSorts = [
                'campaign_name',
                'campaign_code',
                'start_date',
                'end_date',
                'status',
                'budget_allocated',
                'budget_spent',
                'created_at',
                'updated_at',
            ];
            if (!in_array($sortBy, $allowedSorts, true)) {
                $sortBy = 'start_date';
            }

            $sortOrder = strtolower((string) $request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

            $campaigns = $query
                ->orderBy($sortBy, $sortOrder)
                ->paginate($perPage);

            return $this->successResponse([
                'campaigns' => MarketingCampaignResource::collection($campaigns->getCollection()),
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'last_page' => $campaigns->lastPage(),
                    'per_page' => $campaigns->perPage(),
                    'total' => $campaigns->total(),
                    'has_more' => $campaigns->hasMorePages(),
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to fetch campaigns', 500);
        }
    }

    /**
     * Create a marketing campaign.
     */
    public function createCampaign(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'platform_id' => 'required|exists:marketing_platforms,id',
                'campaign_name' => 'required|string|max:255',
                'campaign_type' => 'nullable|string|max:100',
                'target_audience' => 'nullable|array',
                'budget_allocated' => 'nullable|numeric|min:0',
                'budget_spent' => 'nullable|numeric|min:0',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|in:Draft,Active,Paused,Completed',
                'urgency' => 'nullable|in:Low,Medium,High',
                'utm_campaign' => 'nullable|string|max:255',
                'utm_source' => 'nullable|string|max:255',
                'utm_medium' => 'nullable|string|max:255',
                'assigned_staff_id' => 'nullable|exists:staff,id',
                'responsible_staff_id' => 'nullable|exists:staff,id',
                'goals' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $payload = $validator->validated();
            $payload['status'] = $this->normalizeCampaignStatus($payload['status'] ?? null) ?? 'Draft';

            if (!empty($payload['target_audience'])) {
                $payload['target_audience'] = array_values($payload['target_audience']);
            }

            if (!empty($payload['goals'])) {
                $payload['goals'] = array_values($payload['goals']);
            }

            $staffId = optional(Auth::user()?->staff)->id;
            if ($staffId) {
                $payload['created_by_staff_id'] = $staffId;
            }

            $campaign = MarketingCampaign::create($payload);
            $campaign->load(['platform', 'assignedStaff', 'responsibleStaff', 'createdByStaff']);

            return $this->createdResponse([
                'campaign' => new MarketingCampaignResource($campaign),
            ], 'Campaign created successfully');
        } catch (\Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to create campaign', 500);
        }
    }

    /**
     * Update a marketing campaign.
     */
    public function updateCampaign(Request $request, MarketingCampaign $campaign): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'platform_id' => 'sometimes|required|exists:marketing_platforms,id',
                'campaign_name' => 'sometimes|required|string|max:255',
                'campaign_type' => 'nullable|string|max:100',
                'target_audience' => 'nullable|array',
                'budget_allocated' => 'nullable|numeric|min:0',
                'budget_spent' => 'nullable|numeric|min:0',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'nullable|in:Draft,Active,Paused,Completed',
                'urgency' => 'nullable|in:Low,Medium,High',
                'utm_campaign' => 'nullable|string|max:255',
                'utm_source' => 'nullable|string|max:255',
                'utm_medium' => 'nullable|string|max:255',
                'assigned_staff_id' => 'nullable|exists:staff,id',
                'responsible_staff_id' => 'nullable|exists:staff,id',
                'goals' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $payload = $validator->validated();

            if (array_key_exists('status', $payload)) {
                $payload['status'] = $this->normalizeCampaignStatus($payload['status']);
            }

            if (array_key_exists('target_audience', $payload) && is_array($payload['target_audience'])) {
                $payload['target_audience'] = array_values($payload['target_audience']);
            }

            if (array_key_exists('goals', $payload) && is_array($payload['goals'])) {
                $payload['goals'] = array_values($payload['goals']);
            }

            $campaign->update($payload);
            $campaign->load(['platform', 'assignedStaff', 'responsibleStaff', 'createdByStaff']);

            return $this->successResponse([
                'campaign' => new MarketingCampaignResource($campaign),
                'message' => 'Campaign updated successfully',
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to update campaign', 500);
        }
    }

    /**
     * Delete a marketing campaign.
     */
    public function deleteCampaign(MarketingCampaign $campaign): JsonResponse
    {
        try {
            $campaign->delete();

            return $this->successResponse(['message' => 'Campaign deleted successfully']);
        } catch (\Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to delete campaign', 500);
        }
    }

    /**
     * Get leads for a specific campaign.
     */
    public function campaignLeads(Request $request, MarketingCampaign $campaign): JsonResponse
    {
        try {
            $perPage = $this->resolvePerPage($request->get('per_page'));

            $query = MarketingLead::query()
                ->where('source_campaign_id', $campaign->id)
                ->with(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);

            if ($request->filled('status')) {
                $status = $this->normalizeLeadStatus($request->get('status'));
                if ($status) {
                    $query->where('status', $status);
                }
            }

            if ($request->filled('converted')) {
                $converted = $this->resolveBoolean($request->get('converted'));
                if ($converted === true) {
                    $query->whereNotNull('converted_patient_id');
                } elseif ($converted === false) {
                    $query->whereNull('converted_patient_id');
                }
            }

            $leads = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse([
                'leads' => MarketingLeadResource::collection($leads->getCollection()),
                'pagination' => [
                    'current_page' => $leads->currentPage(),
                    'last_page' => $leads->lastPage(),
                    'per_page' => $leads->perPage(),
                    'total' => $leads->total(),
                    'has_more' => $leads->hasMorePages(),
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to fetch campaign leads', 500);
        }
    }

    /**
     * Get all marketing leads with filters and pagination.
     */
    public function leads(Request $request): JsonResponse
    {
        try {
            $perPage = $this->resolvePerPage($request->get('per_page'));

            $query = MarketingLead::query()
                ->with(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);

            if ($request->filled('search')) {
                $search = trim((string) $request->get('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'ILIKE', "%{$search}%")
                        ->orWhere('last_name', 'ILIKE', "%{$search}%")
                        ->orWhereRaw("(first_name || ' ' || last_name) ILIKE ?", ["%{$search}%"])
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhere('phone', 'ILIKE', "%{$search}%")
                        ->orWhere('lead_code', 'ILIKE', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $status = $this->normalizeLeadStatus($request->get('status'));
                if ($status) {
                    $query->where('status', $status);
                }
            }

            if ($request->filled('campaign_id')) {
                $query->where('source_campaign_id', (int) $request->get('campaign_id'));
            }

            if ($request->filled('source')) {
                $source = trim((string) $request->get('source'));
                $query->where('utm_source', 'ILIKE', "%{$source}%");
            }

            if ($request->filled('converted')) {
                $converted = $this->resolveBoolean($request->get('converted'));
                if ($converted === true) {
                    $query->whereNotNull('converted_patient_id');
                } elseif ($converted === false) {
                    $query->whereNull('converted_patient_id');
                }
            }

            $leads = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse([
                'leads' => MarketingLeadResource::collection($leads->getCollection()),
                'pagination' => [
                    'current_page' => $leads->currentPage(),
                    'last_page' => $leads->lastPage(),
                    'per_page' => $leads->perPage(),
                    'total' => $leads->total(),
                    'has_more' => $leads->hasMorePages(),
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to fetch leads', 500);
        }
    }

    /**
     * Create a new marketing lead.
     */
    public function createLead(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'source_campaign_id' => 'nullable|exists:marketing_campaigns,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:25',
                'country' => 'nullable|string|max:100',
                'utm_source' => 'nullable|string|max:100',
                'utm_campaign' => 'nullable|string|max:100',
                'utm_medium' => 'nullable|string|max:100',
                'landing_page_id' => 'nullable|exists:landing_pages,id',
                'lead_score' => 'nullable|integer|min:0|max:100',
                'status' => 'required|in:New,Contacted,Qualified,Converted,Lost',
                'assigned_staff_id' => 'nullable|exists:staff,id',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $payload = $validator->validated();
            $payload['status'] = $this->normalizeLeadStatus($payload['status']);

            $lead = MarketingLead::create($payload);
            $lead->load(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);

            return $this->createdResponse([
                'lead' => new MarketingLeadResource($lead),
            ], 'Lead created successfully');
        } catch (\Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to create lead', 500);
        }
    }

    /**
     * Convert a marketing lead into a patient.
     */
    public function convertLead(Request $request, MarketingLead $lead): JsonResponse
    {
        try {
            if ($lead->converted_patient_id) {
                return $this->errorResponse('Lead is already converted to patient', 400);
            }

            DB::beginTransaction();

            $patientData = [
                'full_name' => trim($lead->first_name.' '.$lead->last_name),
                'email' => $lead->email,
                'phone_number' => $lead->phone,
                'source' => $lead->utm_source ?? 'marketing',
                'marketing_campaign_id' => $lead->source_campaign_id,
                'utm_source' => $lead->utm_source,
                'utm_campaign' => $lead->utm_campaign,
                'utm_medium' => $lead->utm_medium,
                'lead_id' => $lead->id,
                'status' => 'active',
                'acquisition_date' => now(),
            ];

            $patient = Patient::create($patientData);

            $lead->update([
                'converted_patient_id' => $patient->id,
                'conversion_date' => now(),
                'status' => 'Converted',
            ]);

            DB::commit();

            $lead->load(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);
            $patient->load(['marketingCampaign', 'lead']);

            return $this->successResponse([
                'lead' => new MarketingLeadResource($lead),
                'patient' => new PatientResource($patient),
                'message' => 'Lead converted to patient successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->errorResponse('Failed to convert lead', 500);
        }
    }

    /**
     * Get aggregated marketing analytics.
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $defaultStart = now()->subDays(30)->startOfDay();
            $defaultEnd = now()->endOfDay();

            $startDate = $this->resolveDate($request->get('start_date'), $defaultStart, false);
            $endDate = $this->resolveDate($request->get('end_date'), $defaultEnd, true);

            $totalCampaigns = MarketingCampaign::count();
            $activeCampaigns = MarketingCampaign::where('status', 'Active')->count();
            $pausedCampaigns = MarketingCampaign::where('status', 'Paused')->count();
            $completedCampaigns = MarketingCampaign::where('status', 'Completed')->count();

            $leadsQuery = MarketingLead::whereBetween('created_at', [$startDate, $endDate]);
            $totalLeads = (clone $leadsQuery)->count();
            $newLeads = (clone $leadsQuery)->where('status', 'New')->count();
            $convertedLeads = (clone $leadsQuery)->whereNotNull('converted_patient_id')->count();

            $analytics = [
                'campaigns' => [
                    'total' => $totalCampaigns,
                    'active' => $activeCampaigns,
                    'paused' => $pausedCampaigns,
                    'completed' => $completedCampaigns,
                ],
                'leads' => [
                    'total' => $totalLeads,
                    'new' => $newLeads,
                    'converted' => $convertedLeads,
                    'conversion_rate' => $this->calculateConversionRate($totalLeads, $convertedLeads),
                    'status_breakdown' => $this->getLeadStatusBreakdown($startDate, $endDate),
                ],
                'top_campaigns' => $this->getTopCampaigns($startDate, $endDate),
                'lead_sources' => $this->getLeadSources($startDate, $endDate),
                'monthly_trends' => $this->getMonthlyTrends(),
            ];

            return $this->successResponse($analytics);
        } catch (\Throwable $e) {
            report($e);
            return $this->errorResponse('Failed to fetch analytics', 500);
        }
    }

    private function resolvePerPage($perPage): int
    {
        $value = (int) ($perPage ?? 15);
        return $value > 0 ? $value : 15;
    }

    private function resolveBoolean($value): ?bool
    {
        if ($value === null) {
            return null;
        }

        $normalized = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return $normalized;
    }

    private function normalizeCampaignStatus($status): ?string
    {
        if ($status === null) {
            return null;
        }

        $key = strtolower((string) $status);
        return self::CAMPAIGN_STATUS_MAP[$key] ?? ucfirst($key);
    }

    private function normalizeLeadStatus($status): ?string
    {
        if ($status === null) {
            return null;
        }

        $key = strtolower((string) $status);
        return self::LEAD_STATUS_MAP[$key] ?? ucfirst($key);
    }

    private function resolveDate($date, Carbon $fallback, bool $endOfDay): Carbon
    {
        if (empty($date)) {
            return $endOfDay ? $fallback->copy()->endOfDay() : $fallback->copy()->startOfDay();
        }

        try {
            $parsed = Carbon::parse($date);
        } catch (\Throwable $e) {
            return $endOfDay ? $fallback->copy()->endOfDay() : $fallback->copy()->startOfDay();
        }

        return $endOfDay ? $parsed->copy()->endOfDay() : $parsed->copy()->startOfDay();
    }

    private function calculateConversionRate(int $totalLeads, int $convertedLeads): float
    {
        if ($totalLeads === 0) {
            return 0.0;
        }

        return round(($convertedLeads / $totalLeads) * 100, 2);
    }

    private function getLeadStatusBreakdown(Carbon $startDate, Carbon $endDate): array
    {
        return MarketingLead::selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->map(static function ($row) {
                return [
                    'status' => $row->status,
                    'count' => (int) $row->count,
                ];
            })
            ->toArray();
    }

    private function getTopCampaigns(Carbon $startDate, Carbon $endDate): array
    {
        return MarketingCampaign::query()
            ->withCount([
                'leads as leads_count' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                },
                'leads as converted_leads_count' => function ($q) use ($startDate, $endDate) {
                    $q->whereNotNull('converted_patient_id')
                        ->whereBetween('created_at', [$startDate, $endDate]);
                },
            ])
            ->orderByDesc('leads_count')
            ->limit(5)
            ->get(['id', 'campaign_name', 'campaign_code', 'status'])
            ->map(static function ($campaign) {
                $leadsCount = (int) ($campaign->leads_count ?? 0);
                $converted = (int) ($campaign->converted_leads_count ?? 0);

                return [
                    'id' => $campaign->id,
                    'campaign_name' => $campaign->campaign_name,
                    'campaign_code' => $campaign->campaign_code,
                    'status' => $campaign->status,
                    'leads_count' => $leadsCount,
                    'converted_leads_count' => $converted,
                    'conversion_rate' => $leadsCount > 0
                        ? round(($converted / $leadsCount) * 100, 2)
                        : 0,
                ];
            })
            ->toArray();
    }

    private function getLeadSources(Carbon $startDate, Carbon $endDate): array
    {
        return MarketingLead::selectRaw("COALESCE(NULLIF(utm_source, ''), 'Unknown') as source, COUNT(*) as count")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('source')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(static function ($row) {
                return [
                    'source' => $row->source,
                    'count' => (int) $row->count,
                ];
            })
            ->toArray();
    }

    private function getMonthlyTrends(): array
    {
        $start = now()->subMonths(11)->startOfMonth();

        return MarketingLead::selectRaw("DATE_TRUNC('month', created_at) as month, COUNT(*) as leads_count, SUM(CASE WHEN converted_patient_id IS NOT NULL THEN 1 ELSE 0 END) as conversions_count")
            ->where('created_at', '>=', $start)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(static function ($row) {
                $month = $row->month instanceof Carbon
                    ? $row->month
                    : Carbon::parse($row->month);

                return [
                    'month' => $month->format('Y-m'),
                    'leads_count' => (int) $row->leads_count,
                    'conversions_count' => (int) $row->conversions_count,
                ];
            })
            ->toArray();
    }
}
