<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Export</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f3f3f3; }
        h2 { margin-bottom: 0; }
    </style>
</head>
<body>
    <h2>Patients Export - {{ now()->toDateTimeString() }}</h2>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Emergency Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
                <tr>
                    <td>{{ $patient->full_name }}</td>
                    <td>{{ $patient->email ?? '-' }}</td>
                    <td>{{ $patient->phone_number ?? '-' }}</td>
                    <td>{{ $patient->gender ?? '-' }}</td>
                    <td>{{ $patient->emergency_contact ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
