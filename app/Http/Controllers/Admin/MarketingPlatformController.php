<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketingPlatformRequest;
use App\Http\Requests\UpdateMarketingPlatformRequest;
use App\Models\MarketingPlatform;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\MarketingPlatformsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MarketingPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        $query = MarketingPlatform::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'ilike', "%{$search}%");
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

        $marketingPlatforms = $query->paginate($request->input('per_page', 10))
                                    ->withQueryString();

        return Inertia::render('Admin/MarketingPlatforms/Index', [
            'marketingPlatforms' => $marketingPlatforms,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'is_active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/MarketingPlatforms/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMarketingPlatformRequest $request)
    {
        MarketingPlatform::create($request->validated());

        return redirect()->route('admin.marketing-platforms.index')->with('success', 'Marketing Platform created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketingPlatform $marketingPlatform)
    {
        return Inertia::render('Admin/MarketingPlatforms/Show', [
            'marketingPlatform' => $marketingPlatform,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketingPlatform $marketingPlatform)
    {
        return Inertia::render('Admin/MarketingPlatforms/Edit', [
            'marketingPlatform' => $marketingPlatform,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarketingPlatformRequest $request, MarketingPlatform $marketingPlatform)
    {
        $marketingPlatform->update($request->validated());

        return redirect()->route('admin.marketing-platforms.index')->with('success', 'Marketing Platform updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketingPlatform $marketingPlatform)
    {
        $marketingPlatform->delete();

        return back()->with('success', 'Marketing Platform deleted successfully.');
    }

    public function toggleStatus(MarketingPlatform $marketingPlatform)
    {
        $marketingPlatform->is_active = !$marketingPlatform->is_active;
        $marketingPlatform->save();

        return back()->with('success', 'Marketing Platform status updated successfully.');
    }

    public function export(Request $request)
    {
        $type = $request->input('type');
        if ($type === 'csv') {
            return Excel::download(new MarketingPlatformsExport, 'marketing-platforms.csv');
        } elseif ($type === 'pdf') {
            $marketingPlatforms = MarketingPlatform::all();
            $pdf = Pdf::loadView('pdf.marketing-platforms', compact('marketingPlatforms'));
            return $pdf->stream('marketing-platforms.pdf');
        }
        return redirect()->back()->with('error', 'Invalid export type.');
    }

    public function printAll()
    {
        $marketingPlatforms = MarketingPlatform::all();
        return Inertia::render('Admin/MarketingPlatforms/PrintAll', [
            'marketingPlatforms' => $marketingPlatforms,
        ]);
    }

    public function printCurrent(Request $request)
    {
        $query = MarketingPlatform::query();

        // Apply filters from the request
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'ilike', "%{$search}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $marketingPlatforms = $query->get();

        return Inertia::render('Admin/MarketingPlatforms/PrintCurrent', [
            'marketingPlatforms' => $marketingPlatforms,
        ]);
    }
}