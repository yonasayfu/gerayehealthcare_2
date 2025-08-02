<x-printable-report
    title="Assignment Export - Geraye"
    :data="$assignments->map(function($assignment) {
        return [
            'index' => $loop->index + 1,
            'patient_name' => $assignment->patient->full_name ?? 'N/A',
            'staff_member' => ($assignment->staff->first_name ?? '') . ' ' . ($assignment->staff->last_name ?? ''),
            'shift_start' => $assignment->shift_start ? \Carbon\Carbon::parse($assignment->shift_start)->format('Y-m-d H:i') : 'N/A',
            'shift_end' => $assignment->shift_end ? \Carbon\Carbon::parse($assignment->shift_end)->format('Y-m-d H:i') : 'N/A',
            'status' => $assignment->status,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'index', 'label' => '#'],
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