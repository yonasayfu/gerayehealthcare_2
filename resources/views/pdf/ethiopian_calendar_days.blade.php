<!DOCTYPE html>
<html>
<head>
    <title>Ethiopian Calendar Days</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Ethiopian Calendar Days</h1>
    <table>
        <thead>
            <tr>
                <th>Gregorian Date</th>
                <th>Ethiopian Date</th>
                <th>Description</th>
                <th>Is Holiday</th>
                <th>Region</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ethiopianCalendarDays as $day)
            <tr>
                <td>{{ $day->gregorian_date }}</td>
                <td>{{ $day->ethiopian_date }}</td>
                <td>{{ $day->description }}</td>
                <td>{{ $day->is_holiday ? 'Yes' : 'No' }}</td>
                <td>{{ $day->region }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
