<?php

namespace App\Listeners;

use App\Events\InventoryRequestSaved;

class CheckForLowStock
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InventoryRequestSaved $event): void
    {
        $inventoryItem = InventoryItem::find($event->inventoryRequest->item_id);

        if ($inventoryItem && $event->inventoryRequest->quantity_requested > $inventoryItem->quantity) {
            InventoryAlert::create([
                'item_id' => $inventoryItem->id,
                'alert_type' => 'Low Stock',
                'message' => "Inventory request {$event->inventoryRequest->id} requests {$event->inventoryRequest->quantity_requested} of {$inventoryItem->name}, but only {$inventoryItem->quantity} are available.",
                'is_active' => true,
            ]);
        }
    }
}
