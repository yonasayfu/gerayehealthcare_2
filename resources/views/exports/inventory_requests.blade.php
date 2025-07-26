@php echo "ID,Requester,Approver,Item,Quantity Requested,Quantity Approved,Reason,Status,Priority,Needed By Date,Approved At,Fulfilled At
"; @endphp
@foreach($inventoryRequests as $request)
@php
    $requesterName = $request->requester->first_name . ' ' . $request->requester->last_name;
    $approverName = $request->approver ? $request->approver->first_name . ' ' . $request->approver->last_name : 'N/A';
    $itemName = $request->item->name;
    $neededByDate = $request->needed_by_date ? \Carbon\Carbon::parse($request->needed_by_date)->format('Y-m-d') : 'N/A';
    $approvedAt = $request->approved_at ? \Carbon\Carbon::parse($request->approved_at)->format('Y-m-d H:i') : 'N/A';
    $fulfilledAt = $request->fulfilled_at ? \Carbon\Carbon::parse($request->fulfilled_at)->format('Y-m-d H:i') : 'N/A';

    // Escape commas and double quotes within fields
    $csvLine = [
        $request->id,
        str_replace('"', '""', $requesterName),
        str_replace('"', '""', $approverName),
        str_replace('"', '""', $itemName),
        $request->quantity_requested,
        $request->quantity_approved,
        str_replace('"', '""', $request->reason),
        str_replace('"', '""', $request->status),
        str_replace('"', '""', $request->priority),
        $neededByDate,
        $approvedAt,
        $fulfilledAt,
    ];
    echo implode(',', array_map(function($value) { return '"' . $value . '"'; }, $csvLine)) . "
";
@endphp
@endforeach