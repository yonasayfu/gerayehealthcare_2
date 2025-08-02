<?php

namespace App\Http\Controllers\Admin;

use App\Models\EventRecommendation;
use App\Models\Patient;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class EventRecommendationController extends Controller
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
        $type = $request->get('type');
        $recommendations = EventRecommendation::select('event_id', 'source_channel', 'recommended_by_name', 'patient_name', 'patient_phone', 'notes', 'status')->get();

        if ($type === 'csv') {
            $csvData = "Event ID,Source Channel,Recommended By,Patient Name,Patient Phone,Notes,Status\n";
            foreach ($recommendations as $recommendation) {
                $csvData .= "\"{$recommendation->event_id}\",\"{$recommendation->source_channel}\",\"{$recommendation->recommended_by_name}\",\"{$recommendation->patient_name}\",\"{$recommendation->patient_phone}\",\"{$recommendation->notes}\",\"{$recommendation->status}\"\n";
            }

            return Response::make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=\"event-recommendations.csv\"',
            ]);
        }

        if ($type === 'pdf') {
            $data = $recommendations->map(function($recommendation) {
                return [
                    'event_id' => $recommendation->event_id,
                    'source_channel' => $recommendation->source_channel,
                    'recommended_by_name' => $recommendation->recommended_by_name,
                    'patient_name' => $recommendation->patient_name,
                    'patient_phone' => $recommendation->patient_phone,
                    'notes' => $recommendation->notes,
                    'status' => $recommendation->status,
                ];
            })->toArray();

            $columns = [
                ['key' => 'event_id', 'label' => 'Event ID'],
                ['key' => 'source_channel', 'label' => 'Source Channel'],
                ['key' => 'recommended_by_name', 'label' => 'Recommended By'],
                ['key' => 'patient_name', 'label' => 'Patient Name'],
                ['key' => 'patient_phone', 'label' => 'Patient Phone'],
                ['key' => 'notes', 'label' => 'Notes'],
                ['key' => 'status', 'label' => 'Status'],
            ];

            $title = 'Event Recommendations List';
            $documentTitle = 'Event Recommendations List';

            $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                        ->setPaper('a4', 'landscape');
            return $pdf->download('event-recommendations.pdf');
        }

        return abort(400, 'Invalid export type');
    }

    public function printSingle(EventRecommendation $eventRecommendation)
    {
        $data = [
            ['label' => 'Event ID', 'value' => $eventRecommendation->event_id],
            ['label' => 'Source', 'value' => $eventRecommendation->source_channel],
            ['label' => 'Recommended By', 'value' => $eventRecommendation->recommended_by_name],
            ['label' => 'Patient Name', 'value' => $eventRecommendation->patient_name],
            ['label' => 'Patient Phone', 'value' => $eventRecommendation->patient_phone],
            ['label' => 'Notes', 'value' => $eventRecommendation->notes],
            ['label' => 'Status', 'value' => $eventRecommendation->status],
        ];

        $columns = [
            ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
            ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
        ];

        $title = 'Event Recommendation Details';
        $documentTitle = 'Event Recommendation Details';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'portrait');
        return $pdf->stream("event-recommendation-{$eventRecommendation->id}.pdf");
    }

    public function printCurrent(Request $request)
    {
        $query = EventRecommendation::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('patient_name', 'ilike', "%{$search}%")
                  ->orWhere('source_channel', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort') && !empty($request->input('sort'))) {
            $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $recommendations = $query->get();

        $data = $recommendations->map(function($recommendation) {
            return [
                'patient_name' => $recommendation->patient_name,
                'source_channel' => $recommendation->source_channel,
                'status' => $recommendation->status,
            ];
        })->toArray();

        $columns = [
            ['key' => 'patient_name', 'label' => 'Patient Name'],
            ['key' => 'source_channel', 'label' => 'Source'],
            ['key' => 'status', 'label' => 'Status'],
        ];

        $title = 'Event Recommendations List (Current View)';
        $documentTitle = 'Event Recommendations List (Current View)';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('event-recommendations-current.pdf');
    }

    public function printAll(Request $request)
    {
        $recommendations = EventRecommendation::orderBy('patient_name')->get();

        $data = $recommendations->map(function($recommendation) {
            return [
                'patient_name' => $recommendation->patient_name,
                'source_channel' => $recommendation->source_channel,
                'status' => $recommendation->status,
            ];
        })->toArray();

        $columns = [
            ['key' => 'patient_name', 'label' => 'Patient Name'],
            ['key' => 'source_channel', 'label' => 'Source'],
            ['key' => 'status', 'label' => 'Status'],
        ];

        $title = 'Event Recommendations List';
        $documentTitle = 'Event Recommendations List';

        $pdf = Pdf::loadView('print-layout', compact('title', 'data', 'columns', 'documentTitle'))
                    ->setPaper('a4', 'landscape');
        return $pdf->stream('event-recommendations.pdf');
    }
}