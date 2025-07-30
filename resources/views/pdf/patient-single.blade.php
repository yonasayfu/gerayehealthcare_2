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
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 24px;
            margin: 0;
            color: #2c3e50;
        }
        .subtitle {
            font-size: 16px;
            margin: 5px 0;
            color: #666;
        }
        .patient-info {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .patient-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .patient-code {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .info-table td {
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 13px;
            color: #212529;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        .confidential {
            text-align: center;
            font-size: 11px;
            color: #dc3545;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" class="logo">
        <h1>Geraye Home Care Services</h1>
        <p class="subtitle">Patient Record Card</p>
    </header>

    <div class="patient-info">
        <div class="patient-name">{{ $patient->full_name }}</div>
        <div class="patient-code">Patient Code: {{ $patient->patient_code ?? 'N/A' }}</div>
        
        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <div class="info-label">Fayda ID</div>
                    <div class="info-value">{{ $patient->fayda_id ?? 'N/A' }}</div>
                </td>
                <td style="width: 50%;">
                    <div class="info-label">Source</div>
                    <div class="info-value">{{ $patient->source ?? 'N/A' }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $patient->phone_number ?? 'N/A' }}</div>
                </td>
                <td>
                    <div class="info-label">Date of Birth</div>
                    <div class="info-value">{{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('F j, Y') : 'N/A' }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="info-label">Gender</div>
                    <div class="info-value">{{ $patient->gender ?? 'N/A' }}</div>
                </td>
                <td>
                    <div class="info-label">Emergency Contact</div>
                    <div class="info-value">{{ $patient->emergency_contact ?? 'N/A' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="info-label">Address</div>
                    <div class="info-value">{{ $patient->address ?? 'N/A' }}</div>
                </td>
            </tr>
            @if($patient->geolocation)
            <tr>
                <td colspan="2">
                    <div class="info-label">Geolocation</div>
                    <div class="info-value">{{ $patient->geolocation }}</div>
                </td>
            </tr>
            @endif
        </table>
    </div>

    <div class="confidential">
        CONFIDENTIAL - This document contains sensitive patient information
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y, g:i a') }}</p>
        <p>Geraye Home Care Services - Patient Record System</p>
    </div>
</body>
</html>
