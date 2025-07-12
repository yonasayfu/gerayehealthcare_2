<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\VisitService;
use App\Rules\StaffIsAvailableForVisit;
use App\Models\CaregiverAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class VisitServiceController extends Controller
{
    public function index(Request $request): Response
    {
        $query = VisitService::with(['patient', 'staff']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                // Using 'ilike' for case-insensitive search in PostgreSQL
                $q->whereHas('patient', fn($pq) => $pq->where('full_name', 'ilike', "%{$search}%"))
                    ->orWhereHas('staff', fn($sq) => $sq->where('first_name', 'ilike', "%{$search}%")->orWhere('last_name', 'ilike', "%{$search}%"));
            });
        }

        $query->orderBy($request->input('sort_by', 'scheduled_at'), $request->input('sort_direction', 'desc'));

        return Inertia::render('Admin/VisitServices/Index', [
            'visitServices' => $query->paginate($request->input('per_page', 10))->withQueryString(),
            'filters' => $request->only(['search', 'sort_by', 'sort_direction', 'per_page']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/VisitServices/Create', [
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            // THE FIX IS HERE:
            'staff' => Staff::where('status', 'Active')->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }

   public function store(Request $request)
{
    // Your existing validation is correct.
    $validated = $request->validate([
        'patient_id' => 'required|exists:patients,id',
        // 'staff_id' => ['required', 'exists:staff,id', new StaffIsAvailableForVisit],
        //'staff_id' => ['required', 'exists:staff,id'],
        'scheduled_at' => 'required|date',
        'status' => 'required|string|max:255',
        'visit_notes' => 'nullable|string',
        // ... other fields
    ]);

    // --- THE FINAL FIX IS HERE ---
    // Find the currently 'Assigned' record for this patient and staff.
    $assignment = CaregiverAssignment::where('patient_id', $validated['patient_id'])
                                       ->where('staff_id', $validated['staff_id'])
                                       ->where('status', 'Assigned') // This makes the query specific
                                       ->latest('id')
                                       ->first();

    // Add the assignment_id to our validated data array.
    $validated['assignment_id'] = $assignment?->id;
    // --- END OF FIX ---

    // Your existing logic for cost and file uploads is correct.
    $staff = Staff::find($validated['staff_id']);
    $validated['cost'] = ($staff->hourly_rate ?? 0) * 1;

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
     * Display the specified resource.
     */
    public function show(VisitService $visitService): Response
    {
        return Inertia::render('Admin/VisitServices/Show', [
            'visitService' => $visitService->load(['patient', 'staff']),
        ]);
    }

    public function edit(VisitService $visitService): Response
    {
        return Inertia::render('Admin/VisitServices/Edit', [
            'visitService' => $visitService->load(['patient', 'staff']),
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            // THE FIX IS HERE:
            'staff' => Staff::where('status', 'Active')->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }

    public function update(Request $request, VisitService $visitService)
{
    $request->merge(['visit_id' => $visitService->id]);

    $validated = $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'staff_id' => ['required', 'exists:staff,id', new StaffIsAvailableForVisit],
        'scheduled_at' => 'required|date',
        // ... other validation rules
    ]);

    // --- ADD THIS LOGIC BLOCK TO THE UPDATE METHOD ---
    $assignment = CaregiverAssignment::where('patient_id', $validated['patient_id'])
                                       ->where('staff_id', $validated['staff_id'])
                                       ->where('status', 'Assigned')
                                       ->latest('id')
                                       ->first();
                                       
    $validated['assignment_id'] = $assignment?->id;
    // --- END OF BLOCK ---

    $staff = Staff::find($validated['staff_id']);
    $validated['cost'] = ($staff->hourly_rate ?? 0) * 1;

    // Your file handling logic is correct.
    if ($request->hasFile('prescription_file')) {
        // ...
    } else {
        unset($validated['prescription_file']);
    }
    if ($request->hasFile('vitals_file')) {
        // ...
    } else {
        unset($validated['vitals_file']);
    }

    $visitService->update($validated);
    return redirect()->route('admin.visit-services.index')->with('success', 'Visit updated successfully.');
}

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
