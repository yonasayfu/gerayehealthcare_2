<!DOCTYPE html>
<html>
<head>
    <title>Insurance Policies</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Insurance Policies</h1>
    <table>
        <thead>
            <tr>
                <th>Service Type</th>
                <th>Coverage Percentage</th>
                <th>Coverage Type</th>
                <th>Is Active</th>
            </tr>
        </thead>
        <tbody>
            @foreach($insurancePolicies as $policy)
            <tr>
                <td>{{ $policy->service_type }}</td>
                <td>{{ $policy->coverage_percentage }}</td>
                <td>{{ $policy->coverage_type }}</td>
                <td>{{ $policy->is_active ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
