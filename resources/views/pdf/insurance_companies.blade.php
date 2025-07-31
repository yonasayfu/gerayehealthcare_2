<!DOCTYPE html>
<html>
<head>
    <title>Insurance Companies</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Insurance Companies</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Contact Email</th>
                <th>Contact Phone</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($insuranceCompanies as $company)
            <tr>
                <td>{{ $company->name }}</td>
                <td>{{ $company->contact_person }}</td>
                <td>{{ $company->contact_email }}</td>
                <td>{{ $company->contact_phone }}</td>
                <td>{{ $company->address }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
