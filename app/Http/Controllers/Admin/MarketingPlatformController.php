<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateMarketingPlatformDTO;
use App\DTOs\UpdateMarketingPlatformDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\MarketingPlatform;
use App\Services\MarketingPlatform\MarketingPlatformService;
use App\Services\Validation\Rules\MarketingPlatformRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketingPlatformController extends BaseController
{
    public function __construct(MarketingPlatformService $marketingPlatformService)
    {
        parent::__construct(
            $marketingPlatformService,
            MarketingPlatformRules::class,
            'Admin/MarketingPlatforms',
            'marketingPlatforms',
            MarketingPlatform::class,
            CreateMarketingPlatformDTO::class,
            UpdateMarketingPlatformDTO::class
        );
    }

    // Toggle active status
    public function toggleStatus(MarketingPlatform $marketing_platform)
    {
        $this->service->toggleStatus($marketing_platform);

        return redirect()->back()->with('banner', 'Platform status updated.')->with('bannerStyle', 'success');
    }

    public function printCurrent(Request $request)
    {
        $filters = $request->only(['search', 'sort', 'direction', 'per_page', 'is_active']);
        $marketingPlatforms = $this->service->getAll($request, [], true);

        return Inertia::render('Admin/MarketingPlatforms/PrintIndex', [
            'marketingPlatforms' => $marketingPlatforms,
            'filters' => $filters,
        ]);
    }
}
