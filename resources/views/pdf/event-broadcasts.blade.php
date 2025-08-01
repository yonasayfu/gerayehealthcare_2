<!DOCTYPE html>
<html>
<head>
    <title>Event Broadcasts List</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Event Broadcasts List</h1>
    <table>
        <thead>
            <tr>
                <th>Event ID</th>
                <th>Channel</th>
                <th>Message</th>
                <th>Sent By Staff ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($broadcasts as $broadcast)
            <tr>
                <td>{{ $broadcast->event_id }}</td>
                <td>{{ $broadcast->channel }}</td>
                <td>{{ $broadcast->message }}</td>
                <td>{{ $broadcast->sent_by_staff_id }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
