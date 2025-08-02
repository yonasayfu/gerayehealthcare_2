<x-printable-report
    title="Events List"
    :data="$events->map(function($event) {
        return [
            'title' => $event->title,
            'description' => $event->description,
            'event_date' => $event->event_date,
            'is_free_service' => $event->is_free_service ? 'Yes' : 'No',
            'broadcast_status' => $event->broadcast_status,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'title', 'label' => 'Title'],
        ['key' => 'description', 'label' => 'Description'],
        ['key' => 'event_date', 'label' => 'Event Date'],
        ['key' => 'is_free_service', 'label' => 'Free Service'],
        ['key' => 'broadcast_status', 'label' => 'Broadcast Status'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Events List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>