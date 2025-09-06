<?php

/**
 * Script to automatically refactor Laravel controllers to use unified request classes
 * instead of separate Store and Update request classes.
 */
echo "Starting validation refactoring process...\n";

// Define the mapping of controllers to their request classes
$controllerMapping = [
    'EventController' => [
        'store_request' => 'StoreEventRequest',
        'update_request' => 'UpdateEventRequest',
        'new_request' => 'EventRequest',
        'route_param' => 'event',
    ],
    'InventoryItemController' => [
        'store_request' => 'StoreInventoryItemRequest',
        'update_request' => 'UpdateInventoryItemRequest',
        'new_request' => 'InventoryItemRequest',
        'route_param' => 'inventoryItem',
    ],
    'SupplierController' => [
        'store_request' => 'StoreSupplierRequest',
        'update_request' => 'UpdateSupplierRequest',
        'new_request' => 'SupplierRequest',
        'route_param' => 'supplier',
    ],
    'StaffController' => [
        'store_request' => 'StoreStaffRequest',
        'update_request' => 'UpdateStaffRequest',
        'new_request' => 'StaffRequest',
        'route_param' => 'staff',
    ],
    // Add more controllers as needed
];

$controllersDir = __DIR__.'/app/Http/Controllers/Admin';
$requestsDir = __DIR__.'/app/Http/Requests';

foreach ($controllerMapping as $controllerName => $mapping) {
    $controllerPath = "{$controllersDir}/{$controllerName}.php";

    if (! file_exists($controllerPath)) {
        echo "Controller {$controllerName} not found. Skipping...\n";

        continue;
    }

    echo "Refactoring {$controllerName}...\n";

    // Read the controller file
    $content = file_get_contents($controllerPath);

    // Replace the use statements
    $content = preg_replace(
        '/use App\\\\Http\\\\Requests\\\\'.$mapping['store_request'].';\s*use App\\\\Http\\\\Requests\\\\'.$mapping['update_request'].';/',
        "use App\\Http\\Requests\\{$mapping['new_request']};",
        $content
    );

    // Replace store method parameter
    $content = preg_replace(
        '/public function store\(\\App\\\\Http\\\\Requests\\\\'.$mapping['store_request'].'\s+\$request\)/',
        "public function store({$mapping['new_request']} \$request)",
        $content
    );

    // Replace update method parameter
    $content = preg_replace(
        '/public function update\(\\App\\\\Http\\\\Requests\\\\'.$mapping['update_request'].'\s+\$request/',
        "public function update({$mapping['new_request']} \$request",
        $content
    );

    // Write the updated content back to the file
    file_put_contents($controllerPath, $content);

    echo "Successfully refactored {$controllerName}\n";
}

echo "Validation refactoring process completed!\n";
