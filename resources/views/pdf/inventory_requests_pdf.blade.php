<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Request Export - Geraye</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 10px; /* Smaller font for more columns */
            color: #333;
        }
        header {
            text-align: center;
            margin-bottom: 20px; /* Reduced margin */
        }
        h1 {
            font-size: 18px; /* Slightly smaller heading */
            margin: 0;
        }
        p {
            font-size: 12px; /* Slightly smaller paragraph */
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; /* Reduced margin */
        }
        th, td {
            padding: 6px 8px; /* Reduced padding */
            border: 1px solid #999;
            text-align: left;
            vertical-align: top; /* Align content to top */
        }
        th {
            background-color: #f3f3f3;
        }
        .footer {
            text-align: right;
            margin-top: 20px; /* Reduced margin */
            font-size: 10px;
        }
    </style>
</head>
<body>
   <header style="text-align: center; margin-bottom: 20px;">
    <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 50px; margin-bottom: 8px;">
    <h1 style="margin: 0;">Geraye Home-to-Home Care</h1>
    <p style="margin: 0;">Inventory Requests Export</p>
</header>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Requester</th>
                <th>Approver</th>
                <th>Item</th>
                <th>Qty Req.</th>
                <th>Qty App.</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Needed By</th>
                <th>Approved At</th>
                <th>Fulfilled At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventoryRequests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->requester->first_name }} {{ $request->requester->last_name }}</td>
                    <td>{{ $request->approver ? $request->approver->first_name . ' ' . $request->approver->last_name : 'N/A' }}</td>
                    <td>{{ $request->item->name }}</td>
                    <td>{{ $request->quantity_requested }}</td>
                    <td>{{ $request->quantity_approved ?? 'N/A' }}</td>
                    <td>{{ $request->reason ?? '-' }}</td>
                    <td>{{ $request->status }}</td>
                    <td>{{ $request->priority }}</td>
                    <td>{{ $request->needed_by_date ? \Carbon\Carbon::parse($request->needed_by_date)->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $request->approved_at ? \Carbon\Carbon::parse($request->approved_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                    <td>{{ $request->fulfilled_at ? \Carbon\Carbon::parse($request->fulfilled_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Exported on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>