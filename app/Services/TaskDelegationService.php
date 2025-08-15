<?php

namespace App\Services;

use App\DTOs\CreateTaskDelegationDTO;
use App\Models\TaskDelegation;
use Illuminate\Http\Request;

class TaskDelegationService extends BaseService
{
    public function __construct(TaskDelegation $taskDelegation)
    {
        parent::__construct($taskDelegation);
    }

    protected function applySearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%");
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->with(array_merge(['assignee'], $with));

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($request->input('per_page', 10));
    }

    
}
