<!DOCTYPE html>
<html>
<head>
    <title>Inventory Requests Report</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; font-size: 10pt; }
        .container { width: 100%; margin: 0 auto; padding: 20mm; }
        h1 { text-align: center; margin-bottom: 15mm; font-size: 18pt; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10mm; }
        th, td { border: 1px solid #ccc; padding: 8pt; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .footer { text-align: center; margin-top: 20mm; font-size: 8pt; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Inventory Requests Report</h1>
        <table>
            <thead>
                <tr>
                    <th>Requester</th>
                    <th>Item</th>
                    <th>Quantity Requested</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Needed By Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventoryRequests as $request)
                <tr>
                    <td>{{ $request->requester->first_name }} {{ $request->requester->last_name }}</td>
                    <td>{{ $request->item.name }}</td>
                    <td>{{ $request->quantity_requested }}</td>
                    <td>{{ $request->status }}</td>
                    <td>{{ $request->priority }}</td>
                    <td>{{ $request->needed_by_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="footer">
            Generated on: {{ date('Y-m-d H:i:s') }}
        </div>
    </div>
</body>
</html>
