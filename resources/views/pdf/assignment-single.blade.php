<x-printable-report
    title="Assignment Record - #{{ $assignment->id }}"
    :data="[
        ['label' => 'Patient', 'value' => $assignment->patient->full_name ?? 'N/A'],
        ['label' => 'Staff', 'value' => ($assignment->staff->first_name ?? '') . ' ' . ($assignment->staff->last_name ?? '')],
        ['label' => 'Shift Start', 'value' => $assignment->shift_start ? \Carbon\Carbon::parse($assignment->shift_start)->format('F j, Y, g:i a') : 'N/A'],
        ['label' => 'Shift End', 'value' => $assignment->shift_end ? \Carbon\Carbon::parse($assignment->shift_end)->format('F j, Y, g:i a') : 'N/A'],
        ['label' => 'Status', 'value' => $assignment->status ?? 'N/A'],
    ]"
    :columns="[
        ['key' => 'label', 'label' => 'Field', 'printWidth' => '30%'],
        ['key' => 'value', 'label' => 'Value', 'printWidth' => '70%'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Caregiver Assignment Record',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>