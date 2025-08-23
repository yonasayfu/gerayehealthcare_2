<?php

namespace App\Services;

use App\Models\MedicalDocument;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MedicalDocumentService extends BaseService
{
    public function __construct(MedicalDocument $medicalDocument)
    {
        parent::__construct($medicalDocument);
    }

    public function getAll(Request $request, array $with = [])
    {
        // Use lightweight list scope for performance
        $query = $this->model->newQuery()->withList();

        // Search by title, type, summary
        if ($search = $request->input('search')) {
            $this->applySearch($query, $search);
        }

        // Sort
        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderByDesc('document_date');
        }

        return $query->paginate($request->input('per_page', 15));
    }

    public function getById(int $id, array $with = [])
    {
        $with = array_unique(array_merge($with, ['patient', 'visit', 'createdBy', 'prescription']));
        $model = $this->model->with($with)->find($id);
        if (! $model) {
            throw new \App\Exceptions\ResourceNotFoundException(class_basename($this->model) . ' not found.');
        }
        return $model;
    }

    protected function applySearch($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('document_type', 'like', "%{$search}%")
              ->orWhere('summary', 'like', "%{$search}%");
        });
    }

    /**
     * Override create to handle file upload under 'file' key and set 'file_path'.
     */
    public function create(array|object $data)
    {
        $payload = is_object($data) ? (array) $data : $data;
        if (isset($payload['file']) && $payload['file'] instanceof UploadedFile) {
            $payload['file_path'] = $payload['file']->store('medical_documents', 'public');
            unset($payload['file']);
        }
        return parent::create($payload);
    }

    /**
     * Override update to support replacing the stored file and deleting the old one.
     */
    public function update(int $id, array|object $data)
    {
        $payload = is_object($data) ? (array) $data : $data;
        $model = $this->model->findOrFail($id);

        if (isset($payload['file']) && $payload['file'] instanceof UploadedFile) {
            // Delete old file if exists
            if (!empty($model->file_path)) {
                Storage::disk('public')->delete($model->file_path);
            }
            $payload['file_path'] = $payload['file']->store('medical_documents', 'public');
            unset($payload['file']);
        }

        // Avoid overwriting with nulls for missing fields
        $payload = array_filter($payload, fn($v) => !is_null($v));

        $model->update($payload);
        return $model;
    }

    /**
     * Ensure file deletion on record removal.
     */
    public function delete(int $id): void
    {
        $model = $this->model->findOrFail($id);
        if (!empty($model->file_path)) {
            Storage::disk('public')->delete($model->file_path);
        }
        $model->delete();
    }
}
