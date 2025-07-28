<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingLead;
use App\Models\MarketingCampaign;
use App\Models\CampaignMetric;
use App\Models\MarketingBudget;
use App\Models\Patient;
use App\Models\LeadSource;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class MarketingAnalyticsController extends Controller
{
    public function dashboardData(Request $request)
    {
        $totalLeads = MarketingLead::count();
        $convertedLeads = MarketingLead::whereNotNull('converted_patient_id')->count();
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;

        $totalMarketingSpend = MarketingBudget::sum('spent_amount');
        $patientsAcquired = Patient::whereNotNull('marketing_campaign_id')->count();
        $cpa = $patientsAcquired > 0 ? round($totalMarketingSpend / $patientsAcquired, 2) : 0;

        $revenueGenerated = CampaignMetric::sum('revenue_generated');
        $roi = $totalMarketingSpend > 0 ? round((($revenueGenerated - $totalMarketingSpend) / $totalMarketingSpend) * 100, 2) : 0;

        return response()->json([
            'totalLeads' => $totalLeads,
            'convertedLeads' => $convertedLeads,
            'conversionRate' => $conversionRate,
            'totalMarketingSpend' => $totalMarketingSpend,
            'patientsAcquired' => $patientsAcquired,
            'cpa' => $cpa,
            'revenueGenerated' => $revenueGenerated,
            'roi' => $roi,
        ]);
    }

    public function campaignPerformance(Request $request)
    {
        $query = CampaignMetric::query();

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            // Assuming CampaignMetric has a relationship to Campaign, and Campaign to Platform
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

        $performanceData = $query->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(conversions) as conversions, SUM(revenue_generated) as revenue_generated, SUM(cost_per_click * clicks) as total_cost')
                                 ->groupBy('date')
                                 ->orderBy('date')
                                 ->get();

        return response()->json($performanceData);
    }

    public function trafficSourceDistribution(Request $request)
    {
        $sourceData = MarketingLead::selectRaw('utm_source, COUNT(*) as lead_count')
                                   ->groupBy('utm_source')
                                   ->get();

        // You might also want to join with lead_sources table for more descriptive names
        // $sourceData = MarketingLead::selectRaw('lead_sources.name as source_name, COUNT(marketing_leads.id) as lead_count')
        //                            ->join('lead_sources', 'marketing_leads.acquisition_source_id', '=', 'lead_sources.id')
        //                            ->groupBy('source_name')
        //                            ->get();

        return response()->json($sourceData);
    }

    public function conversionFunnel(Request $request)
    {
        $newLeads = MarketingLead::where('status', 'New')->count();
        $contactedLeads = MarketingLead::where('status', 'Contacted')->count();
        $qualifiedLeads = MarketingLead::where('status', 'Qualified')->count();
        $convertedLeads = MarketingLead::where('status', 'Converted')->count();

        return response()->json([
            'New' => $newLeads,
            'Contacted' => $contactedLeads,
            'Qualified' => $qualifiedLeads,
            'Converted' => $convertedLeads,
        ]);
    }

    public function generateReport(Request $request)
    {
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Example: Monthly Marketing Performance Report
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

        // Add more report types as needed (e.g., campaign_roi, patient_acquisition_cost)

        return response()->json(['message' => 'Report generation initiated.', 'report_type' => $reportType]);
    }
}
