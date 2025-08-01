<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Record - {{ $patient->full_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            color: #333;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 20px;
            margin: 0;
        }
        p {
            font-size: 14px;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 8px 10px;
            border: 1px solid #999;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 11px;
        }
        .detail-section {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .detail-section:last-child {
            border-bottom: none;
        }
        .detail-row {
            display: flex;
            margin-bottom: 5px;
        }
        .detail-label {
            font-weight: bold;
            width: 150px; /* Adjust as needed */
        }
        .detail-value {
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
        <h1>Geraye Home Care Services</h1>
        <p>Patient Record</p>
    </header>

    <div class="detail-section">
        <h2>Patient Identification</h2>
        <div class="detail-row"><span class="detail-label">Full Name:</span> <span class="detail-value">{{ $patient->full_name }}</span></div>
        <div class="detail-row"><span class="detail-label">Patient Code:</span> <span class="detail-value">{{ $patient->patient_code ?? '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Fayda ID:</span> <span class="detail-value">{{ $patient->fayda_id ?? '-' }}</span></div>
    </div>

    <div class="detail-section">
        <h2>Demographics</h2>
        <div class="detail-row"><span class="detail-label">Gender:</span> <span class="detail-value">{{ $patient->gender ?? '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Date of Birth:</span> <span class="detail-value">{{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') : '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Age:</span> <span class="detail-value">{{ $patient->age !== null ? $patient->age : '-' }}</span></div>
    </div>

    <div class="detail-section">
        <h2>Contact Information</h2>
        <div class="detail-row"><span class="detail-label">Phone Number:</span> <span class="detail-value">{{ $patient->phone_number ?? '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Email:</span> <span class="detail-value">{{ $patient->email ?? '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Emergency Contact:</span> <span class="detail-value">{{ $patient->emergency_contact ?? '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Address:</span> <span class="detail-value">{{ $patient->address ?? '-' }}</span></div>
    </div>

    <div class="detail-section">
        <h2>Administrative Details</h2>
        <div class="detail-row"><span class="detail-label">Source:</span> <span class="detail-value">{{ $patient->source ?? '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Geolocation:</span> <span class="detail-value">{{ $patient->geolocation ?? '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Registered By:</span> <span class="detail-value">{{ $patient->registeredByStaff->full_name ?? '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Registered Date:</span> <span class="detail-value">{{ $patient->created_at ? \Carbon\Carbon::parse($patient->created_at)->format('M d, Y H:i') : '-' }}</span></div>
        <div class="detail-row"><span class="detail-label">Last Updated:</span> <span class="detail-value">{{ $patient->updated_at ? \Carbon\Carbon::parse($patient->updated_at)->format('M d, Y H:i') : '-' }}</span></div>
    </div>

    <div class="footer">
        Document Generated: {{ now()->format('F j, Y, g:i a') }}
    </div>
</body>
</html>
