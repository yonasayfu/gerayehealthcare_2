<!DOCTYPE html>
<html>
<head>
    <title>Employee Insurance Record Details</title>
    <style>
        body { font-family: sans-serif; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table th, .detail-table td { border: 1px solid black; padding: 8px; text-align: left; }
        .detail-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Employee Insurance Record Details</h1>

    <table class="detail-table">
        <tr>
            <th>ID</th>
            <td>{{ $employeeInsuranceRecord->id }}</td>
        </tr>
        <tr>
            <th>Patient ID</th>
            <td>{{ $employeeInsuranceRecord->patient_id }}</td>
        </tr>
        <tr>
            <th>Policy ID</th>
            <td>{{ $employeeInsuranceRecord->policy_id }}</td>
        </tr>
        <tr>
            <th>Kebele ID</th>
            <td>{{ $employeeInsuranceRecord->kebele_id }}</td>
        </tr>
        <tr>
            <th>Woreda</th>
            <td>{{ $employeeInsuranceRecord->woreda }}</td>
        </tr>
        <tr>
            <th>Region</th>
            <td>{{ $employeeInsuranceRecord->region }}</td>
        </tr>
        <tr>
            <th>Federal ID</th>
            <td>{{ $employeeInsuranceRecord->federal_id }}</td>
        </tr>
        <tr>
            <th>Ministry Department</th>
            <td>{{ $employeeInsuranceRecord->ministry_department }}</td>
        </tr>
        <tr>
            <th>Employee ID Number</th>
            <td>{{ $employeeInsuranceRecord->employee_id_number }}</td>
        </tr>
        <tr>
            <th>Verified</th>
            <td>{{ $employeeInsuranceRecord->verified ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <th>Verified At</th>
            <td>{{ $employeeInsuranceRecord->verified_at }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $employeeInsuranceRecord->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $employeeInsuranceRecord->updated_at }}</td>
        </tr>
    </table>
</body>
</html>
