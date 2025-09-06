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

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->newQuery()->withList();

        if ($search = $request->input('search')) {
            $this->applySearch($query, $search);
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
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
