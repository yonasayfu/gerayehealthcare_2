<x-printable-report
    title="Inventory Requests Report"
    :data="$inventoryRequests->map(function($request, $index) {
        return [
            'index' => $index + 1,
            'requester' => ($request->requester->first_name ?? '') . ' ' . ($request->requester->last_name ?? ''),
            'item_name' => $request->item->name ?? 'N/A',
            'quantity_requested' => $request->quantity_requested ?? 0,
            'status' => $request->status ?? 'N/A',
            'priority' => $request->priority ?? 'N/A',
            'needed_by_date' => $request->needed_by_date ? \Carbon\Carbon::parse($request->needed_by_date)->format('Y-m-d') : 'N/A',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
        ['key' => 'requester', 'label' => 'Requester'],
        ['key' => 'item_name', 'label' => 'Item'],
        ['key' => 'quantity_requested', 'label' => 'Quantity Requested'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'priority', 'label' => 'Priority'],
        ['key' => 'needed_by_date', 'label' => 'Needed By Date'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Inventory Requests Report',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>
