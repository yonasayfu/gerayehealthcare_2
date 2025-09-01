<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\InventoryRequestResource;
use App\Http\Resources\InventoryTransactionResource;
use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use App\Models\InventoryTransaction;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends BaseApiController
{
    /**
     * Get all inventory items
     */
    public function items(Request $request): JsonResponse
    {
        try {
            $query = InventoryItem::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                      ->orWhere('description', 'ILIKE', "%{$search}%")
                      ->orWhere('sku', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->get('category'));
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by low stock
            if ($request->boolean('low_stock')) {
                $query->whereRaw('current_stock <= reorder_level');
            }

            // Filter by supplier
            if ($request->has('supplier_id')) {
                $query->where('supplier_id', $request->get('supplier_id'));
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $items = $query->with(['supplier', 'transactions' => function ($q) {
                $q->latest()->limit(5);
            }])->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'items' => InventoryItemResource::collection($items->items()),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'has_more' => $items->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch inventory items', 500);
        }
    }

    /**
     * Create a new inventory item
     */
    public function createItem(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'sku' => 'required|string|max:100|unique:inventory_items',
                'category' => 'required|string|max:100',
                'unit_of_measure' => 'required|string|max:50',
                'unit_cost' => 'required|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'current_stock' => 'required|integer|min:0',
                'reorder_level' => 'required|integer|min:0',
                'max_stock_level' => 'nullable|integer|min:0',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'location' => 'nullable|string|max:255',
                'expiry_date' => 'nullable|date',
                'status' => 'required|in:active,inactive,discontinued',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $item = InventoryItem::create($validator->validated());

            // Create initial stock transaction
            if ($item->current_stock > 0) {
                InventoryTransaction::create([
                    'inventory_item_id' => $item->id,
                    'transaction_type' => 'stock_in',
                    'quantity' => $item->current_stock,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->current_stock * $item->unit_cost,
                    'reference_number' => 'INITIAL-' . $item->id,
                    'notes' => 'Initial stock entry',
                    'created_by' => Auth::id(),
                ]);
            }

            return $this->successResponse([
                'item' => new InventoryItemResource($item->load('supplier')),
                'message' => 'Inventory item created successfully'
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create inventory item', 500);
        }
    }

    /**
     * Update an inventory item
     */
    public function updateItem(Request $request, InventoryItem $item): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'sku' => 'sometimes|required|string|max:100|unique:inventory_items,sku,' . $item->id,
                'category' => 'sometimes|required|string|max:100',
                'unit_of_measure' => 'sometimes|required|string|max:50',
                'unit_cost' => 'sometimes|required|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'reorder_level' => 'sometimes|required|integer|min:0',
                'max_stock_level' => 'nullable|integer|min:0',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'location' => 'nullable|string|max:255',
                'expiry_date' => 'nullable|date',
                'status' => 'sometimes|required|in:active,inactive,discontinued',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $item->update($validator->validated());

            return $this->successResponse([
                'item' => new InventoryItemResource($item->load('supplier')),
                'message' => 'Inventory item updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update inventory item', 500);
        }
    }

    /**
     * Delete an inventory item
     */
    public function deleteItem(InventoryItem $item): JsonResponse
    {
        try {
            if ($item->current_stock > 0) {
                return $this->errorResponse('Cannot delete item with stock. Please reduce stock to zero first.', 400);
            }

            $item->delete();
            return $this->successResponse(['message' => 'Inventory item deleted successfully']);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete inventory item', 500);
        }
    }

    /**
     * Adjust stock levels
     */
    public function adjustStock(Request $request, InventoryItem $item): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'adjustment_type' => 'required|in:increase,decrease',
                'quantity' => 'required|integer|min:1',
                'reason' => 'required|string|max:255',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            $data = $validator->validated();

            DB::beginTransaction();

            $oldStock = $item->current_stock;
            $quantity = $data['quantity'];

            if ($data['adjustment_type'] === 'increase') {
                $newStock = $oldStock + $quantity;
                $transactionType = 'adjustment_in';
            } else {
                if ($oldStock < $quantity) {
                    return $this->errorResponse('Insufficient stock for adjustment', 400);
                }
                $newStock = $oldStock - $quantity;
                $transactionType = 'adjustment_out';
            }

            // Update item stock
            $item->update(['current_stock' => $newStock]);

            // Create transaction record
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'transaction_type' => $transactionType,
                'quantity' => $quantity,
                'unit_cost' => $item->unit_cost,
                'total_cost' => $quantity * $item->unit_cost,
                'reference_number' => $data['reference_number'] ?? 'ADJ-' . time(),
                'notes' => $data['reason'] . ($data['notes'] ? ' - ' . $data['notes'] : ''),
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return $this->successResponse([
                'item' => new InventoryItemResource($item->fresh()),
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'message' => 'Stock adjusted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to adjust stock', 500);
        }
    }

    /**
     * Get inventory requests
     */
    public function requests(Request $request): JsonResponse
    {
        try {
            $query = InventoryRequest::query();

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            // Filter by requester
            if ($request->has('requested_by')) {
                $query->where('requested_by', $request->get('requested_by'));
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->where('created_at', '>=', $request->get('start_date'));
            }
            if ($request->has('end_date')) {
                $query->where('created_at', '<=', $request->get('end_date'));
            }

            $requests = $query->with(['requester', 'approver', 'items.inventoryItem'])
                             ->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 15));

            return $this->successResponse([
                'requests' => InventoryRequestResource::collection($requests->items()),
                'pagination' => [
                    'current_page' => $requests->currentPage(),
                    'last_page' => $requests->lastPage(),
                    'per_page' => $requests->perPage(),
                    'total' => $requests->total(),
                    'has_more' => $requests->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch inventory requests', 500);
        }
    }

    /**
     * Create inventory request
     */
    public function createRequest(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'purpose' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
                'items.*.quantity_requested' => 'required|integer|min:1',
                'items.*.notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }

            DB::beginTransaction();

            $inventoryRequest = InventoryRequest::create([
                'purpose' => $request->purpose,
                'notes' => $request->notes,
                'requested_by' => Auth::id(),
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                $inventoryRequest->items()->create([
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return $this->successResponse([
                'request' => new InventoryRequestResource($inventoryRequest->load(['requester', 'items.inventoryItem'])),
                'message' => 'Inventory request created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create inventory request', 500);
        }
    }

    /**
     * Get inventory analytics
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $analytics = [
                'summary' => [
                    'total_items' => InventoryItem::count(),
                    'active_items' => InventoryItem::where('status', 'active')->count(),
                    'low_stock_items' => InventoryItem::whereRaw('current_stock <= reorder_level')->count(),
                    'out_of_stock_items' => InventoryItem::where('current_stock', 0)->count(),
                    'total_value' => InventoryItem::selectRaw('SUM(current_stock * unit_cost)')->value('sum') ?? 0,
                ],
                'categories' => InventoryItem::selectRaw('category, COUNT(*) as count, SUM(current_stock * unit_cost) as value')
                    ->groupBy('category')
                    ->get(),
                'low_stock_alerts' => InventoryItem::whereRaw('current_stock <= reorder_level')
                    ->where('status', 'active')
                    ->with('supplier')
                    ->get(),
                'recent_transactions' => InventoryTransaction::with(['inventoryItem', 'createdBy'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch inventory analytics', 500);
        }
    }
}
