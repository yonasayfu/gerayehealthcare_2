<?php

namespace App\Services\LeadSource;

use App\Models\LeadSource;
use App\Services\Base\BaseService;
use Illuminate\Http\Request;

class LeadSourceService extends BaseService
{
    public function __construct(LeadSource $leadSource)
    {
        parent::__construct($leadSource);
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'ilike', "%{$search}%")
            ->orWhere('category', 'ilike', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with($with);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 5));
    }

    public function toggleStatus(int $id): LeadSource
    {
        $leadSource = $this->getById($id);
        $leadSource->is_active = ! $leadSource->is_active;
        $leadSource->save();

        return $leadSource;
    }
}
