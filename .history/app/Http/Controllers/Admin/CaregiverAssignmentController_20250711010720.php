<?php
// FILE: app/Http/Controllers/Admin/CaregiverAssignmentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaregiverAssignment;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response; // Correctly import Inertia's Response

class CaregiverAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // THE FIX IS HERE: The return type is changed to Response (from Inertia)
    public function index(Request $request): Response
    {
        $query = CaregiverAssignment::with(['patient', 'staff']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('patient', fn($q) => $q->where('full_name', 'ilike', "%{$search}%"))
                  ->orWhereHas('staff', fn($q) => $q->where('first_name', 'ilike', "%{$search}%"));
        }

        if ($request->filled('sort')) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        }

        $assignments = $query->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('Admin/CaregiverAssignments/Index', [
            'assignments' => $assignments,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    // ... other methods in this file remain the same ...
    public function create()
    {
        return Inertia::render('Admin/CaregiverAssignments/Create', [
            'staff' => Staff::where('status', 'Active')->orderBy('first_name')->get(),
            'patients' => Patient::orderBy('full_name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'patient_id' => 'required|exists:patients,id',
            'shift_start' => 'required|date',
            'shift_end' => 'required|date|after_or_equal:shift_start',
            'status' => 'required|string',
        ]);

        CaregiverAssignment::create($request->all());

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment created successfully.');
    }

    public function show(CaregiverAssignment $assignment)
    {
        return Inertia::render('Admin/CaregiverAssignments/Show', [
            'assignment' => $assignment->load('staff', 'patient')
        ]);
    }

    public function edit(CaregiverAssignment $assignment)
    {
        $assignment->load('staff', 'patient');
        return Inertia::render('Admin/CaregiverAssignments/Edit', [
            'assignment' => $assignment,
            'staff' => Staff::where('status', 'Active')->orderBy('first_name')->get(),
            'patients' => Patient::orderBy('full_name')->get()
        ]);
    }

    public function update(Request $request, CaregiverAssignment $assignment)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'patient_id' => 'required|exists:patients,id',
            'shift_start' => 'required|date',
            'shift_end' => 'required|date|after_or_equal:shift_start',
            'status' => 'required|string',
        ]);

        $assignment->update($request->all());

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment updated successfully.');
    }

    public function destroy(CaregiverAssignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('admin.assignments.index')->with('success', 'Assignment deleted successfully.');
    }
}
