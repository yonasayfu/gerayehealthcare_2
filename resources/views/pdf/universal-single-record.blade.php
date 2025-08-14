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
        'logo' => public_path('images/geraye_logo.jpeg'),
        'clinic_name' => 'Geraye Home Care Services',
        'document_title' => $config['title'] ?? 'Record Details',
    ]"
    :footer-info="$config['footer_info'] ?? [
        'generated_date' => now()->format('Y-m-d H:i:s'),
    ]"
/>
