<?php

namespace App\Services;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionService extends BaseService
{
    public function __construct(Prescription $prescription)
    {
        parent::__construct($prescription);
    }

    public function create(array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        $items = $data['items'] ?? [];
        unset($data['items']);

        $prescription = $this->model->create($data);
        if (is_array($items) && ! empty($items)) {
            foreach ($items as $it) {
                $prescription->items()->create([
                    'medication_name' => $it['medication_name'] ?? '',
                    'dosage' => $it['dosage'] ?? null,
                    'frequency' => $it['frequency'] ?? null,
                    'duration' => $it['duration'] ?? null,
                    'notes' => $it['notes'] ?? null,
                ]);
            }
        }
        return $prescription;
    }

    public function update(int $id, array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        $items = $data['items'] ?? null;
        unset($data['items']);

        $prescription = $this->model->findOrFail($id);
        $prescription->update($data);

        if (is_array($items)) {
            // Simple reconcile: delete missing and upsert provided
            $existing = $prescription->items()->pluck('id')->all();
            $keepIds = [];
            foreach ($items as $it) {
                if (!empty($it['id']) && in_array($it['id'], $existing, true)) {
                    $prescription->items()->where('id', $it['id'])->update([
                        'medication_name' => $it['medication_name'] ?? '',
                        'dosage' => $it['dosage'] ?? null,
                        'frequency' => $it['frequency'] ?? null,
                        'duration' => $it['duration'] ?? null,
                        'notes' => $it['notes'] ?? null,
                    ]);
                    $keepIds[] = $it['id'];
                } else {
                    $new = $prescription->items()->create([
                        'medication_name' => $it['medication_name'] ?? '',
                        'dosage' => $it['dosage'] ?? null,
                        'frequency' => $it['frequency'] ?? null,
                        'duration' => $it['duration'] ?? null,
                        'notes' => $it['notes'] ?? null,
                    ]);
                    $keepIds[] = $new->id;
                }
            }
            // Remove items not present
            $prescription->items()->whereNotIn('id', $keepIds)->delete();
        }

        return $prescription;
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->newQuery()->withList();

        if ($search = $request->input('search')) {
            $this->applySearch($query, $search);
        }

        // Status filter (All/draft/final/dispensed/cancelled)
        if ($request->filled('status') && $request->input('status') !== 'All') {
            $query->where('status', $request->input('status'));
        }

        // Sorting with allowlist
        $allowed = ['prescribed_date', 'status', 'id'];
        $sort = $request->input('sort');
        $direction = strtolower((string) $request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        if ($sort && in_array($sort, $allowed, true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderByDesc('prescribed_date');
        }

        return $query->paginate($request->input('per_page', 15));
    }

    public function getById(int $id, array $with = [])
    {
        $with = array_unique(array_merge($with, ['patient', 'document', 'items', 'createdBy']));
        $model = $this->model->with($with)->find($id);
        if (! $model) {
            throw new \App\Exceptions\ResourceNotFoundException(class_basename($this->model).' not found.');
        }

        return $model;
    }

    protected function applySearch($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('status', 'like', "%{$search}%")
                ->orWhere('instructions', 'like', "%{$search}%")
                ->orWhereHas('items', function ($iq) use ($search) {
                    $iq->where('medication_name', 'like', "%{$search}%");
                });
        });
    }
}
