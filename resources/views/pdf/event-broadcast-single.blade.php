<!DOCTYPE html>
<html>
<head>
    <title>Event Broadcast Details</title>
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
            <h1>Event Broadcast Details</h1>
        </div>

        <div class="detail-row">
            <span class="detail-label">Event ID:</span> {{ $broadcast->event_id }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Channel:</span> {{ $broadcast->channel }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Message:</span> {{ $broadcast->message }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Sent By Staff ID:</span> {{ $broadcast->sent_by_staff_id }}
        </div>
    </div>
</body>
</html>
