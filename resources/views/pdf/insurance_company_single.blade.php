<!DOCTYPE html>
<html>
<head>
    <title>Insurance Company Details</title>
    <style>
        body { font-family: sans-serif; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table th, .detail-table td { border: 1px solid black; padding: 8px; text-align: left; }
        .detail-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Insurance Company Details</h1>

    <table class="detail-table">
        <tr>
            <th>Name</th>
            <td>{{ $insuranceCompany->name }}</td>
        </tr>
        <tr>
            <th>Amharic Name</th>
            <td>{{ $insuranceCompany->name_amharic }}</td>
        </tr>
        <tr>
            <th>Contact Person</th>
            <td>{{ $insuranceCompany->contact_person }}</td>
        </tr>
        <tr>
            <th>Contact Email</th>
            <td>{{ $insuranceCompany->contact_email }}</td>
        </tr>
        <tr>
            <th>Contact Phone</th>
            <td>{{ $insuranceCompany->contact_phone }}</td>
        </tr>
        <tr>
            <th>Address</th>
            <td>{{ $insuranceCompany->address }}</td>
        </tr>
        <tr>
            <th>Amharic Address</th>
            <td>{{ $insuranceCompany->address_amharic }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $insuranceCompany->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $insuranceCompany->updated_at }}</td>
        </tr>
    </table>
</body>
</html>
