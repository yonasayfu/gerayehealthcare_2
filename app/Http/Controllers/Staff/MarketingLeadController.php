<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketingLeadRequest;
use App\Http\Requests\UpdateMarketingLeadRequest;
use App\Models\MarketingLead;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MarketingLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = MarketingLead::query();

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
            $query->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('lead_code', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('source_campaign_id')) {
            $query->where('source_campaign_id', $request->input('source_campaign_id'));
        }
        if ($request->filled('landing_page_id')) {
            $query->where('landing_page_id', $request->input('landing_page_id'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $marketingLeads = $query->with(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient'])
                               ->paginate($request->input('per_page', 5))
                               ->withQueryString();

        return Inertia::render('Staff/MarketingLeads/Index', [
            'marketingLeads' => $marketingLeads,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'status', 'source_campaign_id', 'landing_page_id']),
        ]);
    }

    public function show(MarketingLead $marketingLead)
    {
        $marketingLead->load(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);

        return Inertia::render('Staff/MarketingLeads/Show', [
            'marketingLead' => $marketingLead,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingLead $marketingLead)
    {
        $marketingLead->load(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);

        return Inertia::render('Staff/MarketingLeads/Edit', [
            'marketingLead' => $marketingLead,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketingLeadRequest $request, MarketingLead $marketingLead)
    {
        $marketingLead->update($request->validated());

        return redirect()->route('staff.marketing-leads.index')->with('success', 'Marketing Lead updated successfully.');
    }
}
