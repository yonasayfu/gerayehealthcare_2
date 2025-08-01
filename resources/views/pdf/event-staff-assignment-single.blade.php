<!DOCTYPE html>
<html>
<head>
    <title>Event Staff Assignment Details</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; }
        .container { width: 80%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .detail-row { margin-bottom: 10px; }
        .detail-label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Event Staff Assignment Details</h1>
        </div>

        <div class="detail-row">
            <span class="detail-label">Event ID:</span> {{ $assignment->event_id }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Staff ID:</span> {{ $assignment->staff_id }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Role:</span> {{ $assignment->role }}
        </div>
    </div>
</body>
</html>
