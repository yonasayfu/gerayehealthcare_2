<!DOCTYPE html>
<html>
<head>
    <title>Insurance Policy Details</title>
    <style>
        body { font-family: sans-serif; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table th, .detail-table td { border: 1px solid black; padding: 8px; text-align: left; }
        .detail-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Insurance Policy Details</h1>

    <table class="detail-table">
        <tr>
            <th>ID</th>
            <td>{{ $insurancePolicy->id }}</td>
        </tr>
        <tr>
            <th>Insurance Company ID</th>
            <td>{{ $insurancePolicy->insurance_company_id }}</td>
        </tr>
        <tr>
            <th>Corporate Client ID</th>
            <td>{{ $insurancePolicy->corporate_client_id }}</td>
        </tr>
        <tr>
            <th>Service Type</th>
            <td>{{ $insurancePolicy->service_type }}</td>
        </tr>
        <tr>
            <th>Amharic Service Type</th>
            <td>{{ $insurancePolicy->service_type_amharic }}</td>
        </tr>
        <tr>
            <th>Coverage Percentage</th>
            <td>{{ $insurancePolicy->coverage_percentage }}</td>
        </tr>
        <tr>
            <th>Coverage Type</th>
            <td>{{ $insurancePolicy->coverage_type }}</td>
        </tr>
        <tr>
            <th>Is Active</th>
            <td>{{ $insurancePolicy->is_active ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <th>Notes</th>
            <td>{{ $insurancePolicy->notes }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $insurancePolicy->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $insurancePolicy->updated_at }}</td>
        </tr>
    </table>
</body>
</html>
