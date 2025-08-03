<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\MarketingPlatformService;
use App\Models\MarketingPlatform;
use App\Services\Validation\Rules\MarketingPlatformRules;
use Illuminate\Http\Request;

class MarketingPlatformController extends BaseController
{
    public function __construct(MarketingPlatformService $marketingPlatformService)
    {
        parent::__construct(
            $marketingPlatformService,
            MarketingPlatformRules::class,
            'Admin/MarketingPlatforms',
            'marketingPlatforms',
            MarketingPlatform::class
        );
    }

    public function show(MarketingPlatform $marketingPlatform)
    {
        return parent::show($marketingPlatform->id);
    }

    public function edit(MarketingPlatform $marketingPlatform)
    {
        return parent::edit($marketingPlatform->id);
    }

    public function update(Request $request, MarketingPlatform $marketingPlatform)
    {
        return parent::update($request, $marketingPlatform->id);
    }

    public function destroy(MarketingPlatform $marketingPlatform)
    {
        return parent::destroy($marketingPlatform->id);
    }

    public function toggleStatus(MarketingPlatform $marketingPlatform)
    {
        $this->service->toggleStatus($marketingPlatform);
        return back()->with('success', 'Marketing Platform status updated successfully.');
    }

    public function export(Request $request)
    {
        return parent::export($request);
    }

    public function printAll(Request $request)
    {
        return parent::printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return parent::printCurrent($request);
    }
}
