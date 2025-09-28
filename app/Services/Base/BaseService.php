<?php

namespace App\Services\Base;

use App\Exceptions\ResourceNotFoundException;
use Illuminate\Http\Request;

class BaseService
{
    protected $model;

    public function __construct($model = null)
    {
        if ($model !== null) {
            $this->model = $model;
        }
    }

    public function getAll(Request $request, array $with = [])
    {
        $query = $this->model->query()->with($with);

        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        }

        // Preserve current query string (search, sort, per_page, etc.) on pagination links
        return $query->paginate($request->input('per_page', 15))->withQueryString();
    }

    public function create(array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;

        return $this->model->create($data);
    }

    public function getById(int $id, array $with = [])
    {
        $query = $this->model->query()->with($with);
        $model = $query->find($id);
        if (! $model) {
            throw new ResourceNotFoundException(class_basename($this->model).' not found.');
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
