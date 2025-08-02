<x-printable-report
    title="Inventory Item Details"
    :data="[
        ['label' => 'Name', 'value' => $inventoryItem->name],
        ['label' => 'Category', 'value' => $inventoryItem->item_category ?? '-'],
        ['label' => 'Type', 'value' => $inventoryItem->item_type ?? '-'],
        ['label' => 'Serial Number', 'value' => $inventoryItem->serial_number ?? '-'],
        ['label' => 'Status', 'value' => $inventoryItem->status ?? '-'],
        ['label' => 'Description', 'value' => $inventoryItem->description ?? '-'],
        ['label' => 'Purchase Date', 'value' => $inventoryItem->purchase_date ? \Carbon\Carbon::parse($inventoryItem->purchase_date)->format('Y-m-d') : '-'],
        ['label' => 'Warranty Expiry', 'value' => $inventoryItem->warranty_expiry ? \Carbon\Carbon::parse($inventoryItem->warranty_expiry)->format('Y-m-d') : '-'],
        ['label' => 'Supplier', 'value' => $inventoryItem->supplier->name ?? '-'],
        ['label' => 'Assigned To Type', 'value' => $inventoryItem->assigned_to_type ?? '-'],
        ['label' => 'Assigned To ID', 'value' => $inventoryItem->assigned_to_id ?? '-'],
        ['label' => 'Last Maintenance Date', 'value' => $inventoryItem->last_maintenance_date ? \Carbon\Carbon::parse($inventoryItem->last_maintenance_date)->format('Y-m-d') : '-'],
        ['label' => 'Next Maintenance Due', 'value' => $inventoryItem->next_maintenance_due ? \Carbon\Carbon::parse($inventoryItem->next_maintenance_due)->format('Y-m-d') : '-'],
        ['label' => 'Maintenance Schedule', 'value' => $inventoryItem->maintenance_schedule ?? '-'],
        ['label' => 'Notes', 'value' => $inventoryItem->notes ?? '-'],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Inventory Item Details Report',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>