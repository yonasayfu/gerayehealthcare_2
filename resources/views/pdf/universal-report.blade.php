{{-- Universal PDF Report Template --}}
{{-- This single template can handle all PDF generation needs --}}
<x-printable-report
    :title="$config['title'] ?? 'Report'"
    :data="$data ?? []"
    :columns="$config['columns'] ?? []"
    :header-info="$config['header_info'] ?? [
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => $config['title'] ?? 'Report',
    ]"
    :footer-info="$config['footer_info'] ?? [
        'generatedDate' => true,
    ]"
/>
