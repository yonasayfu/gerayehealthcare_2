<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaregiverAssignment;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class CaregiverAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CaregiverAssignment::with(['staff', 'patient']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            })->orWhereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhere('status', 'like', "%{$search}%");
        }

        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }

        $perPage = $request->get('per_page', 10);

        return Inertia::render('Admin/CaregiverAssignments/Index', [
            'assignments' => $query->paginate($perPage)->withQueryString(),
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/CaregiverAssignments/Create', [
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            'staff' => Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'shift_start' => 'nullable|date',
            'shift_end' => 'nullable|date|after_or_equal:shift_start',
            'status' => 'required|string|max:255',
        ]);

        CaregiverAssignment::create($request->all());

        return redirect()->route('assignments.index');
    }
 /**
     * Display the specified resource.
     */
    public function show(CaregiverAssignment $assignment)
    {
        $assignment->load(['staff', 'patient']);
        return Inertia::render('Admin/CaregiverAssignments/Show', [
            'assignment' => $assignment,
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CaregiverAssignment $assignment)
    {
        // Load the relations for the form
        $assignment->load(['staff', 'patient']);

        return Inertia::render('Admin/CaregiverAssignments/Edit', [
            'assignment' => $assignment,
            'patients' => Patient::orderBy('full_name')->get(['id', 'full_name']),
            'staff' => Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CaregiverAssignment $assignment)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'shift_start' => 'nullable|date',
            'shift_end' => 'nullable|date|after_or_equal:shift_start',
            'status' => 'required|string|max:255',
        ]);

        $assignment->update($request->all());

        return redirect()->route('assignments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CaregiverAssignment $assignment)
    {
        $assignment->delete();
        return back();
    }

    /**
     * Export data as CSV or PDF.
     */
    public function export(Request $request)
    {
        $type = $request->get('type');
        $assignments = CaregiverAssignment::with(['staff', 'patient'])->get();

        if ($type === 'csv') {
            $csvData = "Patient Name,Staff Name,Shift Start,Shift End,Status\n";
            foreach ($assignments as $assignment) {
                $patientName = $assignment->patient->full_name ?? 'N/A';
                $staffName = ($assignment->staff->first_name ?? '') . ' ' . ($assignment->staff->last_name ?? '');
                $csvData .= "\"{$patientName}\",\"{$staffName}\",\"{$assignment->shift_start}\",\"{$assignment->shift_end}\",\"{$assignment->status}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="assignments.csv"',
            ]);
        }

        if ($type === 'pdf') {
            // Note: You will need to create the 'pdf.assignments' view file.
            $pdf = Pdf::loadView('pdf.assignments', ['assignments' => $assignments])
                ->setPaper('a4', 'landscape');
            return $pdf->stream('assignments.pdf');
        }

        return abort(400, 'Invalid export type');
    }
}