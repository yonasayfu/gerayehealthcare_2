<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketingCampaignRequest;
use App\Http\Requests\UpdateMarketingCampaignRequest;
use App\Models\MarketingCampaign;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class MarketingCampaignController extends Controller
{
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
                                    ->paginate($request->input('per_page', 10))
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
        $type = $request->get('type');
        $campaigns = MarketingCampaign::with(['platform', 'assignedStaff', 'createdByStaff'])->get();

        if ($type === 'csv') {
            $csvData = "Campaign Code,Campaign Name,Platform,Type,Status,Start Date,End Date,Budget Allocated,Budget Spent,Assigned Staff,Created By\n";
            foreach ($campaigns as $campaign) {
                $csvData .= "\"{$campaign->campaign_code}\",\"{$campaign->campaign_name}\",\"{$campaign->platform->name ?? '-'}\",\"{$campaign->campaign_type}\",\"{$campaign->status}\",\"{$campaign->start_date}\",\"{$campaign->end_date}\",\"{$campaign->budget_allocated}\",\"{$campaign->budget_spent}\",\"{$campaign->assignedStaff->full_name ?? '-'}\",\"{$campaign->createdByStaff->full_name ?? '-'}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="marketing_campaigns.csv"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.marketing.campaigns', ['campaigns' => $campaigns])->setPaper('a4', 'landscape');
            return $pdf->stream('marketing_campaigns.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(MarketingCampaign $marketingCampaign)
    {
        $marketingCampaign->load(['platform', 'assignedStaff', 'createdByStaff']);
        $pdf = Pdf::loadView('pdf.marketing.campaigns-single', ['campaign' => $marketingCampaign])->setPaper('a4', 'portrait');
        return $pdf->stream("marketing_campaign_" . $marketingCampaign->campaign_code . ".pdf");
    }

    public function printCurrent(Request $request)
    {
        $query = MarketingCampaign::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('campaign_name', 'ilike', "%{$search}%")
                  ->orWhere('campaign_code', 'ilike', "%{$search}%")
                  ->orWhere('utm_campaign', 'ilike', "%{$search}%");
        }

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

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $campaigns = $query->with(['platform', 'assignedStaff', 'createdByStaff'])
                           ->paginate($request->input('per_page', 10), ['*'], 'page', $request->input('page', 1));

        $pdf = Pdf::loadView('pdf.marketing.campaigns-current', ['campaigns' => $campaigns])->setPaper('a4', 'landscape');
        return $pdf->stream('marketing_campaigns-current.pdf');
    }

    public function printAll(Request $request)
    {
        $campaigns = MarketingCampaign::with(['platform', 'assignedStaff', 'createdByStaff'])->orderBy('campaign_name')->get();

        return Inertia::render('Admin/MarketingCampaigns/PrintAll', [
            'marketingCampaigns' => $campaigns,
        ]);
    }
}
