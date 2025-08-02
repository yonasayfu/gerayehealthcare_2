<x-printable-report
    title="Inventory Requests Report"
    :data="$inventoryRequests->map(function($request) {
        return [
            'id' => $request->id,
            'requester' => $request->requester->first_name . ' ' . $request->requester->last_name,
            'approver' => $request->approver ? $request->approver->first_name . ' ' . $request->approver->last_name : 'N/A',
            'item' => $request->item->name,
            'quantity_requested' => $request->quantity_requested,
            'quantity_approved' => $request->quantity_approved ?? 'N/A',
            'reason' => $request->reason,
            'status' => $request->status,
            'priority' => $request->priority,
            'needed_by_date' => $request->needed_by_date,
            'approved_at' => $request->approved_at,
            'fulfilled_at' => $request->fulfilled_at,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'id', 'label' => 'ID'],
        ['key' => 'requester', 'label' => 'Requester'],
        ['key' => 'approver', 'label' => 'Approver'],
        ['key' => 'item', 'label' => 'Item'],
        ['key' => 'quantity_requested', 'label' => 'Quantity Requested'],
        ['key' => 'quantity_approved', 'label' => 'Quantity Approved'],
        ['key' => 'reason', 'label' => 'Reason'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'priority', 'label' => 'Priority'],
        ['key' => 'needed_by_date', 'label' => 'Needed By Date'],
        ['key' => 'approved_at', 'label' => 'Approved At'],
        ['key' => 'fulfilled_at', 'label' => 'Fulfilled At'],
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
