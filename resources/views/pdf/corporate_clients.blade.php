<!DOCTYPE html>
<html>
<head>
    <title>Corporate Clients</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Corporate Clients</h1>
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
</body>
</html>
