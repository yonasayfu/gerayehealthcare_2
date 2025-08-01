<?php

namespace App\Http\Controllers\Admin;

use App\Models\EventStaffAssignment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class EventStaffAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:' . \App\Enums\RoleEnum::SUPER_ADMIN->value . '|' . \App\Enums\RoleEnum::ADMIN->value);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EventStaffAssignment::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('role', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            $sortableFields = ['event_id', 'staff_id', 'role', 'created_at'];
            if (in_array($sortField, $sortableFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $assignments = $query->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('Admin/EventStaffAssignments/Index', [
            'assignments' => $assignments,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/EventStaffAssignments/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'staff_id' => 'required|exists:staff,id',
            'role' => 'required|string|max:255',
        ]);

        EventStaffAssignment::create($validated);

        return redirect()->route('admin.event-staff-assignments.index')
            ->with('success', 'Event staff assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventStaffAssignment $eventStaffAssignment)
    {
        return Inertia::render('Admin/EventStaffAssignments/Show', [
            'assignment' => $eventStaffAssignment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventStaffAssignment $eventStaffAssignment)
    {
        return Inertia::render('Admin/EventStaffAssignments/Edit', [
            'assignment' => $eventStaffAssignment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventStaffAssignment $eventStaffAssignment)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'staff_id' => 'required|exists:staff,id',
            'role' => 'required|string|max:255',
        ]);

        $eventStaffAssignment->update($validated);

        return redirect()->route('admin.event-staff-assignments.index')
            ->with('success', 'Event staff assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventStaffAssignment $eventStaffAssignment)
    {
        $eventStaffAssignment->delete();

        return redirect()->route('admin.event-staff-assignments.index')
            ->with('success', 'Event staff assignment deleted successfully.');
    }

    public function export(Request $request)
    {
        $type = $request->get('type');
        $assignments = EventStaffAssignment::select('event_id', 'staff_id', 'role')->get();

        if ($type === 'csv') {
            $csvData = "Event ID,Staff ID,Role\n";            foreach ($assignments as $assignment) {                $csvData .= "\"{$assignment->event_id}\",\"{$assignment->staff_id}\",\"{$assignment->role}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=\"event-staff-assignments.csv\"',
            ]);
        }

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('pdf.event-staff-assignments', ['assignments' => $assignments])->setPaper('a4', 'landscape');
            return $pdf->stream('event-staff-assignments.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(EventStaffAssignment $eventStaffAssignment)
    {
        $pdf = Pdf::loadView('pdf.event-staff-assignment-single', ['assignment' => $eventStaffAssignment])->setPaper('a4', 'portrait');
        return $pdf->stream("event-staff-assignment-{$eventStaffAssignment->id}.pdf");
    }

    public function printCurrent(Request $request)
    {
        $query = EventStaffAssignment::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('role', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $assignments = $query->paginate($request->input('per_page', 10))->appends($request->except('page'));

        return Inertia::render('Admin/EventStaffAssignments/PrintCurrent', ['assignments' => $assignments->items()]);
    }

    public function printAll(Request $request)
    {
        $assignments = EventStaffAssignment::orderBy('role')->get();

        $pdf = Pdf::loadView('pdf.event-staff-assignments', ['assignments' => $assignments])->setPaper('a4', 'landscape');
        return $pdf->stream('event-staff-assignments.pdf');
    }
}