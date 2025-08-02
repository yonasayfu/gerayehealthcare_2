<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class EventParticipantController extends Controller
{
    use ExportableTrait;
    public function __construct()
    {
        $this->middleware('role:' . \App\Enums\RoleEnum::SUPER_ADMIN->value . '|' . \App\Enums\RoleEnum::ADMIN->value);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EventParticipant::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('status', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            $sortableFields = ['event_id', 'patient_id', 'status', 'created_at'];
            if (in_array($sortField, $sortableFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $participants = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Admin/EventParticipants/Index', [
            'participants' => $participants,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/EventParticipants/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'patient_id' => 'required|exists:patients,id',
            'status' => 'required|string|in:registered,attended,no-show',
        ]);

        EventParticipant::create($validated);

        return redirect()->route('admin.event-participants.index')
            ->with('success', 'Event participant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventParticipant $eventParticipant)
    {
        return Inertia::render('Admin/EventParticipants/Show', [
            'participant' => $eventParticipant,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventParticipant $eventParticipant)
    {
        return Inertia::render('Admin/EventParticipants/Edit', [
            'participant' => $eventParticipant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventParticipant $eventParticipant)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'patient_id' => 'required|exists:patients,id',
            'status' => 'required|string|in:registered,attended,no-show',
        ]);

        $eventParticipant->update($validated);

        return redirect()->route('admin.event-participants.index')
            ->with('success', 'Event participant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventParticipant $eventParticipant)
    {
        $eventParticipant->delete();

        return redirect()->route('admin.event-participants.index')
            ->with('success', 'Event participant deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, EventParticipant::class, AdditionalExportConfigs::getEventParticipantConfig());
    }

    public function printSingle(EventParticipant $eventParticipant)
    {
        return $this->handlePrintSingle($eventParticipant, AdditionalExportConfigs::getEventParticipantConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EventParticipant::class, AdditionalExportConfigs::getEventParticipantConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EventParticipant::class, AdditionalExportConfigs::getEventParticipantConfig());
    }
}
