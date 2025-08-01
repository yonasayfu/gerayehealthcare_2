<!DOCTYPE html>
<html>
<head>
    <title>Eligibility Criteria Details</title>
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
            <h1>Eligibility Criteria Details</h1>
        </div>

        <div class="detail-row">
            <span class="detail-label">Event ID:</span> {{ $eligibilityCriteria->event_id }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Criteria Name:</span> {{ $eligibilityCriteria->criteria_name }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Operator:</span> {{ $eligibilityCriteria->operator }}
        </div>
        <div class="detail-row">
            <span class="detail-label">Value:</span> {{ $eligibilityCriteria->value }}
        </div>
    </div>
</body>
</html>
