<x-printable-report
    title="Inventory Alerts Report"
    :data="$inventoryAlerts->map(function($alert) {
        return [
            'item_name' => $alert->item->name,
            'alert_type' => $alert->alert_type,
            'message' => $alert->message,
            'status' => $alert->is_active ? 'Active' : 'Inactive',
            'triggered_at' => $alert->triggered_at,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'item_name', 'label' => 'Item'],
        ['key' => 'alert_type', 'label' => 'Alert Type'],
        ['key' => 'message', 'label' => 'Message'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'triggered_at', 'label' => 'Triggered At'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Inventory Alerts Report',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>