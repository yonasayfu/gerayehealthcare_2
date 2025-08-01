<!DOCTYPE html>
<html>
<head>
    <title>Event Participants List</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Event Participants List</h1>
    <table>
        <thead>
            <tr>
                <th>Event ID</th>
                <th>Patient ID</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $participant)
            <tr>
                <td>{{ $participant->event_id }}</td>
                <td>{{ $participant->patient_id }}</td>
                <td>{{ $participant->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
