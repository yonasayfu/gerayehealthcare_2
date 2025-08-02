<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use App\Models\EventRecommendation;
use App\Models\Patient;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class EventRecommendationController extends Controller
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
        $query = EventRecommendation::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('patient_name', 'ilike', "%{$search}%")
                  ->orWhere('source_channel', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $sortField = $request->input('sort');
            $sortDirection = $request->input('direction', 'asc');

            $sortableFields = ['patient_name', 'source_channel', 'status', 'created_at'];
            if (in_array($sortField, $sortableFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $recommendations = $query->paginate($request->input('per_page', 5))->withQueryString();

        return Inertia::render('Admin/EventRecommendations/Index', [
            'recommendations' => $recommendations,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/EventRecommendations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'source_channel' => 'required|string|max:255',
            'recommended_by_name' => 'nullable|string|max:255',
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        EventRecommendation::create($validated);

        return redirect()->route('admin.event-recommendations.index')
            ->with('success', 'Event recommendation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventRecommendation $eventRecommendation)
    {
        return Inertia::render('Admin/EventRecommendations/Show', [
            'eventRecommendation' => $eventRecommendation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventRecommendation $eventRecommendation)
    {
        return Inertia::render('Admin/EventRecommendations/Edit', [
            'eventRecommendation' => $eventRecommendation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventRecommendation $eventRecommendation)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'source_channel' => 'required|string|max:255',
            'recommended_by_name' => 'nullable|string|max:255',
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        $eventRecommendation->update($validated);

        if ($validated['status'] === 'approved') {
            // Find or create patient
            $patient = Patient::firstOrCreate(
                ['phone_number' => $validated['patient_phone']],
                [
                    'full_name' => $validated['patient_name'],
                    'email' => null, // Assuming no email in recommendation
                    'date_of_birth' => null, // Assuming no DOB in recommendation
                    'gender' => null, // Assuming no gender in recommendation
                    'address' => null, // Assuming no address in recommendation
                    'emergency_contact' => null,
                    'source' => 'Event Recommendation',
                    'geolocation' => null,
                    'patient_code' => 'PAT-' . str_pad(Patient::count() + 1, 5, '0', STR_PAD_LEFT),
                ]
            );

            // Link the recommendation to the patient
            $eventRecommendation->linked_patient_id = $patient->id;
            $eventRecommendation->save();

            // Create event participant record
            EventParticipant::firstOrCreate(
                [
                    'event_id' => $validated['event_id'],
                    'patient_id' => $patient->id,
                ],
                ['status' => 'registered']
            );
        }

        return redirect()->route('admin.event-recommendations.index')
            ->with('success', 'Event recommendation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventRecommendation $eventRecommendation)
    {
        $eventRecommendation->delete();

        return redirect()->route('admin.event-recommendations.index')
            ->with('success', 'Event recommendation deleted successfully.');
    }

    public function export(Request $request)
    {
        return $this->handleExport($request, EventRecommendation::class, AdditionalExportConfigs::getEventRecommendationConfig());
    }

    public function printSingle(EventRecommendation $eventRecommendation)
    {
        return $this->handlePrintSingle($eventRecommendation, AdditionalExportConfigs::getEventRecommendationConfig());
    }

    public function printCurrent(Request $request)
    {
        return $this->handlePrintCurrent($request, EventRecommendation::class, AdditionalExportConfigs::getEventRecommendationConfig());
    }

    public function printAll(Request $request)
    {
        return $this->handlePrintAll($request, EventRecommendation::class, AdditionalExportConfigs::getEventRecommendationConfig());
    }
}