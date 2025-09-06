<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMedicalDocumentRequest;
use App\Http\Resources\MedicalDocumentResource;
use App\Models\MedicalDocument;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // List my documents (as patient) or created by me (as staff)
    public function my(Request $request)
    {
        $user = $request->user();
        $query = MedicalDocument::with(['patient', 'createdBy'])
            ->orderByDesc('document_date');

        if ($user->staff) {
            $query->where('created_by_staff_id', $user->staff->id);
        } else {
            $patient = Patient::where('email', $user->email)->first();
            if (! $patient) {
                return response()->json(['data' => []]);
            }
            $query->where('patient_id', $patient->id);
        }

        $docs = $query->paginate($request->integer('per_page', 20));

        return MedicalDocumentResource::collection($docs);
    }

    // Secure download
    public function download(Request $request, MedicalDocument $document)
    {
        $this->authorize('view', $document);

        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::disk('public')->download($document->file_path, $document->title ?: 'document.pdf');
    }

    // Staff upload
    public function store(StoreMedicalDocumentRequest $request)
    {
        $this->authorize('create', MedicalDocument::class);

        $validated = $request->validated();
        $path = $request->file('file')->store('medical-documents', 'public');

        $doc = MedicalDocument::create([
            'patient_id' => $validated['patient_id'],
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'document_date' => $validated['document_date'],
            'file_path' => $path,
            'summary' => $validated['summary'] ?? null,
            'created_by_staff_id' => $request->user()->staff->id,
        ]);

        return new MedicalDocumentResource($doc->load(['patient', 'createdBy']));
    }
}
