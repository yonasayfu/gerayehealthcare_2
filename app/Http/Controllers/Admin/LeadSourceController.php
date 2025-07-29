<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadSourceRequest;
use App\Http\Requests\UpdateLeadSourceRequest;
use App\Models\LeadSource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\LeadSourcesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LeadSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = LeadSource::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'ilike', "%{$search}%")
                  ->orWhere('category', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $leadSources = $query->paginate($request->input('per_page', 10))
                             ->withQueryString();

        return Inertia::render('Admin/LeadSources/Index', [
            'leadSources' => $leadSources,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'is_active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/LeadSources/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeadSourceRequest $request)
    {
        LeadSource::create($request->validated());

        return redirect()->route('admin.lead-sources.index')->with('success', 'Lead Source created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeadSource $leadSource)
    {
        return Inertia::render('Admin/LeadSources/Show', [
            'leadSource' => $leadSource,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeadSource $leadSource)
    {
        return Inertia::render('Admin/LeadSources/Edit', [
            'leadSource' => $leadSource,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeadSourceRequest $request, LeadSource $leadSource)
    {
        $leadSource->update($request->validated());

        return redirect()->route('admin.lead-sources.index')->with('success', 'Lead Source updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeadSource $leadSource)
    {
        $leadSource->delete();

        return back()->with('success', 'Lead Source deleted successfully.');
    }

    public function toggleStatus(LeadSource $leadSource)
    {
        $leadSource->is_active = !$leadSource->is_active;
        $leadSource->save();

        return back()->with('success', 'Lead Source status updated successfully.');
    }

    public function export(Request $request)
    {
        $type = $request->input('type');
        if ($type === 'csv') {
            return Excel::download(new LeadSourcesExport, 'lead-sources.csv');
        } elseif ($type === 'pdf') {
            $leadSources = LeadSource::all();
            $pdf = Pdf::loadView('pdf.lead-sources', compact('leadSources'));
            return $pdf->download('lead-sources.pdf');
        }
        return redirect()->back()->with('error', 'Invalid export type.');
    }

    public function printAll(Request $request)
    {
        $leadSources = LeadSource::all();
        return Inertia::render('Admin/LeadSources/PrintAll', [
            'leadSources' => $leadSources,
        ]);
    }

    public function printCurrent(Request $request)
    {
        $query = LeadSource::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'ilike', "%{$search}%")
                  ->orWhere('category', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $leadSources = $query->get();

        return Inertia::render('Admin/LeadSources/PrintCurrent', [
            'leadSources' => $leadSources,
        ]);
    }
}
