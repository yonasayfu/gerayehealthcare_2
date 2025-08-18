<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\MarketingPlatformService;
use App\Models\MarketingPlatform;
use App\Services\Validation\Rules\MarketingPlatformRules;
use Illuminate\Http\Request;
use App\DTOs\CreateMarketingPlatformDTO;
use App\DTOs\UpdateMarketingPlatformDTO;
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
        return redirect()->back()->with('success', 'Platform status updated.');
    }


    // Print current view (expects the frontend to pass current list if needed)
    public function printCurrent(Request $request)
    {
        // Build the same query as index but use pagination params if provided
        $query = MarketingPlatform::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'ilike', "%{$search}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool)$request->input('is_active'));
        }

        if ($request->filled('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int)($request->input('per_page', 10));
        $page = (int)($request->input('page', 1));

        $platforms = $query->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Admin/MarketingPlatforms/PrintCurrent', [
            'marketingPlatforms' => $platforms->items(),
        ]);
    }
}
