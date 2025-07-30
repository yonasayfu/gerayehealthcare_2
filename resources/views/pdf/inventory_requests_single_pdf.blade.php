<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Request Details - Geraye Home Care Services</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            color: #333;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 20px;
            margin: 0;
        }
        p {
            font-size: 14px;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 8px 10px;
            border: 1px solid #999;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 11px;
        }
    </style>
</head>
<body>
   <header style="text-align: center; margin-bottom: 30px;">
    <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
    <h1 style="margin: 0;">Geraye Home Care Services</h1>
    <p style="margin: 0;">Inventory Request Details</p>
</header>

    <table>
        <thead>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Request ID</td><td>#{{ $inventoryRequest->id }}</td></tr>
            <tr><td>Requester</td><td>{{ $inventoryRequest->requester->first_name }} {{ $inventoryRequest->requester->last_name }}</td></tr>
            <tr><td>Approver</td><td>{{ $inventoryRequest->approver ? $inventoryRequest->approver->first_name . ' ' . $inventoryRequest->approver->last_name : 'N/A' }}</td></tr>
            <tr><td>Item</td><td>{{ $inventoryRequest->item->name }}</td></tr>
            <tr><td>Quantity Requested</td><td>{{ $inventoryRequest->quantity_requested }}</td></tr>
            <tr><td>Quantity Approved</td><td>{{ $inventoryRequest->quantity_approved ?? 'N/A' }}</td></tr>
            <tr><td>Reason</td><td>{{ $inventoryRequest->reason ?? '-' }}</td></tr>
            <tr><td>Status</td><td>{{ $inventoryRequest->status }}</td></tr>
            <tr><td>Priority</td><td>{{ $inventoryRequest->priority }}</td></tr>
            <tr><td>Needed By Date</td><td>{{ $inventoryRequest->needed_by_date ? \Carbon\Carbon::parse($inventoryRequest->needed_by_date)->format('Y-m-d') : 'N/A' }}</td></tr>
            <tr><td>Requested At</td><td>{{ $inventoryRequest->created_at ? \Carbon\Carbon::parse($inventoryRequest->created_at)->format('Y-m-d H:i') : 'N/A' }}</td></tr>
            <tr><td>Approved At</td><td>{{ $inventoryRequest->approved_at ? \Carbon\Carbon::parse($inventoryRequest->approved_at)->format('Y-m-d H:i') : 'N/A' }}</td></tr>
            <tr><td>Fulfilled At</td><td>{{ $inventoryRequest->fulfilled_at ? \Carbon\Carbon::parse($inventoryRequest->fulfilled_at)->format('Y-m-d H:i') : 'N/A' }}</td></tr>
        </tbody>
    </table>

    <div class="footer">
        Exported on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>