<?php

namespace App\Services\MedicalDocument;

use App\Models\MedicalDocument;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MedicalDocumentService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new MedicalDocument());
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
            throw new \App\Exceptions\ResourceNotFoundException(class_basename($this->model).' not found.');
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
        \Illuminate\Support\Facades\Log::info('MedicalDocumentService update method called', ['id' => $id, 'data' => $data]);

        $payload = is_object($data) ? (array) $data : $data;
        $model = $this->model->findOrFail($id);

        if (isset($payload['file']) && $payload['file'] instanceof UploadedFile) {
            \Illuminate\Support\Facades\Log::info('File detected for upload/replacement', ['file_name' => $payload['file']->getClientOriginalName()]);
            // Delete old file if exists
            if (! empty($model->file_path)) {
                Storage::disk('public')->delete($model->file_path);
                \Illuminate\Support\Facades\Log::info('Old file deleted', ['old_file_path' => $model->file_path]);
            }
            $payload['file_path'] = $payload['file']->store('medical_documents', 'public');
            unset($payload['file']);
            \Illuminate\Support\Facades\Log::info('New file stored', ['new_file_path' => $payload['file_path']]);
        }

        // Avoid overwriting with nulls for missing fields
        $payload = array_filter($payload, fn ($v) => ! is_null($v));
        \Illuminate\Support\Facades\Log::info('Payload after null filter', ['payload' => $payload]);

        $model->update($payload);
        \Illuminate\Support\Facades\Log::info('Model updated in database', ['model_id' => $model->id, 'updated_attributes' => $payload]);


        return $model;
    }

    /**
     * Ensure file deletion on record removal.
     */
    public function delete(int $id): void
    {
        $model = $this->model->findOrFail($id);
        if (! empty($model->file_path)) {
            Storage::disk('public')->delete($model->file_path);
        }
        $model->delete();
    }
}
