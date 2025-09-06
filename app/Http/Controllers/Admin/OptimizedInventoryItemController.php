<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\CreateInventoryItemDTO;
use App\Http\Controllers\Base\OptimizedBaseController;
use App\Models\InventoryItem;
use App\Services\OptimizedInventoryItemService;
use App\Services\Validation\Rules\InventoryItemRules;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OptimizedInventoryItemController extends OptimizedBaseController
{
    protected $inventoryService;

    // Define eager loading relationships to prevent N+1 queries
    protected $indexWith = ['supplier'];

    protected $showWith = ['supplier', 'alerts', 'maintenanceRecords', 'transactions'];

    protected $editWith = ['supplier'];

    public function __construct(OptimizedInventoryItemService $inventoryService)
    {
        $this->inventoryService = $inventoryService;

        parent::__construct(
            $inventoryService,
            InventoryItemRules::class,
            'Admin/InventoryItems',
            'inventory-items',
            InventoryItem::class,
            CreateInventoryItemDTO::class
        );
    }

    public function index(Request $request)
    {
        // Use optimized service with caching and eager loading
        $items = $this->inventoryService->getAll($request, $this->indexWith);

        // Get cached statistics and alerts for dashboard
        $statistics = $this->inventoryService->getStatistics();
        $lowStockItems = $this->inventoryService->getLowStockItems(5);
        $maintenanceDue = $this->inventoryService->getMaintenanceDueItems(7, 5);

        return Inertia::render($this->viewName.'/Index', [
            'inventoryItems' => $items,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page', 'status', 'category']),
            'statistics' => $statistics,
            'lowStockItems' => $lowStockItems,
            'maintenanceDue' => $maintenanceDue,
        ]);
    }

    public function show($id)
    {
        // Use optimized service with eager loading
        $item = $this->inventoryService->getById($id, $this->showWith);

        return Inertia::render($this->viewName.'/Show', [
            'inventoryItem' => $item,
        ]);
    }

    public function create()
    {
        // Use cached form data
        $formData = $this->inventoryService->getFormData();

        return Inertia::render($this->viewName.'/Create', $formData);
    }

    public function edit($id)
    {
        // Use optimized service with eager loading
        $item = $this->inventoryService->getById($id, $this->editWith);
        $formData = $this->inventoryService->getFormData();

        return Inertia::render($this->viewName.'/Edit', array_merge([
            'inventoryItem' => $item,
        ], $formData));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate(InventoryItemRules::create());

        // Use DTO for data transfer
        $dto = CreateInventoryItemDTO::from($validatedData);
        $item = $this->inventoryService->create($dto);

        return redirect()->route('admin.inventory-items.index')
            ->with('banner', 'Inventory item created successfully!')
            ->with('bannerStyle', 'success');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate(InventoryItemRules::update());

        // Use DTO for data transfer
        $dto = CreateInventoryItemDTO::from($validatedData);
        $item = $this->inventoryService->update($id, $dto);

        return redirect()->route('admin.inventory-items.index')
            ->with('banner', 'Inventory item updated successfully!')
            ->with('bannerStyle', 'success');
    }

    public function destroy($id)
    {
        $this->inventoryService->delete($id);

        return redirect()->route('admin.inventory-items.index')
            ->with('banner', 'Inventory item deleted successfully!')
            ->with('bannerStyle', 'success');
    }

    // Optimized export methods
    public function export(Request $request)
    {
        return $this->inventoryService->export($request);
    }

    public function printAll(Request $request)
    {
        return $this->inventoryService->printAll($request);
    }

    public function printCurrent(Request $request)
    {
        return $this->inventoryService->printCurrent($request);
    }

    public function printSingle($id, Request $request)
    {
        return $this->inventoryService->printSingle($id, $request);
    }

    // API endpoint for low stock alerts (highly optimized)
    public function lowStockAlerts(Request $request)
    {
        $limit = $request->input('limit', 10);
        $lowStockItems = $this->inventoryService->getLowStockItems($limit);

        return response()->json($lowStockItems);
    }

    // API endpoint for maintenance due items
    public function maintenanceDue(Request $request)
    {
        $days = $request->input('days', 7);
        $limit = $request->input('limit', 10);

        $maintenanceItems = $this->inventoryService->getMaintenanceDueItems($days, $limit);

        return response()->json($maintenanceItems);
    }

    // Bulk quantity update
    public function bulkUpdateQuantities(Request $request)
    {
        $validated = $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:inventory_items,id',
            'updates.*.quantity' => 'required|integer|min:0',
        ]);

        $updatedCount = $this->inventoryService->bulkUpdateQuantities($validated['updates']);

        return response()->json([
            'message' => "Updated quantities for {$updatedCount} items successfully",
            'updated_count' => $updatedCount,
        ]);
    }

    // Quick search for inventory items
    public function quickSearch(Request $request)
    {
        $search = $request->input('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        // Cache quick searches for better performance
        $cacheKey = 'inventory_quick_search_'.md5($search);

        $results = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($search) {
            return InventoryItem::with('supplier:id,name')
                ->where('name', 'ilike', "%{$search}%")
                ->orWhere('serial_number', 'ilike', "%{$search}%")
                ->select('id', 'name', 'serial_number', 'quantity_on_hand', 'supplier_id', 'status')
                ->limit(10)
                ->get();
        });

        return response()->json($results);
    }

    // Stock adjustment endpoint
    public function adjustStock(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:set,add,subtract',
            'reason' => 'nullable|string|max:255',
        ]);

        $item = $this->inventoryService->getById($id);
        $oldQuantity = $item->quantity_on_hand;

        switch ($validated['type']) {
            case 'set':
                $newQuantity = $validated['quantity'];
                break;
            case 'add':
                $newQuantity = $oldQuantity + $validated['quantity'];
                break;
            case 'subtract':
                $newQuantity = max(0, $oldQuantity - $validated['quantity']);
                break;
        }

        $this->inventoryService->update($id, [
            'quantity_on_hand' => $newQuantity,
            'notes' => ($item->notes ?? '')."\n".now()->format('Y-m-d H:i').': '.
                      ($validated['reason'] ?? "Stock adjusted from {$oldQuantity} to {$newQuantity}"),
        ]);

        return response()->json([
            'message' => 'Stock quantity adjusted successfully',
            'old_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
        ]);
    }

    // Dashboard data endpoint
    public function dashboardData()
    {
        $statistics = $this->inventoryService->getStatistics();
        $lowStockItems = $this->inventoryService->getLowStockItems(5);
        $maintenanceDue = $this->inventoryService->getMaintenanceDueItems(7, 5);

        return response()->json([
            'statistics' => $statistics,
            'low_stock_items' => $lowStockItems,
            'maintenance_due' => $maintenanceDue,
        ]);
    }
}
