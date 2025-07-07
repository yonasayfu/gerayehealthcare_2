<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Inertia\Inertia;
use Inertia\Response;

class VisitServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // In app/Http/Controllers/Admin/VisitServiceController.php

public function index(Request $request): Response
{
    $query = VisitService::with(['patient', 'staff']);

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->whereHas('patient', function ($patientQuery) use ($search) {
                $patientQuery->where('full_name', 'like', "%{$search}%");
            })->orWhereHas('staff', function ($staffQuery) use ($search) {
                $staffQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
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

    // **NEW:** Add file URLs for the frontend
    $visitServices->getCollection()->transform(function ($visit) {
        $visit->prescription_file_url = $visit->prescription_file ? Storage::url($visit->prescription_file) : null;
        $visit->vitals_file_url = $visit->vitals_file ? Storage::url($visit->vitals_file) : null;
        return $visit;
    });

    return Inertia::render('Admin/VisitServices/Index', [
        'visitServices' => $visitServices,
        'filters' => $request->only(['search', 'sort_by', 'sort_direction', 'per_page']),
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
            'prescription_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // New
            'vitals_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',       // New
        ]);

        if ($request->hasFile('prescription_file')) {
            $validated['prescription_file'] = $request->file('prescription_file')->store('visits/prescriptions', 'public');
        }

        if ($request->hasFile('vitals_file')) {
            $validated['vitals_file'] = $request->file('vitals_file')->store('visits/vitals', 'public');
        }

        VisitService::create($validated);

        return redirect()->route('admin.visit-services.index')->with('success', 'Visit scheduled successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VisitService $visitService): Response
    {
        // Add file URLs to the visitService object for the frontend
        $visitService->prescription_file_url = $visitService->prescription_file ? Storage::url($visitService->prescription_file) : null;
        $visitService->vitals_file_url = $visitService->vitals_file ? Storage::url($visitService->vitals_file) : null;

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
        // Use post method spoofing for file uploads with Inertia
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'scheduled_at' => 'required|date',
            'status' => 'required|string|max:255',
            'visit_notes' => 'nullable|string',
            'prescription_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'vitals_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('prescription_file')) {
            // Delete old file if it exists
            if ($visitService->prescription_file) {
                Storage::disk('public')->delete($visitService->prescription_file);
            }
            $validated['prescription_file'] = $request->file('prescription_file')->store('visits/prescriptions', 'public');
        }

        if ($request->hasFile('vitals_file')) {
            // Delete old file if it exists
            if ($visitService->vitals_file) {
                Storage::disk('public')->delete($visitService->vitals_file);
            }
            $validated['vitals_file'] = $request->file('vitals_file')->store('visits/vitals', 'public');
        }

        $visitService->update($validated);

        return redirect()->route('admin.visit-services.index')->with('success', 'Visit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisitService $visitService)
    {
        // Delete associated files from storage
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