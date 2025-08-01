<!DOCTYPE html>
<html>
<head>
    <title>Event Participant Details</title>
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
            <h1>Event Participant Details</h1>
        </div>

        <div class="detail-row">
            <span class="detail-label">Event ID:</span> {{ $participant->event_id }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Patient ID:</span> {{ $participant->patient_id }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span> {{ $participant->status }}
        </div>
    </div>
</body>
</html>
