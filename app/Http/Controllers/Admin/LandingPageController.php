<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLandingPageRequest;
use App\Http\Requests\UpdateLandingPageRequest;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\LandingPagesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MarketingCampaign;
use Barryvdh\DomPDF\Facade\Pdf;

class LandingPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = LandingPage::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('page_title', 'ilike', "%{$search}%")
                  ->orWhere('page_url', 'ilike', "%{$search}%")
                  ->orWhere('page_code', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }
        if ($request->filled('language')) {
            $query->where('language', $request->input('language'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $landingPages = $query->with(['campaign'])
                               ->paginate($request->input('per_page', 5))
                               ->withQueryString();

        return Inertia::render('Admin/LandingPages/Index', [
            'landingPages' => $landingPages,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'campaign_id', 'is_active', 'language']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/LandingPages/Create', [
            'campaigns' => MarketingCampaign::all(['id', 'campaign_name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLandingPageRequest $request)
    {
        LandingPage::create($request->validated());

        return redirect()->route('admin.landing-pages.index')->with('success', 'Landing Page created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LandingPage $landingPage)
    {
        $landingPage->load(['campaign']);

        return Inertia::render('Admin/LandingPages/Show', [
            'landingPage' => $landingPage,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LandingPage $landingPage)
    {
        $landingPage->load(['campaign']);

        return Inertia::render('Admin/LandingPages/Edit', [
            'landingPage' => $landingPage,
            'campaigns' => MarketingCampaign::all(['id', 'campaign_name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLandingPageRequest $request, LandingPage $landingPage)
    {
        $landingPage->update($request->validated());

        return redirect()->route('admin.landing-pages.index')->with('success', 'Landing Page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LandingPage $landingPage)
    {
        $landingPage->delete();

        return back()->with('success', 'Landing Page deleted successfully.');
    }

    public function export(Request $request, $type)
    {
        $query = $this->getFilteredQuery($request);
        $pages = $query->get();

        if ($type === 'csv') {
            return Excel::download(new LandingPagesExport($pages), 'landing-pages.csv');
        }

        // PDF export logic can be added here if needed

        return redirect()->back()->with('error', 'Invalid export type.');
    }

    public function printAll(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $pages = $query->get();

        $pdf = Pdf::loadView('pdf.landing-pages', ['pages' => $pages])->setPaper('a4', 'landscape');
        return $pdf->stream('landing-pages.pdf');
    }

    public function printCurrent(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $pages = $query->paginate($request->input('per_page', 5))->appends($request->except('page'));

        return Inertia::render('Admin/LandingPages/PrintCurrent', ['pages' => $pages->items()]);
    }

    private function getFilteredQuery(Request $request)
    {
        $query = LandingPage::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('page_title', 'ilike', "%{$search}%")
                  ->orWhere('page_url', 'ilike', "%{$search}%")
                  ->orWhere('page_code', 'ilike', "%{$search}%");
        }

        // Filtering
        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }
        if ($request->filled('language')) {
            $query->where('language', $request->input('language'));
        }

        // Sorting
        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->with(['campaign']);
    }
}
