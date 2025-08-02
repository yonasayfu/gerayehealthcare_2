<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Http\Requests\StoreMarketingBudgetRequest;
use App\Http\Requests\UpdateMarketingBudgetRequest;
use App\Models\MarketingBudget;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\MarketingBudgetsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MarketingBudgetController extends Controller
{
    use ExportableTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = MarketingBudget::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('budget_name', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('platform_id')) {
            $query->where('platform_id', $request->input('platform_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('period_start')) {
            $query->where('period_start', '>=', $request->input('period_start'));
        }
        if ($request->filled('period_end')) {
            $query->where('period_end', '<=', $request->input('period_end'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $marketingBudgets = $query->with(['campaign', 'platform'])
                                   ->paginate($request->input('per_page', 5))
                                   ->withQueryString();

        return Inertia::render('Admin/MarketingBudgets/Index', [
            'marketingBudgets' => $marketingBudgets,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'campaign_id', 'platform_id', 'status', 'period_start', 'period_end']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campaigns = \App\Models\MarketingCampaign::all();
        $platforms = \App\Models\MarketingPlatform::all();
        $statuses = ['Planned', 'Active', 'Completed', 'On Hold', 'Cancelled'];

        return Inertia::render('Admin/MarketingBudgets/Create', [
            'campaigns' => $campaigns,
            'platforms' => $platforms,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketingBudgetRequest $request)
    {
        MarketingBudget::create($request->validated());

        return redirect()->route('admin.marketing-budgets.index')->with('success', 'Marketing Budget created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingBudget $marketingBudget)
    {
        $marketingBudget->load(['campaign', 'platform']);

        return Inertia::render('Admin/MarketingBudgets/Show', [
            'marketingBudget' => $marketingBudget,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingBudget $marketingBudget)
    {
        $marketingBudget->load(['campaign', 'platform']);
        $campaigns = \App\Models\MarketingCampaign::all();
        $platforms = \App\Models\MarketingPlatform::all();
        $statuses = ['Planned', 'Active', 'Completed', 'On Hold', 'Cancelled'];

        return Inertia::render('Admin/MarketingBudgets/Edit', [
            'marketingBudget' => $marketingBudget,
            'campaigns' => $campaigns,
            'platforms' => $platforms,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketingBudgetRequest $request, MarketingBudget $marketingBudget)
    {
        \Log::info('MarketingBudget update request data:', $request->validated());
        $updated = $marketingBudget->update($request->validated());
        $marketingBudget->refresh(); // Refresh the model to get the latest data from the database
        \Log::info('MarketingBudget update result:', ['updated' => $updated, 'marketingBudget' => $marketingBudget->toArray()]);

        if ($updated) {
            return redirect()->route('admin.marketing-budgets.index')->with('success', 'Marketing Budget updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update Marketing Budget.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingBudget $marketingBudget)
    {
        $marketingBudget->delete();

        return back()->with('success', 'Marketing Budget deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, MarketingBudget::class, AdditionalExportConfigs::getMarketingBudgetConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, MarketingBudget::class, AdditionalExportConfigs::getMarketingBudgetConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, MarketingBudget::class, AdditionalExportConfigs::getMarketingBudgetConfig());
    }
}
