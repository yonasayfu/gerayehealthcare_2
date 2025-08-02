<x-printable-report
    title="Lead Sources List"
    :data="$leadSources->map(function($source) {
        return [
            'name' => $source->name,
            'category' => $source->category ?? '-',
            'is_active' => $source->is_active ? 'Yes' : 'No',
        ];
    })->toArray()"
    :columns="[
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'category', 'label' => 'Category'],
        ['key' => 'is_active', 'label' => 'Active'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Lead Sources List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>