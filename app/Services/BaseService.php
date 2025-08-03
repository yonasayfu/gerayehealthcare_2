<?php

namespace App\Services;

use Illuminate\Http\Request;

class BaseService
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getAll(Request $request)
    {
        $query = $this->model->query();

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        }

        return $query->paginate($request->input('per_page', 10));
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $model = $this->model->findOrFail($id);
        $model->update($data);
        return $model;
    }

    public function delete(int $id): void
    {
        $this->model->findOrFail($id)->delete();
    }

    protected function applySearch($query, $search)
    {
        // This method should be overridden in the child service class
    }
}
