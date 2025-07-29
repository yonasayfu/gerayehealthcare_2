<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketingLeadRequest;
use App\Http\Requests\UpdateMarketingLeadRequest;
use App\Models\MarketingLead;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\MarketingLeadsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

use App\Models\MarketingCampaign;
use App\Models\LandingPage;
use App\Models\Staff;
use App\Models\Patient;

class MarketingLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        Log::info('MarketingLeadController@index called.');
        $query = MarketingLead::query();

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
        if ($request->filled('assigned_staff_id')) {
            $query->where('assigned_staff_id', $request->input('assigned_staff_id'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $marketingLeads = $query->with(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient'])
                               ->paginate($request->input('per_page', 10))
                               ->withQueryString();

        // Temporarily dump data for debugging
        // dd($marketingLeads->toArray());

        return Inertia::render('Admin/MarketingLeads/Index', [
            'marketingLeads' => $marketingLeads,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'status', 'source_campaign_id', 'landing_page_id', 'assigned_staff_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/MarketingLeads/Create', [
            'campaigns' => MarketingCampaign::all(),
            'landingPages' => LandingPage::all(),
            'staffMembers' => Staff::all(),
            'patients' => Patient::all(),
            'statuses' => ['New', 'Contacted', 'Qualified', 'Disqualified', 'Converted'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketingLeadRequest $request)
    {
        MarketingLead::create($request->validated());

        return redirect()->route('admin.marketing-leads.index')->with('success', 'Marketing Lead created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingLead $marketingLead)
    {
        $marketingLead->load(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);

        return Inertia::render('Admin/MarketingLeads/Show', [
            'marketingLead' => $marketingLead,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingLead $marketingLead)
    {
        $marketingLead->load(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);

        return Inertia::render('Admin/MarketingLeads/Edit', [
            'marketingLead' => $marketingLead,
            'campaigns' => MarketingCampaign::all(),
            'landingPages' => LandingPage::all(),
            'staffMembers' => Staff::all(),
            'patients' => Patient::all(),
            'statuses' => ['New', 'Contacted', 'Qualified', 'Disqualified', 'Converted'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketingLeadRequest $request, MarketingLead $marketingLead)
    {
        $marketingLead->update($request->validated());

        return redirect()->route('admin.marketing-leads.index')->with('success', 'Marketing Lead updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingLead $marketingLead)
    {
        $marketingLead->delete();

        return back()->with('success', 'Marketing Lead deleted successfully.');
    }

    public function export(Request $request, $type)
    {
        $query = $this->getFilteredQuery($request);
        $leads = $query->get();

        if ($type === 'csv') {
            return Excel::download(new MarketingLeadsExport($leads), 'marketing-leads.csv');
        }

        // PDF export logic can be added here if needed

        return redirect()->back()->with('error', 'Invalid export type.');
    }

    public function printAll(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $leads = $query->get();

        return Inertia::render('Admin/MarketingLeads/PrintAll', ['leads' => $leads]);
    }

    private function getFilteredQuery(Request $request)
    {
        $query = MarketingLead::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('lead_code', 'ilike', "%{$search}%");
            });
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
        if ($request->filled('assigned_staff_id')) {
            $query->where('assigned_staff_id', $request->input('assigned_staff_id'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->with(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);
    }
}
