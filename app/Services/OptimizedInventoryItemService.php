<?php

namespace App\Services;

use App\DTOs\CreateInventoryItemDTO;
use App\Models\InventoryItem;
use App\Http\Traits\ExportableTrait;
use App\Http\Config\AdditionalExportConfigs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedInventoryItemService extends OptimizedBaseService
{
    use ExportableTrait;

    protected $cacheTtl = 300; // 5 minutes for inventory data (needs frequent updates)

    public function __construct(InventoryItem $inventoryItem)
    {
        parent::__construct($inventoryItem);
        $this->cachePrefix = 'inventory_items';
    }

    public function getAll(Request $request, array $with = [])
    {
        // Always include supplier to prevent N+1 queries
        $defaultWith = ['supplier'];
        $with = array_unique(array_merge($defaultWith, $with));

        $cacheKey = $this->generateCacheKey('all', [
            'search' => $request->input('search'),
            'sort' => $request->input('sort'),
            'direction' => $request->input('direction'),
            'per_page' => $request->input('per_page', 15),
            'status' => $request->input('status'),
            'category' => $request->input('category'),
        ], $with);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request, $with) {
            $query = $this->model->query()->with($with);

            if ($request->has('search')) {
                $this->applySearch($query, $request->input('search'));
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            // Filter by category
            if ($request->filled('category')) {
                $query->where('item_category', $request->input('category'));
            }

            if ($request->has('sort')) {
                $direction = $request->input('direction', 'asc');
                $query->orderBy($request->input('sort'), $direction);
            } else {
                // Default ordering for better performance
                $query->orderBy('name');
            }

            return $query->paginate($request->input('per_page', 15));
        });
    }

    public function getById(int $id, array $with = [])
    {
        // Always include supplier and related inventory data
        $defaultWith = ['supplier', 'alerts', 'maintenanceRecords', 'transactions'];
        $with = array_unique(array_merge($defaultWith, $with));

        $cacheKey = $this->generateCacheKey('single', ['id' => $id], $with);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $with) {
            $item = $this->model->query()->with($with)->find($id);
            
            if (!$item) {
                throw new \App\Exceptions\ResourceNotFoundException('Inventory item not found.');
            }
            
            return $item;
        });
    }

    public function create(array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        
        DB::beginTransaction();
        try {
            $item = $this->model->create($data);
            
            // Automatically create alert if quantity is below reorder level
            $this->checkAndCreateReorderAlert($item);
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            return $item;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array|object $data)
    {
        $data = is_object($data) ? (array) $data : $data;
        
        DB::beginTransaction();
        try {
            $item = $this->model->findOrFail($id);
            $oldQuantity = $item->quantity_on_hand;
            
            $item->update($data);
            
            // Check if quantity changed and create alerts if needed
            if (isset($data['quantity_on_hand']) && $data['quantity_on_hand'] != $oldQuantity) {
                $this->checkAndCreateReorderAlert($item);
                $this->logInventoryTransaction($item, $oldQuantity, $data['quantity_on_hand']);
            }
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            return $item;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        DB::beginTransaction();
        try {
            $item = $this->model->findOrFail($id);

            // Delete dependents to avoid FK violations
            $item->alerts()->delete();
            $item->maintenanceRecords()->delete();
            $item->requests()->delete();
            $item->transactions()->delete();

            $item->delete();
            
            // Clear related caches
            $this->clearCaches();
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function applySearch($query, $search)
    {
        // Optimized search with multiple fields
        $query->where(function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%")
              ->orWhere('serial_number', 'ilike', "%{$search}%")
              ->orWhere('item_category', 'ilike', "%{$search}%")
              ->orWhere('item_type', 'ilike', "%{$search}%")
              ->orWhereHas('supplier', function ($sq) use ($search) {
                  $sq->where('name', 'ilike', "%{$search}%");
              });
        });
    }

    // Cache form data for create/edit forms
    public function getFormData()
    {
        return Cache::remember('inventory_form_data', 1800, function () { // 30 minutes
            return [
                'suppliers' => \App\Models\Supplier::select('id', 'name')->orderBy('name')->get(),
                'categories' => $this->model->distinct('item_category')
                    ->whereNotNull('item_category')
                    ->pluck('item_category')
                    ->sort()
                    ->values(),
                'types' => $this->model->distinct('item_type')
                    ->whereNotNull('item_type')
                    ->pluck('item_type')
                    ->sort()
                    ->values(),
            ];
        });
    }

    // Get inventory statistics for dashboard (cached)
    public function getStatistics()
    {
        return Cache::remember('inventory_statistics', 600, function () { // 10 minutes
            return [
                'total_items' => $this->model->count(),
                'low_stock_items' => $this->model->whereColumn('quantity_on_hand', '<=', 'reorder_level')->count(),
                'out_of_stock' => $this->model->where('quantity_on_hand', 0)->count(),
                'by_status' => $this->model->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'by_category' => $this->model->select('item_category', DB::raw('count(*) as count'))
                    ->whereNotNull('item_category')
                    ->groupBy('item_category')
                    ->pluck('count', 'item_category')
                    ->toArray(),
                'maintenance_due' => $this->model->where('next_maintenance_due', '<=', now()->addDays(7))
                    ->count(),
            ];
        });
    }

    // Get low stock items (cached)
    public function getLowStockItems($limit = 10)
    {
        return Cache::remember('low_stock_items', 300, function () use ($limit) {
            return $this->model->with('supplier')
                ->whereColumn('quantity_on_hand', '<=', 'reorder_level')
                ->orderBy('quantity_on_hand', 'asc')
                ->limit($limit)
                ->get();
        });
    }

    // Get items requiring maintenance (cached)
    public function getMaintenanceDueItems($days = 7, $limit = 10)
    {
        $cacheKey = "maintenance_due_{$days}days_{$limit}";
        
        return Cache::remember($cacheKey, 600, function () use ($days, $limit) {
            return $this->model->with('supplier')
                ->where('next_maintenance_due', '<=', now()->addDays($days))
                ->orderBy('next_maintenance_due', 'asc')
                ->limit($limit)
                ->get();
        });
    }

    // Bulk update inventory quantities
    public function bulkUpdateQuantities(array $updates)
    {
        DB::beginTransaction();
        try {
            $updatedCount = 0;
            
            foreach ($updates as $update) {
                $item = $this->model->find($update['id']);
                if ($item) {
                    $oldQuantity = $item->quantity_on_hand;
                    $item->update(['quantity_on_hand' => $update['quantity']]);
                    
                    $this->checkAndCreateReorderAlert($item);
                    $this->logInventoryTransaction($item, $oldQuantity, $update['quantity']);
                    
                    $updatedCount++;
                }
            }
            
            $this->clearCaches();
            DB::commit();
            
            return $updatedCount;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function checkAndCreateReorderAlert($item)
    {
        if ($item->quantity_on_hand <= $item->reorder_level) {
            // Create or update reorder alert
            \App\Models\InventoryAlert::updateOrCreate(
                [
                    'inventory_item_id' => $item->id,
                    'alert_type' => 'reorder',
                ],
                [
                    'message' => "Item '{$item->name}' is below reorder level ({$item->reorder_level}). Current quantity: {$item->quantity_on_hand}",
                    'priority' => $item->quantity_on_hand == 0 ? 'critical' : 'high',
                    'is_resolved' => false,
                ]
            );
        }
    }

    private function logInventoryTransaction($item, $oldQuantity, $newQuantity)
    {
        $difference = $newQuantity - $oldQuantity;
        
        \App\Models\InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'transaction_type' => $difference > 0 ? 'in' : 'out',
            'quantity' => abs($difference),
            'notes' => "Quantity updated from {$oldQuantity} to {$newQuantity}",
            'recorded_by' => auth()->id(),
        ]);
    }

    // Export methods
    public function export(Request $request)
    {
        $config = $this->buildExportConfig();
        return $this->handleExport($request, InventoryItem::class, $config);
    }

    public function printAll(Request $request)
    {
        $config = $this->buildExportConfig();
        return $this->handlePrintAll($request, InventoryItem::class, $config);
    }

    public function printCurrent(Request $request)
    {
        $config = $this->buildExportConfig();
        return $this->handlePrintCurrent($request, InventoryItem::class, $config);
    }

    public function printSingle($id, Request $request)
    {
        $item = $this->getById($id);
        $config = $this->buildExportConfig();
        return $this->handlePrintSingle($request, $item, $config);
    }

    private function buildExportConfig(): array
    {
        $raw = AdditionalExportConfigs::getInventoryItemConfig();

        // Map CSV config
        $csvHeaders = $raw['csv_headers'] ?? ['Name', 'Category', 'Type', 'Serial Number', 'Purchase Date', 'Warranty Expiry', 'Status'];
        $csvFieldsMap = $raw['csv_fields'] ?? [];
        $csvFields = [];
        foreach ($csvHeaders as $label) {
            $spec = $csvFieldsMap[$label] ?? null;
            if ($spec === null) continue;
            $csvFields[] = $spec;
        }

        // Columns for PDF views
        $pdfColumns = $raw['pdf_columns'] ?? [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'item_category', 'label' => 'Category'],
            ['key' => 'item_type', 'label' => 'Type'],
            ['key' => 'serial_number', 'label' => 'Serial Number'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'purchase_date', 'label' => 'Purchase Date'],
            ['key' => 'warranty_expiry', 'label' => 'Warranty Expiry'],
        ];

        return [
            'csv' => [
                'headers' => $csvHeaders,
                'fields' => $csvFields,
                'filename_prefix' => 'inventory_items',
            ],
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Items List',
                'filename_prefix' => 'inventory_items',
                'orientation' => 'landscape',
                'columns' => $pdfColumns,
            ],
            'all_records' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Items List',
                'filename_prefix' => 'inventory_items',
                'orientation' => 'landscape',
                'include_index' => true,
                'columns' => $pdfColumns,
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Items (Current View)',
                'filename_prefix' => 'inventory_items_current',
                'orientation' => 'landscape',
                'columns' => $pdfColumns,
            ],
            'single_record' => [
                'view' => 'pdf-layout',
                'document_title' => 'Inventory Item Details',
                'filename_prefix' => 'inventory_item',
            ],
        ];
    }

    protected function clearCaches(): void
    {
        // Clear specific inventory caches
        Cache::forget('inventory_form_data');
        Cache::forget('inventory_statistics');
        Cache::forget('low_stock_items');
        
        // Clear maintenance cache patterns
        $patterns = ['maintenance_due_*', 'inventory_items_all_*', 'inventory_items_single_*'];
        foreach ($patterns as $pattern) {
            Cache::flush(); // Simplified for database cache
            break;
        }
    }
}