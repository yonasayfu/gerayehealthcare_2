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
        $totalLeads = MarketingLead::count();
        $convertedLeads = MarketingLead::whereNotNull('converted_patient_id')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;

        $totalMarketingSpend = MarketingBudget::sum('spent_amount');
        $patientsAcquired = Patient::whereNotNull('marketing_campaign_id')->count();
        $cpa = $patientsAcquired > 0 ? round($totalMarketingSpend / $patientsAcquired, 2) : 0;

        $revenueGenerated = CampaignMetric::sum('revenue_generated');
        $roi = $totalMarketingSpend > 0 ? round((($revenueGenerated - $totalMarketingSpend) / $totalMarketingSpend) * 100, 2) : 0;

        $campaignPerformanceData = $this->getFilteredCampaignMetricsQuery($request)
                                        ->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(conversions) as conversions, SUM(revenue_generated) as revenue_generated, SUM(cost_per_click * clicks) as total_cost')
                                        ->groupBy('date')
                                        ->orderBy('date')
                                        ->get();

        $trafficSourceData = MarketingLead::selectRaw('utm_source, COUNT(*) as lead_count')
                                          ->groupBy('utm_source')
                                          ->get();

        $newLeads = MarketingLead::where('status', 'New')->count();
        $contactedLeads = MarketingLead::where('status', 'Contacted')->count();
        $qualifiedLeads = MarketingLead::where('status', 'Qualified')->count();
        $convertedLeads = MarketingLead::where('status', 'Converted')->count();

        $conversionFunnelData = [
            'New' => $newLeads,
            'Contacted' => $contactedLeads,
            'Qualified' => $qualifiedLeads,
            'Converted' => $convertedLeads,
        ];

        return [
            'dashboardStats' => [
                'totalLeads' => $totalLeads,
                'convertedLeads' => $convertedLeads,
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

class MarketingAnalyticsService
{
    public function getDashboardData(Request $request): array
    {
        $totalLeads = MarketingLead::count();
        $convertedLeads = MarketingLead::whereNotNull('converted_patient_id')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;

        $totalMarketingSpend = MarketingBudget::sum('spent_amount');
        $patientsAcquired = Patient::whereNotNull('marketing_campaign_id')->count();
        $cpa = $patientsAcquired > 0 ? round($totalMarketingSpend / $patientsAcquired, 2) : 0;

        $revenueGenerated = CampaignMetric::sum('revenue_generated');
        $roi = $totalMarketingSpend > 0 ? round((($revenueGenerated - $totalMarketingSpend) / $totalMarketingSpend) * 100, 2) : 0;

        $campaignPerformanceData = $this->getFilteredCampaignMetricsQuery($request)
                                        ->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(conversions) as conversions, SUM(revenue_generated) as revenue_generated, SUM(cost_per_click * clicks) as total_cost')
                                        ->groupBy('date')
                                        ->orderBy('date')
                                        ->get();

        $trafficSourceData = MarketingLead::selectRaw('utm_source, COUNT(*) as lead_count')
                                          ->groupBy('utm_source')
                                          ->get();

        $newLeads = MarketingLead::where('status', 'New')->count();
        $contactedLeads = MarketingLead::where('status', 'Contacted')->count();
        $qualifiedLeads = MarketingLead::where('status', 'Qualified')->count();
        $convertedLeads = MarketingLead::where('status', 'Converted')->count();

        $conversionFunnelData = [
            'New' => $newLeads,
            'Contacted' => $contactedLeads,
            'Qualified' => $qualifiedLeads,
            'Converted' => $convertedLeads,
        ];

        return [
            'dashboardStats' => [
                'totalLeads' => $totalLeads,
                'convertedLeads' => $convertedLeads,
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
}
