{{-- Universal Single Record PDF Template --}}
{{-- This template handles individual record details in a field-value format --}}
<x-printable-report
    :title="$config['title'] ?? 'Record Details'"
    :data="$data ?? []"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="$config['header_info'] ?? [
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => $config['title'] ?? 'Record Details',
    ]"
    :footer-info="$config['footer_info'] ?? [
        'generatedDate' => true,
    ]"
/>
