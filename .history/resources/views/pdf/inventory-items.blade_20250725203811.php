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
    <header style="text-align: center; margin-bottom: 30px;">
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
        <h1 style="margin: 0;">Geraye Home-to-Home Care</h1>
        <p style="margin: 0;">Inventory Items Report</p>
    </header>

    <table>
        <thead>
            <tr>
                <th>#</th>
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
            @foreach($inventoryItems as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
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
        Generated on: {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
