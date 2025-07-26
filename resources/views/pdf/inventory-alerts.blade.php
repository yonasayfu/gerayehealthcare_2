<!DOCTYPE html>
<html>
<head>
    <title>Inventory Alerts Report</title>
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
        <h1>Inventory Alerts Report</h1>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Alert Type</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Triggered At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventoryAlerts as $alert)
                <tr>
                    <td>{{ $alert->item.name }}</td>
                    <td>{{ $alert->alert_type }}</td>
                    <td>{{ $alert->message }}</td>
                    <td>{{ $alert->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $alert->triggered_at }}</td>
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
