<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use App\Rules\StaffIsAvailableForVisit; // 1. Import the new rule
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class VisitServiceController extends Controller
{
    // index() method remains the same...
    public function index(Request $request): Response
    {
        $query = VisitService::with(['patient', 'staff']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('full_name', 'ilike', "%{$search}%");
                })->orWhereHas('staff', function ($staffQuery) use ($search) {
                    $staffQuery->where('first_name', 'ilike', "%{$search}%")
                               ->orWhere('last_name', 'ilike', "%{$search}%");
                });
            });
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('scheduled_at', 'desc');
        }

        $perPage = $request->input('per_page', 10);
        $visitServices = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/VisitServices/Index', [
            'visitServices' => $visitServices,
            'filters' => $request->only(['search', 'sort_by', 'sort_direction', 'per_page']),
        ]);
    }


    // create() method remains the same...
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
    /**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'staff_id' => ['required', 'exists:staff,id', new StaffIsAvailableForVisit],
        'scheduled_at' => 'required|date',
        'status' => 'required|string|max:255',
        'visit_notes' => 'nullable|string',
        'prescription_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'vitals_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    // --- Add this new block to calculate the cost ---
    $staff = Staff::find($validated['staff_id']);
    // Assuming a 1-hour visit duration for cost calculation
    $visitCost = $staff->hourly_rate * 1; 
    $validated['cost'] = $visitCost;
    // ---------------------------------------------

    if ($request->hasFile('prescription_file')) {
        $validated['prescription_file'] = $request->file('prescription_file')->store('visits/prescriptions', 'public');
    }
    if ($request->hasFile('vitals_file')) {
        $validated['vitals_file'] = $request->file('vitals_file')->store('visits/vitals', 'public');
    }

    VisitService::create($validated);

    return redirect()->route('admin.visit-services.index')->with('success', 'Visit scheduled successfully.');
}
    // edit() method remains the same...
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
   /**
 * Update the specified resource in storage.
 */
public function update(Request $request, VisitService $visitService)
{
    $request->merge(['visit_id' => $visitService->id]);

    $validated = $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'staff_id' => ['required', 'exists:staff,id', new StaffIsAvailableForVisit],
        'scheduled_at' => 'required|date',
        'status' => 'required|string|max:255',
        'visit_notes' => 'nullable|string',
        'prescription_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'vitals_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    // --- Add this new block to recalculate the cost ---
    $staff = Staff::find($validated['staff_id']);
    // Assuming a 1-hour visit duration for cost calculation
    $visitCost = $staff->hourly_rate * 1; 
    $validated['cost'] = $visitCost;
    // ------------------------------------------------

    if ($request->hasFile('prescription_file')) {
        if ($visitService->prescription_file) {
            Storage::disk('public')->delete($visitService->prescription_file);
        }
        $validated['prescription_file'] = $request->file('prescription_file')->store('visits/prescriptions', 'public');
    }
    if ($request->hasFile('vitals_file')) {
        if ($visitService->vitals_file) {
            Storage::disk('public')->delete($visitService->vitals_file);
        }
        $validated['vitals_file'] = $request->file('vitals_file')->store('visits/vitals', 'public');
    }

    $visitService->update($validated);

    return redirect()->route('admin.visit-services.index')->with('success', 'Visit updated successfully.');
}

    // destroy() method remains the same...
    public function destroy(VisitService $visitService)
    {
        if ($visitService->prescription_file) {
            Storage::disk('public')->delete($visitService->prescription_file);
        }
        if ($visitService->vitals_file) {
            Storage::disk('public')->delete($visitService->vitals_file);
        }

        $visitService->delete();

        return redirect()->back()->with('success', 'Visit cancelled successfully.');
    }
}