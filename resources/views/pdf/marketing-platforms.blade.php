<x-printable-report
    title="Marketing Platforms List"
    :data="$marketingPlatforms->map(function($platform) {
        return [
            'name' => $platform->name,
            'api_endpoint' => $platform->api_endpoint ?? '-',
            'is_active' => $platform->is_active ? 'Yes' : 'No',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'api_endpoint', 'label' => 'API Endpoint'],
        ['key' => 'is_active', 'label' => 'Active'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Marketing Platforms List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>