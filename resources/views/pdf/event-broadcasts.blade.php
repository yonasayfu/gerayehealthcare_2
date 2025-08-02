<x-printable-report
    title="Event Broadcasts List"
    :data="$broadcasts->map(function($broadcast) {
        return [
            'event_id' => $broadcast->event_id,
            'channel' => $broadcast->channel,
            'message' => $broadcast->message,
            'sent_by_staff_id' => $broadcast->sent_by_staff_id,
        ];
    })->toArray()"
    :columns="[
        ['key' => 'event_id', 'label' => 'Event ID'],
        ['key' => 'channel', 'label' => 'Channel'],
        ['key' => 'message', 'label' => 'Message'],
        ['key' => 'sent_by_staff_id', 'label' => 'Sent By Staff ID'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Event Broadcasts List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>