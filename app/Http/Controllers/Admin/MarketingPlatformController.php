<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateMarketingPlatformDTO;
use App\DTOs\UpdateMarketingPlatformDTO;
use App\Http\Controllers\Base\BaseController;
use App\Models\MarketingPlatform;
use App\Services\MarketingPlatformService;
use App\Services\Validation\Rules\MarketingPlatformRules;

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

        return redirect()->back()->with('success', 'Platform status updated.');
    }
}
