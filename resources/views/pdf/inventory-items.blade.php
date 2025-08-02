<x-printable-report
    title="Inventory Items Report"
    :data="$inventoryItems->map(function($item, $index) {
        return [
            'index' => $index + 1,
            'name' => $item->name,
            'item_category' => $item->item_category,
            'item_type' => $item->item_type,
            'serial_number' => $item->serial_number,
            'status' => $item->status,
            'purchase_date' => $item->purchase_date,
            'warranty_expiry' => $item->warranty_expiry,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'item_category', 'label' => 'Category'],
        ['key' => 'item_type', 'label' => 'Type'],
        ['key' => 'serial_number', 'label' => 'Serial Number'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'purchase_date', 'label' => 'Purchase Date'],
        ['key' => 'warranty_expiry', 'label' => 'Warranty Expiry'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Inventory Items Report',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>