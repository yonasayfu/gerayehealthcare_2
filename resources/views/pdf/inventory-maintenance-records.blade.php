<x-printable-report
    title="Inventory Maintenance Records Report"
    :data="$maintenanceRecords->map(function($record, $index) {
        return [
            'index' => $index + 1,
            'item_name' => $record->item->name ?? 'N/A',
            'scheduled_date' => $record->scheduled_date,
            'actual_date' => $record->actual_date,
            'performed_by' => $record->performed_by,
            'cost' => $record->cost,
            'status' => $record->status,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
        ['key' => 'item_name', 'label' => 'Item'],
        ['key' => 'scheduled_date', 'label' => 'Scheduled Date'],
        ['key' => 'actual_date', 'label' => 'Actual Date'],
        ['key' => 'performed_by', 'label' => 'Performed By'],
        ['key' => 'cost', 'label' => 'Cost'],
        ['key' => 'status', 'label' => 'Status'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Inventory Maintenance Records Report',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>