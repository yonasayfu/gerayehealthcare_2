<?php

namespace App\Services;

use App\DTOs\CreateInventoryItemDTO;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryItemService extends BaseService
{
    public function __construct(InventoryItem $inventoryItem)
    {
        parent::__construct($inventoryItem);
    }

    public function getAll(Request $request, array $with = [])
    {
        // Ensure supplier relation is always available for listings
        $with = array_unique(array_merge($with, ['supplier']));
        return parent::getAll($request, $with);
    }

    public function getById(int $id, array $with = [])
    {
        // Ensure supplier is always available for Show view and merge any additional relations
        $with = array_unique(array_merge(['supplier'], $with));
        $model = $this->model->with($with)->find($id);
        if (!$model) {
            throw new \App\Exceptions\ResourceNotFoundException(class_basename($this->model) . ' not found.');
        }
        return $model;
    }

    protected function applySearch($query, $search)
    {
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%");
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $item = $this->model->findOrFail($id);

            // Delete dependents to avoid FK violations
            // Relations defined in App\Models\InventoryItem
            $item->alerts()->delete();
            $item->maintenanceRecords()->delete();
            $item->requests()->delete();
            $item->transactions()->delete();

            $item->delete();
        });
    }
}
