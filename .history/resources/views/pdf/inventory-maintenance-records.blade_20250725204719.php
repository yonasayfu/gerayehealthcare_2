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
    <header style="text-align: center; margin-bottom: 30px;">
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
        <h1 style="margin: 0;">Geraye Home-to-Home Care</h1>
        <p style="margin: 0;">Inventory Maintenance Records Report</p>
    </header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Scheduled Date</th>
                <th>Actual Date</th>
                <th>Performed By</th>
                <th>Cost</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenanceRecords as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->item->name ?? 'N/A' }}</td>
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
