<!DOCTYPE html>
<html>
<head>
    <title>Inventory Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo.png') }}" alt="Logo">
        <h1>Inventory Items Report</h1>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Requester</th>
                <th>Approver</th>
                <th>Item</th>
                <th>Quantity Requested</th>
                <th>Quantity Approved</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Needed By Date</th>
                <th>Approved At</th>
                <th>Fulfilled At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventoryRequests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>{{ $request->requester->first_name }} {{ $request->requester->last_name }}</td>
                <td>{{ $request->approver ? $request->approver->first_name . ' ' . $request->approver->last_name : 'N/A' }}</td>
                <td>{{ $request->item->name }}</td>
                <td>{{ $request->quantity_requested }}</td>
                <td>{{ $request->quantity_approved }}</td>
                <td>{{ $request->reason }}</td>
                <td>{{ $request->status }}</td>
                <td>{{ $request->priority }}</td>
                <td>{{ $request->needed_by_date }}</td>
                <td>{{ $request->approved_at }}</td>
                <td>{{ $request->fulfilled_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>