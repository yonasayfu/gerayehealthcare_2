<!DOCTYPE html>
<html>
<head>
    <title>Event Recommendation Details</title>
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
            <h1>Event Recommendation Details</h1>
        </div>

        <div class="detail-row">
            <span class="detail-label">Event ID:</span> {{ $eventRecommendation->event_id }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Source:</span> {{ $eventRecommendation->source }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Recommended By:</span> {{ $eventRecommendation->recommended_by }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Patient Name:</span> {{ $eventRecommendation->patient_name }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Patient Phone:</span> {{ $eventRecommendation->patient_phone }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Notes:</span> {{ $eventRecommendation->notes }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span> {{ $eventRecommendation->status }}
        </div>
    </div>
</body>
</html>
