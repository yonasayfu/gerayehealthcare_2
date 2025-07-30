<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assignment Record - #{{ $assignment->id }}</title>
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
        .assignment-info {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .assignment-id {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
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
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" class="logo">
        <h1>Geraye Home Care Services</h1>
        <p class="subtitle">Caregiver Assignment Record</p>
    </header>

    <div class="assignment-info">
        <div class="assignment-id">Assignment #{{ $assignment->id }}</div>
        
        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <div class="info-label">Patient</div>
                    <div class="info-value">{{ $assignment->patient->full_name ?? 'N/A' }}</div>
                </td>
                <td style="width: 50%;">
                    <div class="info-label">Staff</div>
                    <div class="info-value">{{ $assignment->staff->first_name ?? '' }} {{ $assignment->staff->last_name ?? '' }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="info-label">Shift Start</div>
                    <div class="info-value">{{ $assignment->shift_start ? \Carbon\Carbon::parse($assignment->shift_start)->format('F j, Y, g:i a') : 'N/A' }}</div>
                </td>
                <td>
                    <div class="info-label">Shift End</div>
                    <div class="info-value">{{ $assignment->shift_end ? \Carbon\Carbon::parse($assignment->shift_end)->format('F j, Y, g:i a') : 'N/A' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="info-label">Status</div>
                    <div class="info-value">{{ $assignment->status ?? 'N/A' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y, g:i a') }}</p>
        <p>Geraye Home Care Services - Assignment Record System</p>
    </div>
</body>
</html>
