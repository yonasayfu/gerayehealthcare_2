<!DOCTYPE html>
<html>
<head>
    <title>Inventory Maintenance Records Report</title>
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
        <h1>Inventory Maintenance Records Report</h1>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Scheduled Date</th>
                    <th>Actual Date</th>
                    <th>Performed By</th>
                    <th>Cost</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maintenanceRecords as $record)
                <tr>
                    <td>{{ $record->item.name }}</td>
                    <td>{{ $record->scheduled_date }}</td>
                    <td>{{ $record->actual_date }}</td>
                    <td>{{ $record->performed_by }}</td>
                    <td>{{ $record->cost }}</td>
                    <td>{{ $record->status }}</td>
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
