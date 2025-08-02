<x-printable-report
    title="All Caregiver Assignments - Geraye"
    :data="$assignments->map(function($assignment) {
        return [
            'patient_name' => $assignment->patient->full_name ?? 'N/A',
            'staff_member' => ($assignment->staff->first_name ?? '') . ' ' . ($assignment->staff->last_name ?? ''),
            'shift_start' => $assignment->shift_start ? \Carbon\Carbon::parse($assignment->shift_start)->format('F j, Y, g:i a') : 'N/A',
            'shift_end' => $assignment->shift_end ? \Carbon\Carbon::parse($assignment->shift_end)->format('F j, Y, g:i a') : 'N/A',
            'status' => $assignment->status,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'patient_name', 'label' => 'Patient Name'],
        ['key' => 'staff_member', 'label' => 'Staff Member'],
        ['key' => 'shift_start', 'label' => 'Shift Start'],
        ['key' => 'shift_end', 'label' => 'Shift End'],
        ['key' => 'status', 'label' => 'Status'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Caregiver Assignment Records Export',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>