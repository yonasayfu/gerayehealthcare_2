<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketingCampaignRequest;
use App\Http\Requests\UpdateMarketingCampaignRequest;
use App\Models\MarketingCampaign;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MarketingCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = MarketingCampaign::query();

        // Filter by assigned staff member
        if (Auth::check()) {
            $user = Auth::user();
            $staffMember = \App\Models\Staff::where('user_id', $user->id)->first();
            if ($staffMember) {
                $query->where('assigned_staff_id', $staffMember->id);
            }
        }

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

        return Inertia::render('Staff/MarketingCampaigns/Index', [
            'marketingCampaigns' => $marketingCampaigns,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'platform_id', 'status', 'campaign_type', 'start_date', 'end_date']),
        ]);
    }

    public function show(MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->load(['platform', 'assignedStaff', 'createdByStaff']);

        return Inertia::render('Staff/MarketingCampaigns/Show', [
            'marketingCampaign' => $marketingCampaign,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->load(['platform', 'assignedStaff', 'createdByStaff']);

        return Inertia::render('Staff/MarketingCampaigns/Edit', [
            'marketingCampaign' => $marketingCampaign,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketingCampaignRequest $request, MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->update($request->validated());

        return redirect()->route('staff.marketing-campaigns.index')->with('success', 'Marketing Campaign updated successfully.');
    }
}
