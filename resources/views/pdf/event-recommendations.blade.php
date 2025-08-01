<!DOCTYPE html>
<html>
<head>
    <title>Event Recommendations List</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Event Recommendations List</h1>
    <table>
        <thead>
            <tr>
                <th>Event ID</th>
                <th>Source</th>
                <th>Recommended By</th>
                <th>Patient Name</th>
                <th>Patient Phone</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recommendations as $recommendation)
            <tr>
                <td>{{ $recommendation->event_id }}</td>
                <td>{{ $recommendation->source }}</td>
                <td>{{ $recommendation->recommended_by }}</td>
                <td>{{ $recommendation->patient_name }}</td>
                <td>{{ $recommendation->patient_phone }}</td>
                <td>{{ $recommendation->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
