<x-printable-report
    title="Campaign Contents List"
    :data="$campaignContents->map(function($content) {
        return [
            'title' => $content->title,
            'campaign_name' => $content->campaign->campaign_name,
            'platform_name' => $content->platform->name,
            'content_type' => $content->content_type,
            'status' => $content->status,
            'scheduled_post_date' => $content->scheduled_post_date->format('Y-m-d H:i'),
        ];
    })->toArray()"
    :columns="[
        ['key' => 'title', 'label' => 'Title'],
        ['key' => 'campaign_name', 'label' => 'Campaign'],
        ['key' => 'platform_name', 'label' => 'Platform'],
        ['key' => 'content_type', 'label' => 'Type'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'scheduled_post_date', 'label' => 'Scheduled Post Date'],
    ]"
    :header-info="[
        'logoSrc' => public_path('images/geraye_logo.jpeg'),
        'clinicName' => 'Geraye Home Care Services',
        'documentTitle' => 'Campaign Contents List',
    ]"
    :footer-info="[
        'generatedDate' => true,
    ]"
/>