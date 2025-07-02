<!DOCTYPE html>
<html>
<head>
    <title>Patients Export</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Geraye Home-to-Home Care</h1>
    <p style="text-align:center;">Patient Export List</p>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Emergency Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($patients as $p)
                <tr>
                    <td>{{ $p->full_name }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->phone_number }}</td>
                    <td>{{ $p->gender }}</td>
                    <td>{{ $p->emergency_contact }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
