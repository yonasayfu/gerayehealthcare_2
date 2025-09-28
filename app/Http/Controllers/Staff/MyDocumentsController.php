<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MedicalDocument;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MyDocumentsController extends Controller
{
    public function index()
    {
        $staffId = optional(Auth::user()->staff)->id;
        if (! $staffId) {
            return redirect()->route('dashboard')
                ->with('banner', 'Your account is not linked to a staff profile.')
                ->with('bannerStyle', 'danger');
        }

        $docs = MedicalDocument::with('patient')
            ->where('created_by_staff_id', $staffId)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->through(function ($d) {
                return [
                    'id' => $d->id,
                    'title' => $d->title,
                    'patient' => [
                        'id' => $d->patient?->id,
                        'full_name' => $d->patient?->full_name,
                    ],
                    'created_at' => (string) $d->created_at,
                ];
            });

        return Inertia::render('Staff/MyDocuments/Index', [
            'documents' => $docs,
        ]);
    }

    public function show(MedicalDocument $document)
    {
        $this->authorize('view', $document);

        $doc = [
            'id' => $document->id,
            'title' => $document->title,
            'document_type' => $document->document_type,
            'document_date' => (string) $document->document_date,
            'summary' => $document->summary,
            'patient' => [
                'id' => $document->patient?->id,
                'full_name' => $document->patient?->full_name,
            ],
            'created_at' => (string) $document->created_at,
        ];

        return Inertia::render('Staff/MyDocuments/Show', [
            'document' => $doc,
        ]);
    }

    public function create()
    {
        $staff = Auth::user()->staff;
        if (! $staff) {
            return redirect()->route('dashboard')
                ->with('banner', 'Your account is not linked to a staff profile.')
                ->with('bannerStyle', 'danger');
        }
        $this->authorize('create', MedicalDocument::class);

        // Offer recent patients this staff served
        $patientIds = \App\Models\VisitService::where('staff_id', $staff->id)
            ->orderByDesc('scheduled_at')
            ->limit(25)
            ->pluck('patient_id')
            ->unique()
            ->values();
        $patients = \App\Models\Patient::whereIn('id', $patientIds)
            ->orderBy('full_name')
            ->get(['id', 'full_name']);

        return Inertia::render('Staff/MyDocuments/Create', [
            'patients' => $patients,
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $staff = Auth::user()->staff;
        if (! $staff) {
            return redirect()->route('dashboard')
                ->with('banner', 'Your account is not linked to a staff profile.')
                ->with('bannerStyle', 'danger');
        }
        $this->authorize('create', MedicalDocument::class);

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'document_type' => ['required', 'in:doctor_note,lab_request,lab_result,prescription,other'],
            'title' => ['required', 'string', 'max:255'],
            'document_date' => ['required', 'date'],
            'summary' => ['nullable', 'string'],
        ]);

        $doc = MedicalDocument::create([
            'patient_id' => $validated['patient_id'],
            'medical_visit_id' => null,
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'document_date' => $validated['document_date'],
            'summary' => $validated['summary'] ?? null,
            'is_printed' => false,
            'created_by_staff_id' => $staff->id,
        ]);

        return redirect()->route('staff.my-documents.show', $doc->id)
            ->with('banner', 'Document created.')
            ->with('bannerStyle', 'success');
    }

    public function edit(MedicalDocument $document)
    {
        $this->authorize('update', $document);

        $staff = Auth::user()->staff;
        $patientIds = \App\Models\VisitService::where('staff_id', $staff->id)
            ->orderByDesc('scheduled_at')
            ->limit(25)
            ->pluck('patient_id')
            ->unique()
            ->values();
        $patients = \App\Models\Patient::whereIn('id', $patientIds)
            ->orderBy('full_name')
            ->get(['id', 'full_name']);

        return Inertia::render('Staff/MyDocuments/Edit', [
            'document' => [
                'id' => $document->id,
                'patient_id' => $document->patient_id,
                'document_type' => $document->document_type,
                'title' => $document->title,
                'document_date' => (string) $document->document_date,
                'summary' => $document->summary,
            ],
            'patients' => $patients,
        ]);
    }

    public function update(\Illuminate\Http\Request $request, MedicalDocument $document)
    {
        $this->authorize('update', $document);

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'document_type' => ['required', 'in:doctor_note,lab_request,lab_result,prescription,other'],
            'title' => ['required', 'string', 'max:255'],
            'document_date' => ['required', 'date'],
            'summary' => ['nullable', 'string'],
        ]);

        $document->update($validated);

        return redirect()->route('staff.my-documents.show', $document->id)
            ->with('banner', 'Document updated.')
            ->with('bannerStyle', 'success');
    }

    public function destroy(MedicalDocument $document)
    {
        $this->authorize('delete', $document);
        $document->delete();
        return redirect()->route('staff.my-documents.index')
            ->with('banner', 'Document deleted.')
            ->with('bannerStyle', 'success');
    }
}
