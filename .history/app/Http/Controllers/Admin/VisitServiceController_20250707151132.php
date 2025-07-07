<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VisitServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = VisitService::with(['patient', 'staff']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('full_name', 'ilike', "%{$search}%");
                })->orWhereHas('staff', function ($staffQuery) use ($search) {
                    $staffQuery->where('first_name', 'ilike', "%{$search}%")
                               ->orWhere('last_name', 'iilike', "%{$search}%");
                });
            });
        }

        // Sorting functionality
        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction', 'asc');
            // Note: Sorting by related table columns requires a more complex query,
            // but we'll handle basic sorting for now.
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('scheduled_at', 'desc'); // Default sort
        }

        $visitServices = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/VisitServices/Index', [
            'visitServices' => $visitServices,
            'filters' => $request->only(['search', 'sort_by', 'sort_direction']),
            'patients' => Patient::all(['id', 'full_name']),
            'staff' => Staff::all(['id', 'first_name', 'last_name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/VisitServices/Create', [
            'patients' => Patient::all(['id', 'full_name']),
            'staff' => Staff::all(['id', 'first_name', 'last_name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'scheduled_at' => 'required|date',
            'status' => 'required|string|max:255',
            'visit_notes' => 'nullable|string',
        ]);

        VisitService::create($validated);

        return redirect()->route('admin.visit-services.index')->with('success', 'Visit scheduled successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VisitService $visitService): Response
    {
        return Inertia::render('Admin/VisitServices/Edit', [
            'visitService' => $visitService->load(['patient', 'staff']),
            'patients' => Patient::all(['id', 'full_name']),
            'staff' => Staff::all(['id', 'first_name', 'last_name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VisitService $visitService)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'scheduled_at' => 'required|date',
            'status' => 'required|string|max:255',
            'visit_notes' => 'nullable|string',
        ]);

        $visitService->update($validated);

        return redirect()->route('admin.visit-services.index')->with('success', 'Visit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisitService $visitService)
    {
        $visitService->delete();

        return redirect()->back()->with('success', 'Visit cancelled successfully.');
    }
}