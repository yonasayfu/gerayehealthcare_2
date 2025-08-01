<!DOCTYPE html>
<html>
<head>
    <title>Events List</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Events List</h1>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Event Date</th>
                <th>Free Service</th>
                <th>Broadcast Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td>{{ $event->title }}</td>
                <td>{{ $event->description }}</td>
                <td>{{ $event->event_date }}</td>
                <td>{{ $event->is_free_service ? 'Yes' : 'No' }}</td>
                <td>{{ $event->broadcast_status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
