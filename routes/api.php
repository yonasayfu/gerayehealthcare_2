<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MarketingAnalyticsController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware(['auth:sanctum', 'role:super_admin|admin'])->prefix('admin')->name('api.admin.')->group(function () {
//     Route::get('marketing-analytics/dashboard-data', [MarketingAnalyticsController::class, 'dashboardData'])->name('marketing-analytics.dashboardData');
//     Route::get('marketing-analytics/campaign-performance', [MarketingAnalyticsController::class, 'campaignPerformance'])->name('marketing-analytics.campaignPerformance');
//     Route::get('marketing-analytics/traffic-source-distribution', [MarketingAnalyticsController::class, 'trafficSourceDistribution'])->name('marketing-analytics.trafficSourceDistribution');
//     Route::get('marketing-analytics/conversion-funnel', [MarketingAnalyticsController::class, 'conversionFunnel'])->name('marketing-analytics.conversionFunnel');
// });
