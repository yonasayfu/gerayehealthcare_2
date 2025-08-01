<!DOCTYPE html>
<html>
<head>
    <title>Event Staff Assignments List</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Event Staff Assignments List</h1>
    <table>
        <thead>
            <tr>
                <th>Event ID</th>
                <th>Staff ID</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignments as $assignment)
            <tr>
                <td>{{ $assignment->event_id }}</td>
                <td>{{ $assignment->staff_id }}</td>
                <td>{{ $assignment->role }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
