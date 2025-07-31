<!DOCTYPE html>
<html>
<head>
    <title>Employee Insurance Records</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Employee Insurance Records</h1>
    <table>
        <thead>
            <tr>
                <th>Kebele ID</th>
                <th>Woreda</th>
                <th>Region</th>
                <th>Federal ID</th>
                <th>Ministry Department</th>
                <th>Employee ID Number</th>
                <th>Verified</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employeeInsuranceRecords as $record)
            <tr>
                <td>{{ $record->kebele_id }}</td>
                <td>{{ $record->woreda }}</td>
                <td>{{ $record->region }}</td>
                <td>{{ $record->federal_id }}</td>
                <td>{{ $record->ministry_department }}</td>
                <td>{{ $record->employee_id_number }}</td>
                <td>{{ $record->verified ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
