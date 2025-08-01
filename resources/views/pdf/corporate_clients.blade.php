<!DOCTYPE html>
<html>
<head>
    <title>Corporate Clients List - Geraye Home Care Services</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; color: #333; }
        header { text-align: center; margin-bottom: 30px; }
        h1 { font-size: 20px; margin: 0; }
        p { font-size: 14px; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px 10px; border: 1px solid #999; text-align: left; }
        th { background-color: #f3f3f3; }
        .footer { text-align: right; margin-top: 30px; font-size: 11px; }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
        <h1>Geraye Home Care Services</h1>
        <p>Corporate Clients List</p>
    </header>

    <table>
        <thead>
            <tr>
                <th>Organization Name</th>
                <th>Contact Person</th>
                <th>Contact Email</th>
                <th>Contact Phone</th>
                <th>TIN Number</th>
                <th>Trade License Number</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($corporateClients as $client)
            <tr>
                <td>{{ $client->organization_name }}</td>
                <td>{{ $client->contact_person }}</td>
                <td>{{ $client->contact_email }}</td>
                <td>{{ $client->contact_phone }}</td>
                <td>{{ $client->tin_number }}</td>
                <td>{{ $client->trade_license_number }}</td>
                <td>{{ $client->address }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
