<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Export</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; } /* Slightly smaller font for more columns */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; } /* Adjust margin-top */
        th, td { border: 1px solid #333; padding: 6px; text-align: left; vertical-align: top; } /* Adjust padding and align top */
        th { background-color: #f3f3f3; font-weight: bold; }
        h2 { margin-bottom: 5px; font-size: 18px; } /* Adjust heading size */
        p { margin-top: 0; font-size: 10px; color: #666; } /* Style for subtitle/date */
        .header { text-align: center; margin-bottom: 20px; }
        .logo { max-width: 100px; max-height: 40px; margin-bottom: 10px; } /* Adjust logo size */
    </style>
</head>
<body>
    <div class="header">
        {{-- You might want to dynamically get your hospital name/logo here --}}
        {{-- Assuming you have a logo at public/images/geraye_logo.jpeg --}}
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Hospital Logo" class="logo">
        <h2>Geraye Hospital</h2>
        <p>All Patient Records Export</p>
        <p>Generated: {{ now()->toDateTimeString() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Patient Code</th>
                <th>Fayda ID</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Address</th>
                <th>Source</th>
                <th>Emergency Contact</th>
                <th>Registered Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
                <tr>
                    <td>{{ $patient->full_name }}</td>
                    <td>{{ $patient->patient_code ?? '-' }}</td>
                    <td>{{ $patient->fayda_id ?? '-' }}</td>
                    <td>{{ $patient->email ?? '-' }}</td>
                    <td>{{ $patient->phone_number ?? '-' }}</td>
                    <td>{{ $patient->gender ?? '-' }}</td>
                    <td>{{ $patient->address ?? '-' }}</td>
                    <td>{{ $patient->source ?? '-' }}</td>
                    <td>{{ $patient->emergency_contact ?? '-' }}</td>
                    <td>{{ $patient->created_at->format('Y-m-d H:i') }}</td> {{-- Format the date --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>