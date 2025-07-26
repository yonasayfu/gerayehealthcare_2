<!DOCTYPE html>
<html>
<head>
    <title>Inventory Items Report</title>
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
        <h1>Inventory Items Report</h1>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Serial Number</th>
                    <th>Status</th>
                    <th>Purchase Date</th>
                    <th>Warranty Expiry</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventoryItems as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->item_category }}</td>
                    <td>{{ $item->item_type }}</td>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->purchase_date }}</td>
                    <td>{{ $item->warranty_expiry }}</td>
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
