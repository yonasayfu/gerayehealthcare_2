<?php

namespace App\Services;

use App\Http\Config\AdditionalExportConfigs;
use App\Http\Traits\ExportableTrait;
use App\Models\CampaignMetric;
use App\Models\MarketingBudget;
use App\Models\MarketingCampaign;
use App\Models\MarketingLead;
use App\Models\MarketingTask;
use App\Models\Patient;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedMarketingAnalyticsService extends OptimizedBaseService
{
    use ExportableTrait;

    protected $cacheTtl = 1800; // 30 minutes for analytics data

    protected $shortCacheTtl = 600; // 10 minutes for real-time metrics

    protected $chunkSize = 1000; // Process large datasets in chunks

    public function __construct()
    {
        // Set base model for cache key generation
        $this->model = new MarketingLead;
    }

    public function getDashboardData(Request $request): array
    {
        $cacheKey = $this->generateCacheKey('dashboard', $request->all());

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request) {
            // Use DB transactions for consistency and optimize queries
            return DB::transaction(function () use ($request) {

                // Parallel query execution for independent metrics
                $totalsData = $this->calculateTotalsWithChunking($request);
                $filteredMetrics = $this->getFilteredCampaignMetricsOptimized($request);
                $trafficSourceData = $this->getTrafficSourceDistributionOptimized($request);
                $conversionFunnelData = $this->getConversionFunnelOptimized($request);
                $campaignPerformanceData = $this->getCampaignPerformanceOptimized($request);

                return [
                    'dashboardStats' => $totalsData,
                    'campaignPerformanceData' => $campaignPerformanceData,
                    'trafficSourceData' => $trafficSourceData,
                    'conversionFunnelData' => $conversionFunnelData,
                ];
            });
        });
    }

    /**
     * Calculate dashboard totals using chunking for large datasets
     */
    protected function calculateTotalsWithChunking(Request $request): array
    {
        $totalLeads = 0;
        $convertedLeadsCount = 0;
        $totalMarketingSpend = 0.0;
        $patientsAcquired = 0;
        $revenueGenerated = 0.0;

        // Process leads in chunks to avoid memory issues
        MarketingLead::when($request->filled('start_date'), function ($query) use ($request) {
            return $query->whereDate('created_at', '>=', $request->input('start_date'));
        })
            ->when($request->filled('end_date'), function ($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->input('end_date'));
            })
            ->when($request->filled('campaign_id'), function ($query) use ($request) {
                return $query->where('source_campaign_id', $request->input('campaign_id'));
            })
            ->chunk($this->chunkSize, function ($leads) use (&$totalLeads, &$convertedLeadsCount) {
                $totalLeads += $leads->count();
                $convertedLeadsCount += $leads->whereNotNull('converted_patient_id')->count();
            });

        // Optimized budget calculation with single query
        $filteredCampaignIds = $this->getFilteredCampaignIds($request);
        $budgetData = $this->calculateMarketingSpendOptimized($request, $filteredCampaignIds);
        $totalMarketingSpend = $budgetData['total_spend'];

        // Patients acquired calculation with single query
        $patientsData = $this->calculatePatientsAcquiredOptimized($request, $filteredCampaignIds);
        $patientsAcquired = $patientsData['count'];

        // Revenue calculation with aggregation
        $revenueData = $this->calculateRevenueOptimized($request);
        $revenueGenerated = $revenueData['total_revenue'];

        // Calculate derived metrics
        $conversionRate = $totalLeads > 0 ? round(($convertedLeadsCount / $totalLeads) * 100, 2) : 0;
        $cpa = $patientsAcquired > 0 ? round($totalMarketingSpend / $patientsAcquired, 2) : 0;
        $roi = $totalMarketingSpend > 0 ? round((($revenueGenerated - $totalMarketingSpend) / $totalMarketingSpend) * 100, 2) : 0;

        return [
            'totalLeads' => $totalLeads,
            'convertedLeads' => $convertedLeadsCount,
            'conversionRate' => $conversionRate,
            'totalMarketingSpend' => $totalMarketingSpend,
            'patientsAcquired' => $patientsAcquired,
            'cpa' => $cpa,
            'revenueGenerated' => $revenueGenerated,
            'roi' => $roi,
        ];
    }

    /**
     * Optimized marketing spend calculation
     */
    protected function calculateMarketingSpendOptimized(Request $request, array $filteredCampaignIds): array
    {
        $query = MarketingBudget::query();

        if (! empty($filteredCampaignIds)) {
            $query->whereIn('campaign_id', $filteredCampaignIds);
        }

        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->input('platform_id'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = $request->input('start_date');
            $end = $request->input('end_date');
            $query->whereDate('period_start', '<=', $end)
                ->whereDate('period_end', '>=', $start);
        }

        return [
            'total_spend' => (float) $query->sum('spent_amount'),
            'total_allocated' => (float) $query->sum('allocated_amount'),
        ];
    }

    /**
     * Optimized patients acquired calculation
     */
    protected function calculatePatientsAcquiredOptimized(Request $request, array $filteredCampaignIds): array
    {
        $query = Patient::whereNotNull('marketing_campaign_id');

        if (! empty($filteredCampaignIds)) {
            $query->whereIn('marketing_campaign_id', $filteredCampaignIds);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('acquisition_date', [
                $request->input('start_date'),
                $request->input('end_date'),
            ]);
        }

        return [
            'count' => $query->count(),
            'total_value' => $query->sum('lifetime_value'), // If column exists
        ];
    }

    /**
     * Optimized revenue calculation
     */
    protected function calculateRevenueOptimized(Request $request): array
    {
        $query = $this->getFilteredCampaignMetricsOptimized($request);

        return [
            'total_revenue' => (float) $query->sum('revenue_generated'),
            'avg_revenue_per_metric' => (float) $query->avg('revenue_generated'),
        ];
    }

    /**
     * Optimized campaign performance with chunking
     */
    protected function getCampaignPerformanceOptimized(Request $request): array
    {
        $cacheKey = $this->generateCacheKey('campaign_performance', $request->only(['campaign_id', 'platform_id', 'start_date', 'end_date']));

        return Cache::remember($cacheKey, $this->shortCacheTtl, function () use ($request) {
            return $this->getFilteredCampaignMetricsOptimized($request)
                ->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(conversions) as conversions, SUM(revenue_generated) as revenue_generated, SUM(cost_per_click * clicks) as total_cost')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();
        });
    }

    /**
     * Optimized traffic source distribution
     */
    protected function getTrafficSourceDistributionOptimized(Request $request): array
    {
        $cacheKey = $this->generateCacheKey('traffic_sources', $request->only(['campaign_id', 'platform_id', 'start_date', 'end_date']));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request) {
            return $this->getFilteredLeadsQuery($request)
                ->selectRaw('utm_source, COUNT(*) as lead_count')
                ->groupBy('utm_source')
                ->orderByDesc('lead_count')
                ->get()
                ->toArray();
        });
    }

    /**
     * Optimized conversion funnel
     */
    protected function getConversionFunnelOptimized(Request $request): array
    {
        $cacheKey = $this->generateCacheKey('conversion_funnel', $request->only(['campaign_id', 'platform_id', 'start_date', 'end_date']));

        return Cache::remember($cacheKey, $this->shortCacheTtl, function () use ($request) {
            $results = $this->getFilteredLeadsQuery($request)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();

            return [
                'New' => $results['New'] ?? 0,
                'Contacted' => $results['Contacted'] ?? 0,
                'Qualified' => $results['Qualified'] ?? 0,
                'Converted' => $results['Converted'] ?? 0,
            ];
        });
    }

    public function getCampaignPerformance(Request $request)
    {
        $cacheKey = $this->generateCacheKey('campaign_performance_paginated', $request->all());

        return Cache::remember($cacheKey, $this->shortCacheTtl, function () use ($request) {
            $query = $this->getFilteredCampaignMetricsOptimized($request);

            if ($request->filled('sort') && ! empty($request->input('sort'))) {
                $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
            } else {
                $query->orderBy('date', 'asc');
            }

            return $query->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(conversions) as conversions, SUM(revenue_generated) as revenue_generated, SUM(cost_per_click * clicks) as total_cost')
                ->groupBy('date')
                ->paginate($request->input('per_page', 5))
                ->withQueryString();
        });
    }

    public function getBudgetPacing(Request $request): array
    {
        $cacheKey = $this->generateCacheKey('budget_pacing', $request->all());

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request) {
            // Resolve date window
            $start = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
            $end = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            // Optimized budget query with single database hit
            $campaignIds = $this->getFilteredCampaignIds($request);
            $budgets = MarketingBudget::query()
                ->when(! empty($campaignIds), function ($query) use ($campaignIds) {
                    return $query->whereIn('campaign_id', $campaignIds);
                })
                ->when($request->filled('platform_id'), function ($query) use ($request) {
                    return $query->where('platform_id', $request->input('platform_id'));
                })
                ->whereDate('period_start', '<=', $end->toDateString())
                ->whereDate('period_end', '>=', $start->toDateString())
                ->select(['allocated_amount', 'spent_amount', 'period_start', 'period_end'])
                ->get();

            return $this->processBudgetPacing($budgets, $start, $end);
        });
    }

    /**
     * Process budget pacing data efficiently
     */
    protected function processBudgetPacing($budgets, Carbon $start, Carbon $end): array
    {
        $byMonth = [];

        foreach ($budgets as $row) {
            $pStart = Carbon::parse($row->period_start);
            $pEnd = Carbon::parse($row->period_end);
            $clipStart = $pStart->greaterThan($start) ? $pStart : $start;
            $clipEnd = $pEnd->lessThan($end) ? $pEnd : $end;

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

                $byMonth[$key]['allocated'] += (float) $row->allocated_amount;
                $byMonth[$key]['spent'] += (float) $row->spent_amount;
                $cursor->addMonth();
            }
        }

        // Calculate pacing metrics
        foreach ($byMonth as $key => &$m) {
            [$y, $mNum] = explode('-', $key);
            $monthStart = Carbon::createFromDate((int) $y, (int) $mNum, 1)->startOfDay();
            $monthEnd = (clone $monthStart)->endOfMonth();
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

        // Calculate totals
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
        $cacheKey = $this->generateCacheKey('staff_performance', $request->all());

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request) {
            // Optimized staff performance with single queries
            $leadData = $this->getStaffLeadPerformance($request);
            $taskData = $this->getStaffTaskPerformance($request);

            return $this->mergeStaffPerformanceData($leadData, $taskData);
        });
    }

    /**
     * Get staff lead performance data optimized
     */
    protected function getStaffLeadPerformance(Request $request): array
    {
        return $this->getFilteredLeadsQuery($request)
            ->selectRaw('assigned_staff_id as staff_id, COUNT(*) as total,
                      SUM(CASE WHEN status in ("Contacted","Qualified","Converted") THEN 1 ELSE 0 END) as contacted,
                      SUM(CASE WHEN status = "Qualified" THEN 1 ELSE 0 END) as qualified,
                      SUM(CASE WHEN status = "Converted" THEN 1 ELSE 0 END) as converted')
            ->groupBy('assigned_staff_id')
            ->get()
            ->keyBy('staff_id')
            ->toArray();
    }

    /**
     * Get staff task performance data optimized
     */
    protected function getStaffTaskPerformance(Request $request): array
    {
        $now = Carbon::now();
        $query = MarketingTask::query()
            ->when($request->filled('campaign_id'), function ($q) use ($request) {
                return $q->where('campaign_id', $request->input('campaign_id'));
            })
            ->when($request->filled('platform_id'), function ($q) use ($request) {
                return $q->whereHas('campaign', function ($subQ) use ($request) {
                    $subQ->where('platform_id', $request->input('platform_id'));
                });
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                return $q->whereDate('scheduled_at', '>=', $request->input('start_date'));
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                return $q->whereDate('scheduled_at', '<=', $request->input('end_date'));
            });

        return $query->selectRaw('assigned_to_staff_id as staff_id,
                        COUNT(*) as tasks_total,
                        SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) as tasks_completed,
                        SUM(CASE WHEN status = "Completed" AND completed_at <= scheduled_at THEN 1 ELSE 0 END) as tasks_on_time,
                        SUM(CASE WHEN status <> "Completed" AND scheduled_at < ? THEN 1 ELSE 0 END) as tasks_overdue_open,
                        SUM(CASE WHEN status = "Completed" AND completed_at > scheduled_at THEN 1 ELSE 0 END) as tasks_overdue_completed',
            [$now])
            ->groupBy('assigned_to_staff_id')
            ->get()
            ->keyBy('staff_id')
            ->toArray();
    }

    /**
     * Merge staff performance data efficiently
     */
    protected function mergeStaffPerformanceData(array $leadData, array $taskData): array
    {
        $staffIds = array_unique(array_merge(array_keys($leadData), array_keys($taskData)));

        // Get staff names in single query
        $staffNames = Staff::whereIn('id', $staffIds)
            ->select('id', 'first_name', 'last_name')
            ->get()
            ->mapWithKeys(function ($staff) {
                $fullName = trim(($staff->first_name ?? '').' '.($staff->last_name ?? ''));

                return [$staff->id => $fullName ?: "Staff #{$staff->id}"];
            })
            ->toArray();

        $result = [];

        foreach ($staffIds as $staffId) {
            $leadStats = $leadData[$staffId] ?? [];
            $taskStats = $taskData[$staffId] ?? [];

            $total = $leadStats['total'] ?? 0;
            $contacted = $leadStats['contacted'] ?? 0;
            $qualified = $leadStats['qualified'] ?? 0;
            $converted = $leadStats['converted'] ?? 0;

            $tasksTotal = $taskStats['tasks_total'] ?? 0;
            $tasksCompleted = $taskStats['tasks_completed'] ?? 0;
            $tasksOnTime = $taskStats['tasks_on_time'] ?? 0;

            $result[] = [
                'staff_id' => $staffId,
                'staff' => [
                    'id' => $staffId,
                    'name' => $staffNames[$staffId] ?? ($staffId ? "Staff #{$staffId}" : 'Unassigned'),
                ],
                'leads' => [
                    'total' => (int) $total,
                    'contacted' => (int) $contacted,
                    'qualified' => (int) $qualified,
                    'converted' => (int) $converted,
                    'contact_rate' => $total > 0 ? round($contacted / $total, 2) : 0,
                    'qualification_rate' => $total > 0 ? round($qualified / $total, 2) : 0,
                    'conversion_rate' => $total > 0 ? round($converted / $total, 2) : 0,
                ],
                'tasks' => [
                    'tasks_total' => (int) $tasksTotal,
                    'tasks_completed' => (int) $tasksCompleted,
                    'tasks_on_time' => (int) $tasksOnTime,
                    'tasks_overdue_open' => (int) ($taskStats['tasks_overdue_open'] ?? 0),
                    'tasks_overdue_completed' => (int) ($taskStats['tasks_overdue_completed'] ?? 0),
                    'on_time_rate' => $tasksCompleted > 0 ? round($tasksOnTime / $tasksCompleted, 2) : 0,
                ],
            ];
        }

        return $result;
    }

    public function getTaskSla(Request $request): array
    {
        $cacheKey = $this->generateCacheKey('task_sla', $request->all());

        return Cache::remember($cacheKey, $this->shortCacheTtl, function () use ($request) {
            $now = Carbon::now();

            $totals = MarketingTask::query()
                ->when($request->filled('campaign_id'), function ($q) use ($request) {
                    return $q->where('campaign_id', $request->input('campaign_id'));
                })
                ->when($request->filled('platform_id'), function ($q) use ($request) {
                    return $q->whereHas('campaign', function ($subQ) use ($request) {
                        $subQ->where('platform_id', $request->input('platform_id'));
                    });
                })
                ->when($request->filled('start_date'), function ($q) use ($request) {
                    return $q->whereDate('scheduled_at', '>=', $request->input('start_date'));
                })
                ->when($request->filled('end_date'), function ($q) use ($request) {
                    return $q->whereDate('scheduled_at', '<=', $request->input('end_date'));
                })
                ->selectRaw('COUNT(*) as total,
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
        });
    }

    /**
     * Clear analytics caches when data is updated
     */
    public function clearAnalyticsCaches(): void
    {
        $patterns = [
            'analytics_*',
            'dashboard_*',
            'campaign_performance_*',
            'traffic_sources_*',
            'conversion_funnel_*',
            'budget_pacing_*',
            'staff_performance_*',
            'task_sla_*',
        ];

        foreach ($patterns as $pattern) {
            $this->clearCachePattern($pattern);
        }
    }

    // Optimized helper methods
    protected function getFilteredCampaignMetricsOptimized(Request $request)
    {
        return CampaignMetric::query()
            ->when($request->filled('campaign_id'), function ($q) use ($request) {
                return $q->where('campaign_id', $request->input('campaign_id'));
            })
            ->when($request->filled('platform_id'), function ($q) use ($request) {
                return $q->whereHas('campaign.platform', function ($subQ) use ($request) {
                    $subQ->where('id', $request->input('platform_id'));
                });
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                return $q->where('date', '>=', $request->input('start_date'));
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                return $q->where('date', '<=', $request->input('end_date'));
            });
    }

    protected function getFilteredLeadsQuery(Request $request)
    {
        return MarketingLead::query()
            ->when($request->filled('campaign_id'), function ($q) use ($request) {
                return $q->where('source_campaign_id', $request->input('campaign_id'));
            })
            ->when($request->filled('platform_id'), function ($q) use ($request) {
                return $q->whereHas('sourceCampaign', function ($subQ) use ($request) {
                    $subQ->where('platform_id', $request->input('platform_id'));
                });
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->input('start_date'));
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->input('end_date'));
            });
    }

    protected function getFilteredCampaignIds(Request $request): array
    {
        return MarketingCampaign::query()
            ->when($request->filled('campaign_id'), function ($q) use ($request) {
                return $q->where('id', $request->input('campaign_id'));
            })
            ->when($request->filled('platform_id'), function ($q) use ($request) {
                return $q->where('platform_id', $request->input('platform_id'));
            })
            ->pluck('id')
            ->toArray();
    }

    // Export and PDF methods remain the same
    public function generateReport(Request $request)
    {
        return $this->generateReportOptimized($request);
    }

    protected function generateReportOptimized(Request $request)
    {
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($reportType === 'monthly_performance') {
            // Use chunking for large campaign datasets
            $campaigns = MarketingCampaign::with(['platform'])
                ->with(['campaignMetrics' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }])
                ->chunk(100, function ($campaignChunk) {
                    // Process campaigns in chunks for memory efficiency
                    return $campaignChunk;
                });

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
}
