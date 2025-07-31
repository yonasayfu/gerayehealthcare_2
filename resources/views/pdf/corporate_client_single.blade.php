<!DOCTYPE html>
<html>
<head>
    <title>Corporate Client Details</title>
    <style>
        body { font-family: sans-serif; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table th, .detail-table td { border: 1px solid black; padding: 8px; text-align: left; }
        .detail-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Corporate Client Details</h1>

    <table class="detail-table">
        <tr>
            <th>Organization Name</th>
            <td>{{ $corporateClient->organization_name }}</td>
        </tr>
        <tr>
            <th>Amharic Organization Name</th>
            <td>{{ $corporateClient->organization_name_amharic }}</td>
        </tr>
        <tr>
            <th>Contact Person</th>
            <td>{{ $corporateClient->contact_person }}</td>
        </tr>
        <tr>
            <th>Contact Email</th>
            <td>{{ $corporateClient->contact_email }}</td>
        </tr>
        <tr>
            <th>Contact Phone</th>
            <td>{{ $corporateClient->contact_phone }}</td>
        </tr>
        <tr>
            <th>TIN Number</th>
            <td>{{ $corporateClient->tin_number }}</td>
        </tr>
        <tr>
            <th>Trade License Number</th>
            <td>{{ $corporateClient->trade_license_number }}</td>
        </tr>
        <tr>
            <th>Address</th>
            <td>{{ $corporateClient->address }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $corporateClient->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $corporateClient->updated_at }}</td>
        </tr>
    </table>
</body>
</html>
