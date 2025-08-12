{{-- Universal PDF Report Template --}}
{{-- This single template can handle all PDF generation needs --}}
<x-printable-report
    :title="$config['title'] ?? 'Report'"
    :data="$data ?? []"
    :columns="$config['columns'] ?? []"
    :header-info="$config['header_info'] ?? [
        'logo' => public_path('images/geraye_logo.jpeg'),
        'clinic_name' => 'Geraye Home Care Services',
        'document_title' => $config['title'] ?? 'Report',
    ]"
    :footer-info="$config['footer_info'] ?? [
        'generated_date' => now()->format('Y-m-d H:i:s'),
    ]"
/>
