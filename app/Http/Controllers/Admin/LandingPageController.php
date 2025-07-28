<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLandingPageRequest;
use App\Http\Requests\UpdateLandingPageRequest;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
                               ->paginate($request->input('per_page', 10))
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
        return Inertia::render('Admin/LandingPages/Create');
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
}
