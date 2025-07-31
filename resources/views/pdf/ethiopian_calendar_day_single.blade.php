<!DOCTYPE html>
<html>
<head>
    <title>Ethiopian Calendar Day Details</title>
    <style>
        body { font-family: sans-serif; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table th, .detail-table td { border: 1px solid black; padding: 8px; text-align: left; }
        .detail-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Ethiopian Calendar Day Details</h1>

    <table class="detail-table">
        <tr>
            <th>ID</th>
            <td>{{ $ethiopianCalendarDay->id }}</td>
        </tr>
        <tr>
            <th>Gregorian Date</th>
            <td>{{ $ethiopianCalendarDay->gregorian_date }}</td>
        </tr>
        <tr>
            <th>Ethiopian Date</th>
            <td>{{ $ethiopianCalendarDay->ethiopian_date }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{ $ethiopianCalendarDay->description }}</td>
        </tr>
        <tr>
            <th>Is Holiday</th>
            <td>{{ $ethiopianCalendarDay->is_holiday ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <th>Region</th>
            <td>{{ $ethiopianCalendarDay->region }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $ethiopianCalendarDay->created_at }}</td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td>{{ $ethiopianCalendarDay->updated_at }}</td>
        </tr>
    </table>
</body>
</html>
