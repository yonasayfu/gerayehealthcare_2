<!DOCTYPE html>
<html>
<head>
    <title>Event Details</title>
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
            <h1>Event Details</h1>
        </div>

        <div class="detail-row">
            <span class="detail-label">Title:</span> {{ $event->title }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Description:</span> {{ $event->description }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Event Date:</span> {{ $event->event_date }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Free Service:</span> {{ $event->is_free_service ? 'Yes' : 'No' }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Broadcast Status:</span> {{ $event->broadcast_status }}
        </div>
    </div>
</body>
</html>
