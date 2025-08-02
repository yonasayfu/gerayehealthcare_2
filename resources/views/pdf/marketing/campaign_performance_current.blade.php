<x-printable-report
    title="Current Campaign Performance Report"
    :data="$performanceData->map(function($data) {
        return [
            'date' => $data->date,
            'impressions' => $data->impressions,
            'clicks' => $data->clicks,
            'conversions' => $data->conversions,
            'revenue_generated' => $data->revenue_generated,
            'total_cost' => $data->total_cost,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'date', 'label' => 'Date'],
        ['key' => 'impressions', 'label' => 'Impressions'],
        ['key' => 'clicks', 'label' => 'Clicks'],
        ['key' => 'conversions', 'label' => 'Conversions'],
        ['key' => 'revenue_generated', 'label' => 'Revenue Generated'],
        ['key' => 'total_cost', 'label' => 'Total Cost'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Current Campaign Performance Report',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>