<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaregiverAssignment;
use App\Models\Patient;
use App\Models\Staff;
use App\Rules\StaffIsAvailableForShift; // Import our new rule
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class CaregiverAssignmentController extends Controller
{
    // ... index() and create() methods remain the same ...
    public function index(Request $request)
    {
        $query = CaregiverAssignment::with(['staff', 'patient']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%");
            })->orWhereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                      ->orWhere('last_name', 'ilike', "%{$search}%");
            })->orWhere('status', 'ilike', "%{$search}%");
        }
        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }
        return Inertia::render('Admin/CaregiverAssignments/Index', [
            'assignments' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }
    public function create()
    {
        return Inertia::render('Admin/CaregiverAssignments/Create', [
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            'staff' => Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }


    public function store(Request $request)
    {
        $request->merge([
            'shift_start' => Carbon::parse($request->shift_start)->toIso8601String(),
            'shift_end' => Carbon::parse($request->shift_end)->toIso8601String(),
        ]);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'status' => 'required|string|max:255',
            'shift_end' => 'required|date|after_or_equal:shift_start',
            // Use our custom rule for shift_start
            'shift_start' => [
                'required',
                'date',
                new StaffIsAvailableForShift($request->staff_id, $request->shift_end)
            ],
        ]);

        CaregiverAssignment::create($request->all());

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment created successfully.');
    }

    // ... show() and edit() methods remain the same ...
     public function show(CaregiverAssignment $assignment)
    {
        $assignment->load(['staff', 'patient']);
        return Inertia::render('Admin/CaregiverAssignments/Show', [
            'assignment' => $assignment,
        ]);
    }
    public function edit(CaregiverAssignment $assignment)
    {
        $assignment->load(['staff', 'patient']);
        return Inertia::render('Admin/CaregiverAssignments/Edit', [
            'assignment' => $assignment,
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            'staff' => Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function update(Request $request, CaregiverAssignment $assignment)
    {
        $request->merge([
            'shift_start' => Carbon::parse($request->shift_start)->toIso8601String(),
            'shift_end' => Carbon::parse($request->shift_end)->toIso8601String(),
        ]);
        
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'status' => 'required|string|max:255',
            'shift_end' => 'required|date|after_or_equal:shift_start',
            // Use our custom rule, ignoring the current assignment ID
            'shift_start' => [
                'required',
                'date',
                new StaffIsAvailableForShift($request->staff_id, $request->shift_end, $assignment->id)
            ],
        ]);

        $assignment->update($request->all());

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment updated successfully.');
    }

    // ... destroy() and export() methods remain the same ...
    public function destroy(CaregiverAssignment $assignment)
    {
        $assignment->delete();
        return back();
    }
    public function export(Request $request)
    {
        $type = $request->get('type');
        $assignments = CaregiverAssignment::with(['staff', 'patient'])->get();
        if ($type === 'csv') {
            $csvData = "Patient Name,Staff Name,Shift Start,Shift End,Status\n";
            foreach ($assignments as $a) {
                $patientName = $a->patient->full_name ?? 'N/A';
                $staffName = ($a->staff->first_name ?? '') . ' ' . ($a->staff->last_name ?? '');
                $csvData .= "\"{$patientName}\",\"{$staffName}\",\"{$a->shift_start}\",\"{$a->shift_end}\",\"{$a->status}\"\n";
            }
            return Response::make($csvData, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="assignments.csv"']);
        }
        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.assignments', ['assignments' => $assignments])->setPaper('a4', 'landscape');
            return $pdf->stream('assignments.pdf');
        }
        return abort(400, 'Invalid export type');
    }
}
