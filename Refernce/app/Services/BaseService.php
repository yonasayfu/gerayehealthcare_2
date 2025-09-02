<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    /**
     * The model class name that this service operates on
     */
    protected string $model;

    /**
     * Validation rules for this service
     */
    protected array $validationRules = [];

    /**
     * Validation messages for this service
     */
    protected array $validationMessages = [];

    /**
     * Create a new service instance
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get all records with optional filtering
     */
    public function getAll(Request $request, array $with = []): LengthAwarePaginator
    {
        $query = $this->newQuery()->with($with);

        // Apply search filter
        if ($request->has('search')) {
            $this->applySearch($query, $request->input('search'));
        }

        // Apply sorting
        if ($request->has('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->input('sort'), $direction);
        }

        // Apply filters
        $this->applyFilters($query, $request);

        return $query->paginate($request->input('per_page', 15));
    }

    /**
     * Find a record by ID
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findById(int $id, array $with = [])
    {
        return $this->newQuery()->with($with)->find($id);
    }

    /**
     * Find a record by ID or fail
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByIdOrFail(int $id, array $with = [])
    {
        return $this->newQuery()->with($with)->findOrFail($id);
    }

    /**
     * Create a new record
     */
    public function create(array $data): Model
    {
        // Validate data
        $this->validateData($data, 'create');

        // Create the record
        return $this->newQuery()->create($data);
    }

    /**
     * Update an existing record
     */
    public function update(int $id, array $data): Model
    {
        // Validate data
        $this->validateData($data, 'update');

        // Find and update the record
        $record = $this->findByIdOrFail($id);
        $record->update($data);

        return $record;
    }

    /**
     * Delete a record
     */
    public function delete(int $id): ?bool
    {
        $record = $this->findByIdOrFail($id);

        return $record->delete();
    }

    /**
     * Bulk delete records
     */
    public function bulkDelete(array $ids): int
    {
        return $this->newQuery()->whereIn('id', $ids)->delete();
    }

    /**
     * Create a new query instance
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newQuery()
    {
        return app($this->model)->newQuery();
    }

    /**
     * Validate data against rules
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateData(array $data, string $context = 'default'): void
    {
        $rules = $this->getValidationRules($context);
        $messages = $this->getValidationMessages($context);

        if (! empty($rules)) {
            validator($data, $rules, $messages)->validate();
        }
    }

    /**
     * Get validation rules for a specific context
     */
    protected function getValidationRules(string $context = 'default'): array
    {
        return $this->validationRules[$context] ?? $this->validationRules['default'] ?? [];
    }

    /**
     * Get validation messages for a specific context
     */
    protected function getValidationMessages(string $context = 'default'): array
    {
        return $this->validationMessages[$context] ?? $this->validationMessages['default'] ?? [];
    }

    /**
     * Apply search filter to query
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    protected function applySearch($query, string $search): void
    {
        // This should be overridden in child classes to implement specific search logic
        // Example implementation:
        // $query->where('name', 'LIKE', "%{$search}%");
    }

    /**
     * Apply additional filters to query
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    protected function applyFilters($query, Request $request): void
    {
        // This should be overridden in child classes to implement specific filter logic
        // Example implementation:
        // if ($request->has('status')) {
        //     $query->where('status', $request->input('status'));
        // }
    }

    /**
     * Log service activity
     */
    protected function log(string $message, string $level = 'info', array $context = []): void
    {
        Log::log($level, '['.static::class.'] '.$message, $context);
    }

    /**
     * Dispatch an event
     */
    protected function dispatchEvent(object $event): void
    {
        Event::dispatch($event);
    }

    /**
     * Run a database transaction
     *
     * @return mixed
     */
    protected function transaction(callable $callback)
    {
        return DB::transaction($callback);
    }
}
