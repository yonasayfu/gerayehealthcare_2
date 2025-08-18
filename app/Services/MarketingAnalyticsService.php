<?php

namespace App\Services;

use App\Models\MarketingLead;
use App\Models\MarketingCampaign;
use App\Models\CampaignMetric;
use App\Models\MarketingBudget;
use App\Models\Patient;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;

class MarketingAnalyticsService
{
    use ExportableTrait;

    public function getDashboardData(Request $request): array
    {
        // Totals (optionally filtered by campaign, platform, and date range)
        $totalLeads = MarketingLead::count();

        // True conversions: leads linked to patients
        $convertedLeadsByPatientLink = MarketingLead::whereNotNull('converted_patient_id')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeadsByPatientLink / $totalLeads) * 100, 2) : 0;

        // Build filtered queries for cross-module consistency
        $filteredMetricQuery = $this->getFilteredCampaignMetricsQuery($request);
        $filteredCampaignIds = $this->getFilteredCampaignIds($request);

        // Marketing spend from budgets (filtered by campaign/platform and date overlap if provided)
        $budgetQuery = MarketingBudget::query();
        if (!empty($filteredCampaignIds)) {
            $budgetQuery->whereIn('campaign_id', $filteredCampaignIds);
        }
        if ($request->filled('platform_id')) {
            $budgetQuery->where('platform_id', $request->input('platform_id'));
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = $request->input('start_date');
            $end = $request->input('end_date');
            // budgets overlapping the selected window
            $budgetQuery->whereDate('period_start', '<=', $end)
                        ->whereDate('period_end', '>=', $start);
        }
        $totalMarketingSpend = (float) $budgetQuery->sum('spent_amount');

        // Patients acquired through marketing (filter by campaign and acquisition_date window)
        $patientsQuery = Patient::whereNotNull('marketing_campaign_id');
        if (!empty($filteredCampaignIds)) {
            $patientsQuery->whereIn('marketing_campaign_id', $filteredCampaignIds);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $patientsQuery->whereBetween('acquisition_date', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        }
        $patientsAcquired = (int) $patientsQuery->count();
        $cpa = $patientsAcquired > 0 ? round($totalMarketingSpend / $patientsAcquired, 2) : 0;

        // Revenue generated (filtered)
        $revenueGenerated = (float) $filteredMetricQuery->clone()->sum('revenue_generated');
        $roi = $totalMarketingSpend > 0 ? round((($revenueGenerated - $totalMarketingSpend) / $totalMarketingSpend) * 100, 2) : 0;

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
        $newLeads = (clone $filteredLeads)->where('status', 'New')->count();
        $contactedLeads = (clone $filteredLeads)->where('status', 'Contacted')->count();
        $qualifiedLeads = (clone $filteredLeads)->where('status', 'Qualified')->count();
        $convertedLeadsByStatus = (clone $filteredLeads)->where('status', 'Converted')->count();

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

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('date', 'asc');
        }

        return $query->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(conversions) as conversions, SUM(revenue_generated) as revenue_generated, SUM(cost_per_click * clicks) as total_cost')
                     ->groupBy('date')
                     ->paginate($request->input('per_page', 5))
                     ->withQueryString();
    }

    public function getTrafficSourceDistribution(): array
    {
        return MarketingLead::selectRaw('utm_source, COUNT(*) as lead_count')
                           ->groupBy('utm_source')
                           ->get()->toArray();
    }

    public function getConversionFunnel(): array
    {
        $newLeads = MarketingLead::where('status', 'New')->count();
        $contactedLeads = MarketingLead::where('status', 'Contacted')->count();
        $qualifiedLeads = MarketingLead::where('status', 'Qualified')->count();
        $convertedLeads = MarketingLead::where('status', 'Converted')->count();

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
            $campaigns = MarketingCampaign::with(['platform', 'campaignMetrics' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])->get();

            $pdf = Pdf::loadView('pdf.marketing.monthly_performance', [
                'campaigns' => $campaigns,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('monthly_marketing_performance_' . $startDate . '_' . $endDate . '.pdf');
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
}