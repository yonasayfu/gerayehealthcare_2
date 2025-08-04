<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Exceptions\ResourceNotFoundException;

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

    public function create(array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        return $this->model->create($data);
    }

    public function getById(int $id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            throw new ResourceNotFoundException(class_basename($this->model) . ' not found.');
        }
        return $model;
    }

    public function update(int $id, array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
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
