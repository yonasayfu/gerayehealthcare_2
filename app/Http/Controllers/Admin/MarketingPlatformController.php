<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\MarketingPlatformService;
use App\Models\MarketingPlatform;
use App\Services\Validation\Rules\MarketingPlatformRules;
use Illuminate\Http\Request;
use App\DTOs\CreateMarketingPlatformDTO;
use App\DTOs\UpdateMarketingPlatformDTO;

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

   

    

   
}
