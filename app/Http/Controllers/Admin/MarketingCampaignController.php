<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlatform;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class MarketingCampaignController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = MarketingCampaign::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('campaign_name', 'ilike', "%{$search}%")
                  ->orWhere('campaign_code', 'ilike', "%{$search}%")
                  ->orWhere('utm_campaign', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->input('platform_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('campaign_type')) {
            $query->where('campaign_type', $request->input('campaign_type'));
        }
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->input('end_date'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $marketingCampaigns = $query->with(['platform', 'assignedStaff', 'createdByStaff'])
                                    ->paginate($request->input('per_page', 5))
                                    ->withQueryString();

        return Inertia::render('Admin/MarketingCampaigns/Index', [
            'marketingCampaigns' => $marketingCampaigns,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'platform_id', 'status', 'campaign_type', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketingCampaignRequest $request)
    {
        $data = $request->validated();

        if (Auth::check()) {
            $user = Auth::user();
            $staffMember = \App\Models\Staff::where('user_id', $user->id)->first();

            if ($staffMember) {
                $data['created_by_staff_id'] = $staffMember->id;
            }
        }

        MarketingCampaign::create($data);

        return redirect()->route('admin.marketing-campaigns.index')->with('success', 'Marketing Campaign created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->load(['platform', 'assignedStaff', 'createdByStaff']);

        return Inertia::render('Admin/MarketingCampaigns/Show', [
            'marketingCampaign' => $marketingCampaign,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->load(['platform', 'assignedStaff', 'createdByStaff']);

        return Inertia::render('Admin/MarketingCampaigns/Edit', [
            'marketingCampaign' => $marketingCampaign,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketingCampaignRequest $request, MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->update($request->validated());

        return redirect()->route('admin.marketing-campaigns.index')->with('success', 'Marketing Campaign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->delete();

        return back()->with('success', 'Marketing Campaign deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, MarketingCampaign::class, AdditionalExportConfigs::getMarketingCampaignConfig());
    }

    public function printSingle(MarketingCampaign $marketingCampaign)
    {
        return $this->handlePrintSingle($marketingCampaign, AdditionalExportConfigs::getMarketingCampaignConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, MarketingCampaign::class, AdditionalExportConfigs::getMarketingCampaignConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, MarketingCampaign::class, AdditionalExportConfigs::getMarketingCampaignConfig());
    }
}
