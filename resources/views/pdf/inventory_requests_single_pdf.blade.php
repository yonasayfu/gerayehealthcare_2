<x-printable-report
    title="Inventory Request Details - Geraye Home Care Services"
    :data="[
        ['label' => 'Request ID', 'value' => '#' . $inventoryRequest->id],
        ['label' => 'Requester', 'value' => $inventoryRequest->requester->first_name . ' ' . $inventoryRequest->requester->last_name],
        ['label' => 'Approver', 'value' => $inventoryRequest->approver ? $inventoryRequest->approver->first_name . ' ' . $inventoryRequest->approver->last_name : 'N/A'],
        ['label' => 'Item', 'value' => $inventoryRequest->item->name],
        ['label' => 'Quantity Requested', 'value' => $inventoryRequest->quantity_requested],
        ['label' => 'Quantity Approved', 'value' => $inventoryRequest->quantity_approved ?? 'N/A'],
        ['label' => 'Reason', 'value' => $inventoryRequest->reason ?? '-'],
        ['label' => 'Status', 'value' => $inventoryRequest->status],
        ['label' => 'Priority', 'value' => $inventoryRequest->priority],
        ['label' => 'Needed By Date', 'value' => $inventoryRequest->needed_by_date ? \Carbon\Carbon::parse($inventoryRequest->needed_by_date)->format('Y-m-d') : 'N/A'],
        ['label' => 'Requested At', 'value' => $inventoryRequest->created_at ? \Carbon\Carbon::parse($inventoryRequest->created_at)->format('Y-m-d H:i') : 'N/A'],
        ['label' => 'Approved At', 'value' => $inventoryRequest->approved_at ? \Carbon\Carbon::parse($inventoryRequest->approved_at)->format('Y-m-d H:i') : 'N/A'],
        ['label' => 'Fulfilled At', 'value' => $inventoryRequest->fulfilled_at ? \Carbon\Carbon::parse($inventoryRequest->fulfilled_at)->format('Y-m-d H:i') : 'N/A'],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Inventory Request Details',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>
