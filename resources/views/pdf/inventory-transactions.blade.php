<!DOCTYPE html>
<html>
<head>
    <title>Inventory Maintenance Report</title>
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
        <h1>Inventory Maintenance Report</h1>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Performed By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventoryTransactions as $transaction)
                <tr>
                    <td>{{ $transaction->item.name }}</td>
                    <td>{{ $transaction->transaction_type }}</td>
                    <td>{{ $transaction->quantity }}</td>
                    <td>{{ $transaction->from_location }}</td>
                    <td>{{ $transaction->to_location }}</td>
                    <td>{{ $transaction->performedBy.first_name }} {{ $transaction->performedBy.last_name }}</td>
                    <td>{{ $transaction->created_at }}</td>
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
