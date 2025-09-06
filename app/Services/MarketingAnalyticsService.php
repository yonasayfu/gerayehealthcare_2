<?php

namespace App\Services;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Traits\ExportableTrait;
use App\Models\CampaignMetric;
use App\Models\MarketingBudget;
use App\Models\MarketingCampaign;
use App\Models\MarketingLead;
use App\Models\MarketingRoiView;
use App\Models\MarketingTask;
use App\Models\Patient;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketingAnalyticsService
{
    use ExportableTrait;

    public function getDashboardData(Request $request): array
    {
        // Totals (optionally filtered by campaign, platform, and date range)
        // Apply range and filters consistently
        $filteredLeadsQuery = $this->getFilteredLeadsQuery($request);
        $totalLeads = (int) (clone $filteredLeadsQuery)->count();

        // True conversions: leads linked to patients
        $convertedLeadsByPatientLink = (int) (clone $filteredLeadsQuery)->whereNotNull('converted_patient_id')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeadsByPatientLink / $totalLeads) * 100, 2) : 0;

        // Build filtered queries for cross-module consistency
        $filteredMetricQuery = $this->getFilteredCampaignMetricsQuery($request);
        $filteredCampaignIds = $this->getFilteredCampaignIds($request);

        // Use unified ROI series (MarketingRoiView) for spend/revenue/roi to ensure single source of truth with reports
        $roiSeries = $this->getRoiSeries($request);
        $totalMarketingSpend = (float) ($roiSeries['totals']['spend'] ?? 0);

        // Patients acquired through marketing (filter by campaign and acquisition_date window)
        $patientsQuery = Patient::whereNotNull('marketing_campaign_id');
        if (! empty($filteredCampaignIds)) {
            $patientsQuery->whereIn('marketing_campaign_id', $filteredCampaignIds);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $patientsQuery->whereBetween('acquisition_date', [
                $request->input('start_date'),
                $request->input('end_date'),
            ]);
        }
        $patientsAcquired = (int) $patientsQuery->count();
        $cpa = $patientsAcquired > 0 ? round($totalMarketingSpend / $patientsAcquired, 2) : 0;

        // Revenue generated and ROI from unified series
        $revenueGenerated = (float) ($roiSeries['totals']['revenue'] ?? 0);
        $roi = (float) ($roiSeries['totals']['roi_percent'] ?? 0);

        // Time-series campaign performance
        $campaignPerformanceData = $this->getFilteredCampaignMetricsQuery($request)
            ->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(conversions) as conversions, SUM(revenue_generated) as revenue_generated, SUM(cost_per_click * clicks) as total_cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Leads by traffic source (apply filters)
        $trafficSourceData = $this->getFilteredLeadsQuery($request)
            ->selectRaw('utm_source, COUNT(*) as lead_count')
            ->groupBy('utm_source')
            ->get();

        // Funnel counts by status (apply filters)
        $filteredLeads = $this->getFilteredLeadsQuery($request);
        $newLeads = (int) (clone $filteredLeads)->where('status', 'New')->count();
        $contactedLeads = (int) (clone $filteredLeads)->where('status', 'Contacted')->count();
        $qualifiedLeads = (int) (clone $filteredLeads)->where('status', 'Qualified')->count();
        $convertedLeadsByStatus = (int) (clone $filteredLeads)->where('status', 'Converted')->count();

        $conversionFunnelData = [
            'New' => $newLeads,
            'Contacted' => $contactedLeads,
            'Qualified' => $qualifiedLeads,
            'Converted' => $convertedLeadsByStatus,
        ];

        return [
            'dashboardStats' => [
                'totalLeads' => $totalLeads,
                'convertedLeads' => $convertedLeadsByPatientLink,
                'convertedLeadsByStatus' => $convertedLeadsByStatus,
                'conversionRate' => $conversionRate,
                'totalMarketingSpend' => $totalMarketingSpend,
                'patientsAcquired' => $patientsAcquired,
                'cpa' => $cpa,
                'revenueGenerated' => $revenueGenerated,
                'roi' => $roi,
            ],
            'campaignPerformanceData' => $campaignPerformanceData,
            'trafficSourceData' => $trafficSourceData,
            'conversionFunnelData' => $conversionFunnelData,
        ];
    }

    /**
     * Unified ROI series from MarketingRoiView with simple caching.
     * Returns ['series' => [...], 'totals' => ['revenue' => float, 'spend' => float, 'roi_percent' => float]]
     */
    public function getRoiSeries(Request $request): array
    {
        $cacheKey = 'svc:roi:'.md5(json_encode([
            'from' => $request->input('date_from'),
            'to' => $request->input('date_to'),
            'granularity' => $request->input('granularity'),
            // Support both platform and platform_id for flexibility
            'platform' => $request->input('platform') ?? $request->input('platform_id'),
        ]));

        $rows = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $q = MarketingRoiView::query();
            if ($request->filled('date_from')) {
                $q->whereDate('bucket_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $q->whereDate('bucket_date', '<=', $request->input('date_to'));
            }
            if ($request->filled('granularity')) {
                $q->where('granularity', $request->input('granularity'));
            }
            // Allow filtering by platform string or id if present in the view
            if ($request->filled('platform')) {
                $q->where('platform', $request->input('platform'));
            }

            return $q->orderBy('bucket_date')->get();
        });

        $revenue = (float) $rows->sum('revenue_generated');
        $spend = (float) $rows->sum('spend');
        $roiPercent = $spend > 0 ? round((($revenue - $spend) / $spend) * 100, 2) : 0.0;

        return [
            'series' => $rows->toArray(),
            'totals' => [
                'revenue' => $revenue,
                'spend' => $spend,
                'roi_percent' => $roiPercent,
            ],
        ];
    }

    public function getCampaignPerformance(Request $request)
    {
        $query = CampaignMetric::query();

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            $query->whereHas('campaign.platform', function ($q) use ($request) {
                $q->where('id', $request->input('platform_id'));
            });
        }
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->input('end_date'));
        }

        if ($request->filled('sort') && ! empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('date', 'asc');
        }

        return $query->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(conversions) as conversions, SUM(revenue_generated) as revenue_generated, SUM(cost_per_click * clicks) as total_cost')
            ->groupBy('date')
            ->paginate($request->input('per_page', 5))
            ->withQueryString();
    }

    public function getTrafficSourceDistribution(Request $request): array
    {
        return $this->getFilteredLeadsQuery($request)
            ->selectRaw('utm_source, COUNT(*) as lead_count')
            ->groupBy('utm_source')
            ->get()->toArray();
    }

    public function getConversionFunnel(Request $request): array
    {
        $q = $this->getFilteredLeadsQuery($request);
        $newLeads = (int) (clone $q)->where('status', 'New')->count();
        $contactedLeads = (int) (clone $q)->where('status', 'Contacted')->count();
        $qualifiedLeads = (int) (clone $q)->where('status', 'Qualified')->count();
        $convertedLeads = (int) (clone $q)->where('status', 'Converted')->count();

        return [
            'New' => $newLeads,
            'Contacted' => $contactedLeads,
            'Qualified' => $qualifiedLeads,
            'Converted' => $convertedLeads,
        ];
    }

    public function generateReport(Request $request)
    {
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($reportType === 'monthly_performance') {
            $campaigns = MarketingCampaign::with(['platform', 'campaignMetrics' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])->get();

            $pdf = Pdf::loadView('pdf.marketing.monthly_performance', [
                'campaigns' => $campaigns,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('monthly_marketing_performance_'.$startDate.'_'.$endDate.'.pdf');
        }

        return response()->json(['message' => 'Report generation initiated.', 'report_type' => $reportType]);
    }

    public function printAllCampaignPerformance(Request $request)
    {
        return $this->handlePrintAll($request, CampaignMetric::class, AdditionalExportConfigs::getCampaignMetricConfig());
    }

    public function printCurrentCampaignPerformance(Request $request)
    {
        return $this->handlePrintCurrent($request, CampaignMetric::class, AdditionalExportConfigs::getCampaignMetricConfig());
    }

    private function getFilteredCampaignMetricsQuery(Request $request)
    {
        $query = CampaignMetric::query();

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            $query->whereHas('campaign.platform', function ($q) use ($request) {
                $q->where('id', $request->input('platform_id'));
            });
        }
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->input('end_date'));
        }

        return $query;
    }

    private function getFilteredLeadsQuery(Request $request)
    {
        $query = MarketingLead::query();
        // Filter by campaign via source_campaign_id
        if ($request->filled('campaign_id')) {
            $query->where('source_campaign_id', $request->input('campaign_id'));
        }
        // Filter by platform via campaign relation
        if ($request->filled('platform_id')) {
            $query->whereHas('sourceCampaign', function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform_id'));
            });
        }
        // Date range uses created_at by default for leads
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        return $query;
    }

    private function getFilteredCampaignIds(Request $request): array
    {
        $campaigns = MarketingCampaign::query();
        if ($request->filled('campaign_id')) {
            $campaigns->where('id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            $campaigns->where('platform_id', $request->input('platform_id'));
        }

        return $campaigns->pluck('id')->all();
    }

    public function getBudgetPacing(Request $request): array
    {
        // Resolve date window
        $start = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $end = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        // Base budget query filtered by campaign/platform and overlapping the window
        $campaignIds = $this->getFilteredCampaignIds($request);
        $budgets = MarketingBudget::query();
        if (! empty($campaignIds)) {
            $budgets->whereIn('campaign_id', $campaignIds);
        }
        if ($request->filled('platform_id')) {
            $budgets->where('platform_id', $request->input('platform_id'));
        }
        // Overlap condition: budget period intersects [start, end]
        $budgets->whereDate('period_start', '<=', $end->toDateString())
            ->whereDate('period_end', '>=', $start->toDateString());

        $rows = $budgets->get(['allocated_amount', 'spent_amount', 'period_start', 'period_end']);

        // Aggregate by YYYY-MM within window
        $byMonth = [];
        foreach ($rows as $row) {
            $pStart = Carbon::parse($row->period_start);
            $pEnd = Carbon::parse($row->period_end);
            // Clip to selected window
            $clipStart = $pStart->greaterThan($start) ? $pStart : $start;
            $clipEnd = $pEnd->lessThan($end) ? $pEnd : $end;
            // Iterate months in clipped range
            $cursor = (clone $clipStart)->startOfMonth();
            while ($cursor <= $clipEnd) {
                $key = $cursor->format('Y-m');
                if (! isset($byMonth[$key])) {
                    $byMonth[$key] = [
                        'month' => $key,
                        'allocated' => 0.0,
                        'spent' => 0.0,
                        'projected_spend' => 0.0,
                        'overrun' => false,
                        'pacing' => 0.0,
                    ];
                }
                // Simple allocation: full amounts counted in overlapping months (can refine to pro‑rata if needed)
                $byMonth[$key]['allocated'] += (float) $row->allocated_amount;
                $byMonth[$key]['spent'] += (float) $row->spent_amount;
                $cursor->addMonth();
            }
        }

        // Compute pacing and projected spend for each month
        foreach ($byMonth as $key => &$m) {
            [$y, $mNum] = explode('-', $key);
            $monthStart = Carbon::createFromDate((int) $y, (int) $mNum, 1)->startOfDay();
            $monthEnd = (clone $monthStart)->endOfMonth();
            // Clip month to [start, end]
            $clipStart = $monthStart->greaterThan($start) ? $monthStart : $start;
            $clipEnd = $monthEnd->lessThan($end) ? $monthEnd : $end;
            $daysInWindow = $clipStart->diffInDays($clipEnd) + 1;
            $daysElapsed = $clipStart->isFuture() ? 0 : min($daysInWindow, $clipStart->diffInDays(min(Carbon::now()->endOfDay(), $clipEnd)) + 1);

            $elapsedRatio = $daysInWindow > 0 ? max(0, min(1, $daysElapsed / $daysInWindow)) : 0;
            if ($elapsedRatio > 0) {
                $m['projected_spend'] = round($m['spent'] / $elapsedRatio, 2);
            } else {
                $m['projected_spend'] = 0.0;
            }
            $m['pacing'] = $m['allocated'] > 0 ? round($m['projected_spend'] / $m['allocated'], 2) : 0.0;
            $m['overrun'] = $m['projected_spend'] > $m['allocated'];
        }
        ksort($byMonth);

        // Totals
        $totals = [
            'allocated' => array_sum(array_column($byMonth, 'allocated')),
            'spent' => array_sum(array_column($byMonth, 'spent')),
            'projected_spend' => array_sum(array_column($byMonth, 'projected_spend')),
        ];
        $totals['pacing'] = $totals['allocated'] > 0 ? round($totals['projected_spend'] / $totals['allocated'], 2) : 0.0;
        $totals['overrun'] = $totals['projected_spend'] > $totals['allocated'];

        return [
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'monthly' => array_values($byMonth),
            'totals' => $totals,
        ];
    }

    public function getStaffPerformance(Request $request): array
    {
        // Leads grouped by assigned staff
        $leadQuery = $this->getFilteredLeadsQuery($request);
        $leadRows = $leadQuery->selectRaw('assigned_staff_id as staff_id, COUNT(*) as total,
                          SUM(CASE WHEN status in ("Contacted","Qualified","Converted") THEN 1 ELSE 0 END) as contacted,
                          SUM(CASE WHEN status = "Qualified" THEN 1 ELSE 0 END) as qualified,
                          SUM(CASE WHEN status = "Converted" THEN 1 ELSE 0 END) as converted')
            ->groupBy('assigned_staff_id')
            ->get();

        // Tasks grouped by assigned staff
        $taskQuery = MarketingTask::query();
        if ($request->filled('campaign_id')) {
            $taskQuery->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            $taskQuery->whereHas('campaign', function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform_id'));
            });
        }
        if ($request->filled('start_date')) {
            $taskQuery->whereDate('scheduled_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $taskQuery->whereDate('scheduled_at', '<=', $request->input('end_date'));
        }
        $now = Carbon::now();
        $taskRows = $taskQuery->selectRaw('assigned_to_staff_id as staff_id,
                            COUNT(*) as tasks_total,
                            SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) as tasks_completed,
                            SUM(CASE WHEN status = "Completed" AND completed_at <= scheduled_at THEN 1 ELSE 0 END) as tasks_on_time,
                            SUM(CASE WHEN status <> "Completed" AND scheduled_at < ? THEN 1 ELSE 0 END) as tasks_overdue_open,
                            SUM(CASE WHEN status = "Completed" AND completed_at > scheduled_at THEN 1 ELSE 0 END) as tasks_overdue_completed',
            [$now])
            ->groupBy('assigned_to_staff_id')
            ->get();

        // Merge by staff_id
        $byStaff = [];
        foreach ($leadRows as $r) {
            $byStaff[$r->staff_id] = [
                'staff_id' => $r->staff_id,
                'leads' => [
                    'total' => (int) $r->total,
                    'contacted' => (int) $r->contacted,
                    'qualified' => (int) $r->qualified,
                    'converted' => (int) $r->converted,
                    'contact_rate' => $r->total > 0 ? round($r->contacted / $r->total, 2) : 0,
                    'qualification_rate' => $r->total > 0 ? round($r->qualified / $r->total, 2) : 0,
                    'conversion_rate' => $r->total > 0 ? round($r->converted / $r->total, 2) : 0,
                ],
                'tasks' => [
                    'tasks_total' => 0,
                    'tasks_completed' => 0,
                    'tasks_on_time' => 0,
                    'tasks_overdue_open' => 0,
                    'tasks_overdue_completed' => 0,
                    'on_time_rate' => 0,
                ],
            ];
        }
        foreach ($taskRows as $t) {
            if (! isset($byStaff[$t->staff_id])) {
                $byStaff[$t->staff_id] = [
                    'staff_id' => $t->staff_id,
                    'leads' => [
                        'total' => 0,
                        'contacted' => 0,
                        'qualified' => 0,
                        'converted' => 0,
                        'contact_rate' => 0,
                        'qualification_rate' => 0,
                        'conversion_rate' => 0,
                    ],
                    'tasks' => [
                        'tasks_total' => 0,
                        'tasks_completed' => 0,
                        'tasks_on_time' => 0,
                        'tasks_overdue_open' => 0,
                        'tasks_overdue_completed' => 0,
                        'on_time_rate' => 0,
                    ],
                ];
            }
            $byStaff[$t->staff_id]['tasks']['tasks_total'] = (int) $t->tasks_total;
            $byStaff[$t->staff_id]['tasks']['tasks_completed'] = (int) $t->tasks_completed;
            $byStaff[$t->staff_id]['tasks']['tasks_on_time'] = (int) $t->tasks_on_time;
            $byStaff[$t->staff_id]['tasks']['tasks_overdue_open'] = (int) $t->tasks_overdue_open;
            $byStaff[$t->staff_id]['tasks']['tasks_overdue_completed'] = (int) $t->tasks_overdue_completed;
            $byStaff[$t->staff_id]['tasks']['on_time_rate'] = $t->tasks_completed > 0 ? round($t->tasks_on_time / $t->tasks_completed, 2) : 0;
        }

        // Enrich with staff names
        $staffIds = array_values(array_filter(array_keys($byStaff), function ($id) {
            return ! is_null($id);
        }));
        $nameMap = [];
        if (! empty($staffIds)) {
            $nameMap = Staff::whereIn('id', $staffIds)->get()->mapWithKeys(function ($s) {
                return [$s->id => ($s->full_name ?? ($s->first_name.' '.$s->last_name))];
            })->toArray();
        }
        foreach ($byStaff as $id => &$row) {
            if (is_null($id)) {
                $row['staff'] = ['id' => null, 'name' => 'Unassigned'];
            } else {
                $row['staff'] = ['id' => $id, 'name' => $nameMap[$id] ?? ('Staff #'.$id)];
            }
        }

        // Include all staff with zero metrics to ensure table visibility
        $allStaff = Staff::select('id', 'first_name', 'last_name')->get();
        foreach ($allStaff as $s) {
            if (! isset($byStaff[$s->id])) {
                $fullName = trim(($s->first_name ?? '').' '.($s->last_name ?? ''));
                $byStaff[$s->id] = [
                    'staff_id' => $s->id,
                    'staff' => ['id' => $s->id, 'name' => $fullName !== '' ? $fullName : ('Staff #'.$s->id)],
                    'leads' => [
                        'total' => 0,
                        'contacted' => 0,
                        'qualified' => 0,
                        'converted' => 0,
                        'contact_rate' => 0,
                        'qualification_rate' => 0,
                        'conversion_rate' => 0,
                    ],
                    'tasks' => [
                        'tasks_total' => 0,
                        'tasks_completed' => 0,
                        'tasks_on_time' => 0,
                        'tasks_overdue_open' => 0,
                        'tasks_overdue_completed' => 0,
                        'on_time_rate' => 0,
                    ],
                ];
            }
        }

        // Return list
        return array_values($byStaff);
    }

    public function getTaskSla(Request $request): array
    {
        $query = MarketingTask::query();
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            $query->whereHas('campaign', function ($q) use ($request) {
                $q->where('platform_id', $request->input('platform_id'));
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('scheduled_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('scheduled_at', '<=', $request->input('end_date'));
        }
        $now = Carbon::now();

        $totals = $query->selectRaw('COUNT(*) as total,
                        SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN status = "Completed" AND completed_at <= scheduled_at THEN 1 ELSE 0 END) as on_time,
                        SUM(CASE WHEN status <> "Completed" AND scheduled_at < ? THEN 1 ELSE 0 END) as overdue_open,
                        SUM(CASE WHEN status = "Completed" AND completed_at > scheduled_at THEN 1 ELSE 0 END) as overdue_completed',
            [$now])
            ->first();

        return [
            'total' => (int) $totals->total,
            'completed' => (int) $totals->completed,
            'on_time' => (int) $totals->on_time,
            'overdue_open' => (int) $totals->overdue_open,
            'overdue_completed' => (int) $totals->overdue_completed,
            'on_time_rate' => $totals->completed > 0 ? round($totals->on_time / $totals->completed, 2) : 0,
        ];
    }
}
