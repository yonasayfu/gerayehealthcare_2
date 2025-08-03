<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MarketingAnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketingAnalyticsController extends Controller
{
    protected $marketingAnalyticsService;

    public function __construct(MarketingAnalyticsService $marketingAnalyticsService)
    {
        $this->marketingAnalyticsService = $marketingAnalyticsService;
    }

    public function dashboardData(Request $request)
    {
        $data = $this->marketingAnalyticsService->getDashboardData($request);
        return Inertia::render('Admin/MarketingAnalytics/Dashboard', $data);
    }

    public function campaignPerformance(Request $request)
    {
        $performanceData = $this->marketingAnalyticsService->getCampaignPerformance($request);
        return response()->json($performanceData);
    }

    public function trafficSourceDistribution()
    {
        $sourceData = $this->marketingAnalyticsService->getTrafficSourceDistribution();
        return response()->json($sourceData);
    }

    public function conversionFunnel()
    {
        $funnelData = $this->marketingAnalyticsService->getConversionFunnel();
        return response()->json($funnelData);
    }

    public function generateReport(Request $request)
    {
        return $this->marketingAnalyticsService->generateReport($request);
    }

    public function printAllCampaignPerformance(Request $request)
    {
        return $this->marketingAnalyticsService->printAllCampaignPerformance($request);
    }

    public function printCurrentCampaignPerformance(Request $request)
    {
        return $this->marketingAnalyticsService->printCurrentCampaignPerformance($request);
    }
}