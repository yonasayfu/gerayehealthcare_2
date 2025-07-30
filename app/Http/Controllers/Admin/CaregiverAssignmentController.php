<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaregiverAssignment;
use App\Models\Patient;
use App\Models\Staff;
use App\Rules\StaffIsAvailableForShift;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\NewCaregiverAssignment;
use Illuminate\Support\Facades\Notification;
use Inertia\Response as InertiaResponse; // Use an alias to avoid conflicts
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response as LaravelResponse; // Use an alias for Laravel's Response

class CaregiverAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $query = CaregiverAssignment::with(['patient', 'staff']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('patient', fn($q) => $q->where('full_name', 'ilike', "%{$search}%"))
                  ->orWhereHas('staff', fn($q) => $q->where('first_name', 'ilike', "%{$search}%"));
        }

        if ($request->filled('sort')) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $assignments = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Admin/CaregiverAssignments/Index', [
            'assignments' => $assignments,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

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
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'status' => 'required|string|max:255',
            'shift_end' => 'required|date|after_or_equal:shift_start',
            'shift_start' => [
                'required',
                'date',
                new StaffIsAvailableForShift($request->staff_id, $request->shift_end)
            ],
        ]);

        $assignment = CaregiverAssignment::create($request->all());

        // Notify the assigned staff member
        $staff = Staff::find($request->staff_id);
        if ($staff) {
            Notification::send($staff, new NewCaregiverAssignment($assignment));
        }

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment created successfully.');
    }

    public function show(CaregiverAssignment $assignment)
    {
        $assignment->load(['staff', 'patient']);
        return Inertia::render('Admin/CaregiverAssignments/Show', [
            'assignment' => $assignment,
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
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'status' => 'required|string|max:255',
            'shift_end' => 'required|date|after_or_equal:shift_start',
            'shift_start' => [
                'required',
                'date',
                new StaffIsAvailableForShift($request->staff_id, $request->shift_end, $assignment->id)
            ],
        ]);

        $assignment->update($request->all());

        // Notify the assigned staff member
        $staff = Staff::find($request->staff_id);
        if ($staff) {
            Notification::send($staff, new NewCaregiverAssignment($assignment));
        }

        return redirect()->route('admin.assignments.index')->with('success', 'Assignment updated successfully.');
    }

    public function destroy(CaregiverAssignment $assignment)
    {
        $assignment->delete();
        return back()->with('success', 'Assignment deleted successfully.');
    }

    public function printAll(Request $request)
    {
        $query = CaregiverAssignment::with(['patient', 'staff']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('patient', fn($q) => $q->where('full_name', 'ilike', "%{$search}%"))
                  ->orWhereHas('staff', fn($q) => $q->where('first_name', 'ilike', "%{$search}%"));
        }

        if ($request->filled('sort')) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $assignments = $query->get();

        return Inertia::render('Admin/CaregiverAssignments/PrintAll', [
            'assignments' => $assignments,
        ]);
    }

    public function printSingle(CaregiverAssignment $assignment)
    {
        $assignment->load(['staff', 'patient']);
        $pdf = Pdf::loadView('pdf.assignment-single', ['assignment' => $assignment])->setPaper('a4', 'portrait');
        return $pdf->stream("assignment-{$assignment->id}.pdf");
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
            return LaravelResponse::make($csvData, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="assignments.csv"']);
        }

        

        return abort(400, 'Invalid export type');
    }
}
