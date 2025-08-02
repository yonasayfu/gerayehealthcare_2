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
use Barryvdh\DomPDF\Facade\Pdf;

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
                               ->paginate($request->input('per_page', 5))
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

        if ($type === 'pdf') {
            $data = $leads->map(function($lead) {
                return [
                    'lead_code' => $lead->lead_code,
                    'name' => $lead->first_name . ' ' . $lead->last_name,
                    'email' => $lead->email,
                    'phone' => $lead->phone_number,
                    'status' => $lead->status,
                    'source_campaign' => $lead->sourceCampaign->name ?? 'N/A',
                    'landing_page' => $lead->landingPage->name ?? 'N/A',
                    'assigned_staff' => $lead->assignedStaff->user->name ?? 'N/A',
                ];
            })->toArray();

            $columns = [
                ['key' => 'lead_code', 'label' => 'Lead Code'],
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'phone', 'label' => 'Phone'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'source_campaign', 'label' => 'Source Campaign'],
                ['key' => 'landing_page', 'label' => 'Landing Page'],
                ['key' => 'assigned_staff', 'label' => 'Assigned Staff'],
            ];

            $title = 'Marketing Leads List';
            $documentTitle = 'Marketing Leads List';

            $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                        ->setPaper('a4', 'landscape');
            return $pdf->stream('marketing-leads.pdf');
        }

        return redirect()->back()->with('error', 'Invalid export type.');
    }

    public function printSingle(MarketingLead $marketingLead)
    {
        $marketingLead->load(['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient']);

        $data = [
            ['label' => 'Lead Code', 'value' => $marketingLead->lead_code],
            ['label' => 'Full Name', 'value' => $marketingLead->first_name . ' ' . $marketingLead->last_name],
            ['label' => 'Email', 'value' => $marketingLead->email],
            ['label' => 'Phone Number', 'value' => $marketingLead->phone_number],
            ['label' => 'Status', 'value' => $marketingLead->status],
            ['label' => 'Source Campaign', 'value' => $marketingLead->sourceCampaign->name ?? 'N/A'],
            ['label' => 'Landing Page', 'value' => $marketingLead->landingPage->name ?? 'N/A'],
            ['label' => 'Assigned Staff', 'value' => $marketingLead->assignedStaff->user->name ?? 'N/A'],
            ['label' => 'Conversion Date', 'value' => $marketingLead->conversion_date ? \Carbon\Carbon::parse($marketingLead->conversion_date)->format('M d, Y') : 'N/A'],
            ['label' => 'Converted Patient', 'value' => ($marketingLead->convertedPatient->full_name ?? 'N/A') . ' (' . ($marketingLead->convertedPatient->patient_code ?? 'N/A') . ')'],
            ['label' => 'Notes', 'value' => $marketingLead->notes ?? 'N/A'],
            ['label' => 'Created At', 'value' => \Carbon\Carbon::parse($marketingLead->created_at)->format('M d, Y H:i')],
            ['label' => 'Updated At', 'value' => \Carbon\Carbon::parse($marketingLead->updated_at)->format('M d, Y H:i')],
        ];

        $columns = [
            ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
            ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
        ];

        $title = 'Marketing Lead Details';
        $documentTitle = 'Marketing Lead Details';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'portrait');
        return $pdf->stream("marketing-lead-{$marketingLead->lead_code}.pdf");
    }

    public function printAll(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $leads = $query->get();

        $data = $leads->map(function($lead) {
            return [
                'lead_code' => $lead->lead_code,
                'name' => $lead->first_name . ' ' . $lead->last_name,
                'email' => $lead->email,
                'phone' => $lead->phone_number,
                'status' => $lead->status,
                'source_campaign' => $lead->sourceCampaign->name ?? 'N/A',
                'landing_page' => $lead->landingPage->name ?? 'N/A',
                'assigned_staff' => $lead->assignedStaff->user->name ?? 'N/A',
            ];
        })->toArray();

        $columns = [
            ['key' => 'lead_code', 'label' => 'Lead Code'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'source_campaign', 'label' => 'Source Campaign'],
            ['key' => 'landing_page', 'label' => 'Landing Page'],
            ['key' => 'assigned_staff', 'label' => 'Assigned Staff'],
        ];

        $title = 'Marketing Leads List';
        $documentTitle = 'Marketing Leads List';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('marketing-leads.pdf');
    }

    public function printCurrent(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $leads = $query->get();

        $data = $leads->map(function($lead) {
            return [
                'lead_code' => $lead->lead_code,
                'name' => $lead->first_name . ' ' . $lead->last_name,
                'email' => $lead->email,
                'phone' => $lead->phone_number,
                'status' => $lead->status,
                'source_campaign' => $lead->sourceCampaign->name ?? 'N/A',
                'landing_page' => $lead->landingPage->name ?? 'N/A',
                'assigned_staff' => $lead->assignedStaff->user->name ?? 'N/A',
            ];
        })->toArray();

        $columns = [
            ['key' => 'lead_code', 'label' => 'Lead Code'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'source_campaign', 'label' => 'Source Campaign'],
            ['key' => 'landing_page', 'label' => 'Landing Page'],
            ['key' => 'assigned_staff', 'label' => 'Assigned Staff'],
        ];

        $title = 'Marketing Leads List (Current View)';
        $documentTitle = 'Marketing Leads List (Current View)';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('marketing-leads-current.pdf');
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
