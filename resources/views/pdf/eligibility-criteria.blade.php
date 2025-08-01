<!DOCTYPE html>
<html>
<head>
    <title>Eligibility Criteria List</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Eligibility Criteria List</h1>
    <table>
        <thead>
            <tr>
                <th>Event ID</th>
                <th>Criteria Name</th>
                <th>Operator</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($criteria as $criterion)
            <tr>
                <td>{{ $criterion->event_id }}</td>
                <td>{{ $criterion->criteria_name }}</td>
                <td>{{ $criterion->operator }}</td>
                <td>{{ $criterion->value }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
