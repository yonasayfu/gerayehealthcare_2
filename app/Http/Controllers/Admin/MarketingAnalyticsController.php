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
        // If this is an Inertia navigation, return an Inertia view as required
        if ($request->header('X-Inertia')) {
            return Inertia::render('Admin/MarketingAnalytics/Dashboard', $data);
        }
        // Otherwise, return JSON for axios usage in the dashboard page
        return response()->json($data['dashboardStats']);
    }

    public function campaignPerformance(Request $request)
    {
        $performanceData = $this->marketingAnalyticsService->getCampaignPerformance($request);
        return response()->json($performanceData);
    }

    public function trafficSourceDistribution(Request $request)
    {
        $sourceData = $this->marketingAnalyticsService->getTrafficSourceDistribution($request);
        return response()->json($sourceData);
    }

    public function conversionFunnel(Request $request)
    {
        $funnelData = $this->marketingAnalyticsService->getConversionFunnel($request);
        return response()->json($funnelData);
    }

    public function budgetPacing(Request $request)
    {
        $data = $this->marketingAnalyticsService->getBudgetPacing($request);
        return response()->json($data);
    }

    public function staffPerformance(Request $request)
    {
        $data = $this->marketingAnalyticsService->getStaffPerformance($request);
        return response()->json($data);
    }

    public function taskSla(Request $request)
    {
        $data = $this->marketingAnalyticsService->getTaskSla($request);
        return response()->json($data);
    }
}
