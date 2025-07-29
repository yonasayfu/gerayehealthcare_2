<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MarketingCampaignController as AdminMarketingCampaignController;
use App\Http\Controllers\Admin\MarketingLeadController as AdminMarketingLeadController;
use App\Http\Controllers\Admin\MarketingAnalyticsController as AdminMarketingAnalyticsController;
use App\Http\Controllers\Admin\LandingPageController as AdminLandingPageController;
use App\Http\Controllers\Admin\MarketingPlatformController as AdminMarketingPlatformController;
use App\Http\Controllers\Admin\LeadSourceController as AdminLeadSourceController;
use App\Http\Controllers\Admin\MarketingBudgetController as AdminMarketingBudgetController;
use App\Http\Controllers\Admin\CampaignContentController as AdminCampaignContentController;
use App\Http\Controllers\Admin\MarketingTaskController as AdminMarketingTaskController;
use App\Http\Controllers\Staff\MarketingCampaignController as StaffMarketingCampaignController;
use App\Http\Controllers\Staff\MarketingLeadController as StaffMarketingLeadController;
use App\Http\Controllers\Staff\MarketingTaskController as StaffMarketingTaskController;


Route::middleware(['auth', 'verified'])->group(function () {
    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('marketing-campaigns/export', [AdminMarketingCampaignController::class, 'export'])->name('marketing-campaigns.export');
        Route::get('marketing-campaigns/print-all', [AdminMarketingCampaignController::class, 'printAll'])->name('marketing-campaigns.printAll');
        Route::get('marketing-campaigns/print-current', [AdminMarketingCampaignController::class, 'printCurrent'])->name('marketing-campaigns.printCurrent');
        Route::get('marketing-campaigns/{marketing_campaign}/print', [AdminMarketingCampaignController::class, 'printSingle'])->name('marketing-campaigns.printSingle');
        Route::resource('marketing-campaigns', AdminMarketingCampaignController::class);
        Route::get('marketing-leads/export', [AdminMarketingLeadController::class, 'export'])->name('marketing-leads.export');
        Route::get('marketing-leads/print-all', [AdminMarketingLeadController::class, 'printAll'])->name('marketing-leads.printAll');
        Route::resource('marketing-leads', AdminMarketingLeadController::class);
        Route::resource('landing-pages', AdminLandingPageController::class);
        Route::resource('marketing-platforms', AdminMarketingPlatformController::class);
        Route::resource('lead-sources', AdminLeadSourceController::class);
        Route::resource('marketing-budgets', AdminMarketingBudgetController::class);
        Route::resource('campaign-contents', AdminCampaignContentController::class);
        Route::resource('marketing-tasks', AdminMarketingTaskController::class);

        // Analytics Routes
        Route::prefix('marketing-analytics')->name('marketing-analytics.')->group(function () {
            Route::get('dashboard-data', [AdminMarketingAnalyticsController::class, 'dashboardData'])->name('dashboard-data');
            Route::get('campaign-performance', [AdminMarketingAnalyticsController::class, 'campaignPerformance'])->name('campaign-performance');
            Route::get('traffic-source-distribution', [AdminMarketingAnalyticsController::class, 'trafficSourceDistribution'])->name('traffic-source-distribution');
            Route::get('conversion-funnel', [AdminMarketingAnalyticsController::class, 'conversionFunnel'])->name('conversion-funnel');
            Route::get('generate-report', [AdminMarketingAnalyticsController::class, 'generateReport'])->name('generate-report');
        });

        // Additional Admin actions
        Route::put('marketing-platforms/{marketing_platform}/toggle-status', [AdminMarketingPlatformController::class, 'toggleStatus'])->name('marketing-platforms.toggle-status');
        Route::put('lead-sources/{lead_source}/toggle-status', [AdminLeadSourceController::class, 'toggleStatus'])->name('lead-sources.toggle-status');
    });

    // Staff Routes
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::resource('marketing-campaigns', StaffMarketingCampaignController::class)->only(['index', 'show', 'edit', 'update']);
        Route::resource('marketing-leads', StaffMarketingLeadController::class)->only(['index', 'show', 'edit', 'update']);
        Route::resource('marketing-tasks', StaffMarketingTaskController::class)->only(['index', 'show', 'edit', 'update']);
    });
});
