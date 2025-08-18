<?php

namespace App\Http\Config;

class ExportConfig
{
    /**
     * Get export configuration for Patient model
     */
    public static function getPatientConfig(): array
    {
        return [
            'searchable_fields' => ['full_name', 'email'],
            'sortable_fields' => ['full_name', 'patient_code', 'created_at', 'date_of_birth'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'patients', // Added for CSV export
            'select_fields' => [
                'full_name', 'patient_code', 'fayda_id', 'email', 'source',
                'phone_number', 'address', 'gender', 'emergency_contact', 'date_of_birth',
            ],

            

            'csv' => [
                'headers' => [
                    'Full Name', 'Patient Code', 'Fayda ID', 'Email', 'Source',
                    'Phone', 'Address', 'Gender', 'Emergency Contact',
                ],
                'fields' => [
                    'full_name', 'patient_code', 'fayda_id', 'email', 'source',
                    'phone_number', 'address', 'gender', 'emergency_contact',
                ],
                'filename_prefix' => 'patients', // Changed from 'filename' to 'filename_prefix'
            ],

            'pdf' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Patient Export - Geraye Home Care Services',
                'document_title' => 'Patient Records Export',
                'filename_prefix' => 'patients',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'full_name' => 'full_name',
                    'patient_code' => ['field' => 'patient_code', 'default' => '-'],
                    'fayda_id' => ['field' => 'fayda_id', 'default' => '-'],
                    'email' => 'email',
                    'source' => ['field' => 'source', 'default' => '-'],
                    'phone_number' => 'phone_number',
                    'address' => ['field' => 'address', 'default' => '-'],
                    'gender' => 'gender',
                    'emergency_contact' => 'emergency_contact',
                ],
                'columns' => [
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'patient_code', 'label' => 'Patient Code'],
                    ['key' => 'fayda_id', 'label' => 'Fayda ID'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'source', 'label' => 'Source'],
                    ['key' => 'phone_number', 'label' => 'Phone'],
                    ['key' => 'address', 'label' => 'Address'],
                    ['key' => 'gender', 'label' => 'Gender'],
                    ['key' => 'emergency_contact', 'label' => 'Emergency Contact'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Patient List (Current View) - Geraye Home Care Services',
                'document_title' => 'Patient List (Current View)',
                'filename_prefix' => 'patients-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['registeredByStaff'],
                'fields' => [
                    'full_name' => 'full_name',
                    'patient_code' => ['field' => 'patient_code', 'default' => '-'],
                    'fayda_id' => ['field' => 'fayda_id', 'default' => '-'],
                    'age' => [
                        'field' => 'date_of_birth',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->age : '-';
                        },
                    ],
                    'gender' => ['field' => 'gender', 'default' => '-'],
                    'phone_number' => 'phone_number',
                    'source' => ['field' => 'source', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'patient_code', 'label' => 'Patient Code'],
                    ['key' => 'fayda_id', 'label' => 'Fayda ID'],
                    ['key' => 'age', 'label' => 'Age'],
                    ['key' => 'gender', 'label' => 'Gender'],
                    ['key' => 'phone_number', 'label' => 'Phone'],
                    ['key' => 'source', 'label' => 'Source'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Patient List - Geraye Home Care Services',
                'document_title' => 'Patient Records Export',
                'filename_prefix' => 'patients',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'full_name',
                'with_relations' => ['registeredByStaff'],
                'fields' => [
                    'full_name' => 'full_name',
                    'patient_code' => ['field' => 'patient_code', 'default' => '-'],
                    'fayda_id' => ['field' => 'fayda_id', 'default' => '-'],
                    'age' => [
                        'field' => 'date_of_birth',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->age : '-';
                        },
                    ],
                    'gender' => ['field' => 'gender', 'default' => '-'],
                    'phone_number' => 'phone_number',
                    'source' => ['field' => 'source', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'patient_code', 'label' => 'Patient Code'],
                    ['key' => 'fayda_id', 'label' => 'Fayda ID'],
                    ['key' => 'age', 'label' => 'Age'],
                    ['key' => 'gender', 'label' => 'Gender'],
                    ['key' => 'phone_number', 'label' => 'Phone'],
                    ['key' => 'source', 'label' => 'Source'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Patient Record - Geraye Home Care Services',
                'document_title' => 'Patient Record',
                'filename_prefix' => 'patient-record',
                'with_relations' => ['registeredByStaff'],
                'fields' => [
                    'Full Name' => 'full_name',
                    'Patient Code' => ['field' => 'patient_code', 'default' => '-'],
                    'Fayda ID' => ['field' => 'fayda_id', 'default' => '-'],
                    'Date of Birth' => 'date_of_birth',
                    'Age' => [
                        'field' => 'date_of_birth',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->age . ' years' : '-';
                        },
                    ],
                    'Gender' => ['field' => 'gender', 'default' => '-'],
                    'Phone Number' => 'phone_number',
                    'Email' => 'email',
                    'Address' => ['field' => 'address', 'default' => '-'],
                    'Emergency Contact' => ['field' => 'emergency_contact', 'default' => '-'],
                    'Source' => ['field' => 'source', 'default' => '-'],
                    'Registered By Staff' => ['field' => 'registeredByStaff.full_name', 'default' => '-'],
                    'Registered Date' => ['field' => 'created_at', 'transform' => function ($value) {return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-';}],
                    'Last Updated' => ['field' => 'updated_at', 'transform' => function ($value) {return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-';}],
                ],
                'columns' => [
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'patient_code', 'label' => 'Patient Code'],
                    ['key' => 'fayda_id', 'label' => 'Fayda ID'],
                    ['key' => 'date_of_birth', 'label' => 'Date of Birth'],
                    ['key' => 'age', 'label' => 'Age', 'transform' => function ($value, $model) {return $model->date_of_birth ? \Carbon\Carbon::parse($model->date_of_birth)->age . ' years' : '-';}],
                    ['key' => 'gender', 'label' => 'Gender'],
                    ['key' => 'phone_number', 'label' => 'Phone Number'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'address', 'label' => 'Address'],
                    ['key' => 'emergency_contact', 'label' => 'Emergency Contact'],
                    ['key' => 'source', 'label' => 'Source'],
                    ['key' => 'registeredByStaff.full_name', 'label' => 'Registered By Staff'],
                    ['key' => 'created_at', 'label' => 'Registered Date', 'transform' => function ($value) {return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-';}],
                    ['key' => 'updated_at', 'label' => 'Last Updated', 'transform' => function ($value) {return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-';}],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for Event model
     */
    public static function getEventConfig(): array
    {
        return [
            'searchable_fields' => ['title', 'description', 'broadcast_status'],
            'sortable_fields' => ['title', 'event_date', 'broadcast_status', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'events',

            'csv' => [
                'headers' => ['#', 'Title', 'Description', 'Event Date', 'Free Service', 'Status'],
                'fields' => [
                    'index',
                    'title',
                    'description',
                    [
                        'field' => 'event_date',
                        'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'is_free_service',
                        'transform' => function ($v) { return $v ? 'Yes' : 'No'; },
                    ],
                    'broadcast_status',
                ],
                'filename_prefix' => 'events',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Events - Geraye Home Care Services',
                'document_title' => 'Events Export',
                'filename_prefix' => 'events',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'title' => 'title',
                    'description' => ['field' => 'description', 'default' => '-'],
                    'event_date' => [ 'field' => 'event_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'is_free_service' => [ 'field' => 'is_free_service', 'transform' => function ($v) { return $v ? 'Yes' : 'No'; } ],
                    'broadcast_status' => 'broadcast_status',
                ],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'event_date', 'label' => 'Event Date'],
                    ['key' => 'is_free_service', 'label' => 'Free Service'],
                    ['key' => 'broadcast_status', 'label' => 'Status'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Events (Current View) - Geraye Home Care Services',
                'document_title' => 'Events (Current View)',
                'filename_prefix' => 'events-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'title' => 'title',
                    'description' => ['field' => 'description', 'default' => '-'],
                    'event_date' => [ 'field' => 'event_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'is_free_service' => [ 'field' => 'is_free_service', 'transform' => function ($v) { return $v ? 'Yes' : 'No'; } ],
                    'broadcast_status' => 'broadcast_status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'event_date', 'label' => 'Event Date'],
                    ['key' => 'is_free_service', 'label' => 'Free Service'],
                    ['key' => 'broadcast_status', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Events - Geraye Home Care Services',
                'document_title' => 'All Events',
                'filename_prefix' => 'events',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'event_date',
                'fields' => [
                    'title' => 'title',
                    'description' => ['field' => 'description', 'default' => '-'],
                    'event_date' => [ 'field' => 'event_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'is_free_service' => [ 'field' => 'is_free_service', 'transform' => function ($v) { return $v ? 'Yes' : 'No'; } ],
                    'broadcast_status' => 'broadcast_status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'event_date', 'label' => 'Event Date'],
                    ['key' => 'is_free_service', 'label' => 'Free Service'],
                    ['key' => 'broadcast_status', 'label' => 'Status'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Event Detail - Geraye Home Care Services',
                'document_title' => 'Event Detail',
                'filename_prefix' => 'event-record',
                'fields' => [
                    'Title' => 'title',
                    'Description' => ['field' => 'description', 'default' => '-'],
                    'Event Date' => [ 'field' => 'event_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Free Service' => [ 'field' => 'is_free_service', 'transform' => function ($v) { return $v ? 'Yes' : 'No'; } ],
                    'Status' => 'broadcast_status',
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'event_date', 'label' => 'Event Date'],
                    ['key' => 'is_free_service', 'label' => 'Free Service'],
                    ['key' => 'broadcast_status', 'label' => 'Status'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for MarketingBudget model
     */
    public static function getMarketingBudgetConfig(): array
    {
        return [
            'searchable_fields' => ['budget_name', 'status', 'campaign.campaign_name', 'platform.name'],
            'sortable_fields' => ['budget_name', 'allocated_amount', 'spent_amount', 'period_start', 'period_end', 'status', 'campaign_id', 'platform_id', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'marketing-budgets',

            'csv' => [
                'headers' => [
                    '#', 'Budget Name', 'Campaign', 'Platform', 'Allocated', 'Spent', 'Start Date', 'End Date', 'Status',
                ],
                'fields' => [
                    'index',
                    'budget_name',
                    ['field' => 'campaign.campaign_name', 'default' => '-'],
                    ['field' => 'platform.name', 'default' => '-'],
                    [
                        'field' => 'allocated_amount',
                        'transform' => function ($value) { return number_format((float)$value, 2); },
                    ],
                    [
                        'field' => 'spent_amount',
                        'transform' => function ($value) { return $value !== null ? number_format((float)$value, 2) : '0.00'; },
                    ],
                    [
                        'field' => 'period_start',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'period_end',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'status',
                ],
                'with_relations' => ['campaign', 'platform'],
                'filename_prefix' => 'marketing-budgets',
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Marketing Budgets - Geraye',
                'document_title' => 'Marketing Budgets Export',
                'filename_prefix' => 'marketing-budgets-all',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['campaign', 'platform'],
                'fields' => [
                    'budget_name' => 'budget_name',
                    'campaign' => ['field' => 'campaign.campaign_name', 'default' => '-'],
                    'platform' => ['field' => 'platform.name', 'default' => '-'],
                    'allocated_amount' => [
                        'field' => 'allocated_amount',
                        'transform' => function ($value) { return number_format((float)$value, 2); },
                    ],
                    'spent_amount' => [
                        'field' => 'spent_amount',
                        'transform' => function ($value) { return $value !== null ? number_format((float)$value, 2) : '0.00'; },
                    ],
                    'period_start' => [
                        'field' => 'period_start',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'period_end' => [
                        'field' => 'period_end',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'budget_name', 'label' => 'Budget Name'],
                    ['key' => 'campaign.campaign_name', 'label' => 'Campaign'],
                    ['key' => 'platform.name', 'label' => 'Platform'],
                    ['key' => 'allocated_amount', 'label' => 'Allocated'],
                    ['key' => 'spent_amount', 'label' => 'Spent'],
                    ['key' => 'period_start', 'label' => 'Start Date'],
                    ['key' => 'period_end', 'label' => 'End Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Marketing Budgets (Current View) - Geraye',
                'document_title' => 'Marketing Budgets (Current View)',
                'filename_prefix' => 'marketing-budgets-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['campaign', 'platform'],
                'fields' => [
                    'budget_name' => 'budget_name',
                    'campaign' => ['field' => 'campaign.campaign_name', 'default' => '-'],
                    'platform' => ['field' => 'platform.name', 'default' => '-'],
                    'allocated_amount' => [
                        'field' => 'allocated_amount',
                        'transform' => function ($value) { return number_format((float)$value, 2); },
                    ],
                    'spent_amount' => [
                        'field' => 'spent_amount',
                        'transform' => function ($value) { return $value !== null ? number_format((float)$value, 2) : '0.00'; },
                    ],
                    'period_start' => [
                        'field' => 'period_start',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'period_end' => [
                        'field' => 'period_end',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'budget_name', 'label' => 'Budget Name'],
                    ['key' => 'campaign.campaign_name', 'label' => 'Campaign'],
                    ['key' => 'platform.name', 'label' => 'Platform'],
                    ['key' => 'allocated_amount', 'label' => 'Allocated'],
                    ['key' => 'spent_amount', 'label' => 'Spent'],
                    ['key' => 'period_start', 'label' => 'Start Date'],
                    ['key' => 'period_end', 'label' => 'End Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'filename_prefix' => 'marketing-budget-record',
                'with_relations' => ['campaign', 'platform'],
                'fields' => [
                    'Budget Name' => 'budget_name',
                    'Campaign' => ['field' => 'campaign.campaign_name', 'default' => '-'],
                    'Platform' => ['field' => 'platform.name', 'default' => '-'],
                    'Allocated Amount' => [
                        'field' => 'allocated_amount',
                        'transform' => function ($value) { return number_format((float)$value, 2); },
                    ],
                    'Spent Amount' => [
                        'field' => 'spent_amount',
                        'transform' => function ($value) { return $value !== null ? number_format((float)$value, 2) : '0.00'; },
                    ],
                    'Period Start' => [
                        'field' => 'period_start',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'Period End' => [
                        'field' => 'period_end',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'Status' => 'status',
                ],
                'columns' => [
                    ['key' => 'budget_name', 'label' => 'Budget Name'],
                    ['key' => 'campaign.campaign_name', 'label' => 'Campaign'],
                    ['key' => 'platform.name', 'label' => 'Platform'],
                    ['key' => 'allocated_amount', 'label' => 'Allocated Amount'],
                    ['key' => 'spent_amount', 'label' => 'Spent Amount'],
                    ['key' => 'period_start', 'label' => 'Period Start'],
                    ['key' => 'period_end', 'label' => 'Period End'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for LandingPage model
     */
    public static function getLandingPageConfig(): array
    {
        return [
            'searchable_fields' => ['page_title', 'page_url', 'page_code', 'campaign.campaign_name'],
            'sortable_fields' => ['page_title', 'page_code', 'language', 'is_active', 'campaign_id', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'landing-pages',

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Landing Pages - Geraye',
                'document_title' => 'Landing Pages Export',
                'filename_prefix' => 'landing-pages-all',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['campaign'],
                'fields' => [
                    'page_title' => 'page_title',
                    'page_code' => ['field' => 'page_code', 'default' => '-'],
                    'page_url' => ['field' => 'page_url', 'default' => '-'],
                    'language' => ['field' => 'language', 'default' => '-'],
                    'is_active' => [
                        'field' => 'is_active',
                        'transform' => function ($v) { return $v ? 'Yes' : 'No'; }
                    ],
                    'campaign' => ['field' => 'campaign.campaign_name', 'default' => '-'],
                    'created_at' => [
                        'field' => 'created_at',
                        'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'page_title', 'label' => 'Page Title'],
                    ['key' => 'page_code', 'label' => 'Page Code'],
                    ['key' => 'page_url', 'label' => 'URL'],
                    ['key' => 'language', 'label' => 'Language'],
                    ['key' => 'is_active', 'label' => 'Active'],
                    ['key' => 'campaign.campaign_name', 'label' => 'Campaign'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Landing Pages (Current View) - Geraye',
                'document_title' => 'Landing Pages (Current View)',
                'filename_prefix' => 'landing-pages-current',
                'orientation' => 'landscape',
                'hide_footer' => true,
                'include_index' => true,
                'with_relations' => ['campaign'],
                'fields' => [
                    'page_title' => 'page_title',
                    'page_code' => ['field' => 'page_code', 'default' => '-'],
                    'page_url' => ['field' => 'page_url', 'default' => '-'],
                    'language' => ['field' => 'language', 'default' => '-'],
                    'is_active' => [
                        'field' => 'is_active',
                        'transform' => function ($v) { return $v ? 'Yes' : 'No'; }
                    ],
                    'campaign' => ['field' => 'campaign.campaign_name', 'default' => '-'],
                    'created_at' => [
                        'field' => 'created_at',
                        'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'page_title', 'label' => 'Page Title'],
                    ['key' => 'page_code', 'label' => 'Page Code'],
                    ['key' => 'page_url', 'label' => 'URL'],
                    ['key' => 'language', 'label' => 'Language'],
                    ['key' => 'is_active', 'label' => 'Active'],
                    ['key' => 'campaign.campaign_name', 'label' => 'Campaign'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for InventoryAlert model
     */
    public static function getInventoryAlertConfig(): array
    {
        return [
            'searchable_fields' => ['alert_type', 'message', 'item.name'],
            'sortable_fields' => ['is_active', 'alert_type', 'triggered_at', 'created_at'],
            'default_sort' => 'created_at',
            'select_fields' => [
                'item_id', 'alert_type', 'message', 'is_active', 'triggered_at', 'created_at',
            ],

            // CSV intentionally not used for Inventory Alerts per latest requirement
            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Inventory Alerts - Geraye Home Care Services',
                'document_title' => 'Inventory Alerts',
                'filename_prefix' => 'inventory-alerts',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['item'],
                'fields' => [
                    'status' => [
                        'field' => 'is_active',
                        'transform' => function ($value) { return $value ? 'Active' : 'Resolved'; },
                    ],
                    'item' => ['field' => 'item.name', 'default' => '-'],
                    'qty_on_hand' => ['field' => 'item.quantity_on_hand', 'default' => '-'],
                    'reorder_level' => ['field' => 'item.reorder_level', 'default' => '-'],
                    'alert_type' => ['field' => 'alert_type', 'default' => '-'],
                    'triggered_at' => [
                        'field' => 'triggered_at',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                    'message' => ['field' => 'message', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'item.quantity_on_hand', 'label' => 'Qty on Hand'],
                    ['key' => 'item.reorder_level', 'label' => 'Reorder Level'],
                    ['key' => 'alert_type', 'label' => 'Alert Type'],
                    ['key' => 'triggered_at', 'label' => 'Triggered'],
                    ['key' => 'message', 'label' => 'Message'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Inventory Alerts (Current View) - Geraye Home Care Services',
                'document_title' => 'Inventory Alerts (Current View)',
                'filename_prefix' => 'inventory-alerts-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['item'],
                'fields' => [
                    'status' => [
                        'field' => 'is_active',
                        'transform' => function ($value) { return $value ? 'Active' : 'Resolved'; },
                    ],
                    'item' => ['field' => 'item.name', 'default' => '-'],
                    'qty_on_hand' => ['field' => 'item.quantity_on_hand', 'default' => '-'],
                    'reorder_level' => ['field' => 'item.reorder_level', 'default' => '-'],
                    'alert_type' => ['field' => 'alert_type', 'default' => '-'],
                    'triggered_at' => [
                        'field' => 'triggered_at',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                    'message' => ['field' => 'message', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'item.quantity_on_hand', 'label' => 'Qty on Hand'],
                    ['key' => 'item.reorder_level', 'label' => 'Reorder Level'],
                    ['key' => 'alert_type', 'label' => 'Alert Type'],
                    ['key' => 'triggered_at', 'label' => 'Triggered'],
                    ['key' => 'message', 'label' => 'Message'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'Inventory Alerts - Geraye Home Care Services',
                'document_title' => 'Inventory Alerts',
                'filename_prefix' => 'inventory-alerts',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['item'],
                'fields' => [
                    'status' => [
                        'field' => 'is_active',
                        'transform' => function ($value) { return $value ? 'Active' : 'Resolved'; },
                    ],
                    'item' => ['field' => 'item.name', 'default' => '-'],
                    'qty_on_hand' => ['field' => 'item.quantity_on_hand', 'default' => '-'],
                    'reorder_level' => ['field' => 'item.reorder_level', 'default' => '-'],
                    'alert_type' => ['field' => 'alert_type', 'default' => '-'],
                    'triggered_at' => [
                        'field' => 'triggered_at',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                    'message' => ['field' => 'message', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'item.quantity_on_hand', 'label' => 'Qty on Hand'],
                    ['key' => 'item.reorder_level', 'label' => 'Reorder Level'],
                    ['key' => 'alert_type', 'label' => 'Alert Type'],
                    ['key' => 'triggered_at', 'label' => 'Triggered'],
                    ['key' => 'message', 'label' => 'Message'],
                ],
            ],
        ];
    }

    /**
     * Get export configuration for InventoryTransaction model
     */
    public static function getInventoryTransactionConfig(): array
    {
        return [
            'searchable_fields' => ['transaction_type', 'from_location', 'to_location', 'item.name', 'performedBy.first_name', 'performedBy.last_name'],
            'sortable_fields' => ['transaction_type', 'quantity', 'from_location', 'to_location', 'performed_by_id', 'item_id', 'created_at'],
            'default_sort' => 'created_at',
            'select_fields' => [
                'item_id', 'transaction_type', 'quantity', 'from_location', 'to_location', 'performed_by_id', 'created_at',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Item', 'Type', 'Quantity', 'From', 'To', 'Performed By', 'Date',
                ],
                'fields' => [
                    'index',
                    'item.name',
                    'transaction_type',
                    'quantity',
                    'from_location',
                    'to_location',
                    [
                        'field' => 'performedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->performedBy->first_name ?? '';
                            $ln = $model->performedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : 'N/A';
                        },
                    ],
                    [
                        'field' => 'created_at',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                ],
                'with_relations' => ['item', 'performedBy'],
                'filename_prefix' => 'inventory-transactions',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Inventory Transactions - Geraye',
                'document_title' => 'Inventory Transactions',
                'filename_prefix' => 'inventory-transactions',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['item', 'performedBy'],
                'fields' => [
                    'item' => ['field' => 'item.name', 'default' => '-'],
                    'transaction_type' => 'transaction_type',
                    'quantity' => 'quantity',
                    'from_location' => ['field' => 'from_location', 'default' => '-'],
                    'to_location' => ['field' => 'to_location', 'default' => '-'],
                    'performed_by' => [
                        'field' => 'performedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->performedBy->first_name ?? '';
                            $ln = $model->performedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : 'N/A';
                        },
                    ],
                    'created_at' => [
                        'field' => 'created_at',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                ],
                'columns' => [
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'transaction_type', 'label' => 'Type'],
                    ['key' => 'quantity', 'label' => 'Qty'],
                    ['key' => 'from_location', 'label' => 'From'],
                    ['key' => 'to_location', 'label' => 'To'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'created_at', 'label' => 'Date'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Inventory Transactions (Current View) - Geraye',
                'document_title' => 'Inventory Transactions (Current View)',
                'filename_prefix' => 'inventory-transactions-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['item', 'performedBy'],
                'fields' => [
                    'item' => ['field' => 'item.name', 'default' => '-'],
                    'transaction_type' => 'transaction_type',
                    'quantity' => 'quantity',
                    'from_location' => ['field' => 'from_location', 'default' => '-'],
                    'to_location' => ['field' => 'to_location', 'default' => '-'],
                    'performed_by' => [
                        'field' => 'performedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->performedBy->first_name ?? '';
                            $ln = $model->performedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : 'N/A';
                        },
                    ],
                    'created_at' => [
                        'field' => 'created_at',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'transaction_type', 'label' => 'Type'],
                    ['key' => 'quantity', 'label' => 'Qty'],
                    ['key' => 'from_location', 'label' => 'From'],
                    ['key' => 'to_location', 'label' => 'To'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'created_at', 'label' => 'Date'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Inventory Transactions - Geraye',
                'document_title' => 'All Inventory Transactions',
                'filename_prefix' => 'inventory-transactions',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['item', 'performedBy'],
                'fields' => [
                    'item' => ['field' => 'item.name', 'default' => '-'],
                    'transaction_type' => 'transaction_type',
                    'quantity' => 'quantity',
                    'from_location' => ['field' => 'from_location', 'default' => '-'],
                    'to_location' => ['field' => 'to_location', 'default' => '-'],
                    'performed_by' => [
                        'field' => 'performedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->performedBy->first_name ?? '';
                            $ln = $model->performedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : 'N/A';
                        },
                    ],
                    'created_at' => [
                        'field' => 'created_at',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'transaction_type', 'label' => 'Type'],
                    ['key' => 'quantity', 'label' => 'Qty'],
                    ['key' => 'from_location', 'label' => 'From'],
                    ['key' => 'to_location', 'label' => 'To'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'created_at', 'label' => 'Date'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Inventory Transaction Record - Geraye',
                'document_title' => 'Inventory Transaction',
                'filename_prefix' => 'inventory-transaction',
                'with_relations' => ['item', 'performedBy'],
                'fields' => [
                    'Item' => ['field' => 'item.name', 'default' => '-'],
                    'Type' => 'transaction_type',
                    'Quantity' => 'quantity',
                    'From' => ['field' => 'from_location', 'default' => '-'],
                    'To' => ['field' => 'to_location', 'default' => '-'],
                    'Performed By' => [
                        'field' => 'performedBy.first_name',
                        'transform' => function ($value, $model) { $fn = $model->performedBy->first_name ?? ''; $ln = $model->performedBy->last_name ?? ''; $full = trim($fn . ' ' . $ln); return $full !== '' ? $full : 'N/A'; },
                    ],
                    'Date' => ['field' => 'created_at', 'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-'; }],
                ],
                'columns' => [
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'transaction_type', 'label' => 'Type'],
                    ['key' => 'quantity', 'label' => 'Quantity'],
                    ['key' => 'from_location', 'label' => 'From'],
                    ['key' => 'to_location', 'label' => 'To'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'created_at', 'label' => 'Date'],
                ],
            ],
        ];
    }

    

    /**
     * Get export configuration for Staff model
     */
    public static function getStaffConfig(): array
    {
        return [
            'searchable_fields' => ['first_name', 'last_name', 'email', 'position'],
            'sortable_fields' => ['first_name', 'last_name', 'email', 'position', 'department', 'status', 'hire_date', 'created_at'],
            'default_sort' => 'created_at',
            'select_fields' => [
                'first_name', 'last_name', 'email', 'phone', 'position',
                'department', 'status', 'hire_date',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Full Name', 'Email', 'Phone', 'Position', 'Department', 'Status', 'Hire Date',
                ],
                'fields' => [
                    'index',
                    'full_name' => [
                        'field' => 'first_name',
                        'transform' => function ($value, $model) {
                            return $model->first_name . ' ' . $model->last_name;
                        },
                    ],
                    'email', 'phone', 'position', 'department', 'status', 'hire_date',
                ],
                'filename_prefix' => 'staff',
            ],

            'pdf' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Staff Export - Geraye Home Care Services',
                'document_title' => 'All Staff Records',
                'filename_prefix' => 'staff',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'full_name' => [
                        'field' => 'first_name',
                        'transform' => function ($value, $model) {
                            return $model->first_name . ' ' . $model->last_name;
                        },
                    ],
                    'email' => ['field' => 'email', 'default' => '-'],
                    'phone' => ['field' => 'phone', 'default' => '-'],
                    'position' => ['field' => 'position', 'default' => '-'],
                    'department' => ['field' => 'department', 'default' => '-'],
                    'status' => 'status',
                    'hire_date' => [
                        'field' => 'hire_date',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'position', 'label' => 'Position'],
                    ['key' => 'department', 'label' => 'Department'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'hire_date', 'label' => 'Hire Date'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Staff List (Current View) - Geraye Home Care Services',
                'document_title' => 'Staff List (Current View)',
                'filename_prefix' => 'staff-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'full_name' => [
                        'field' => 'first_name',
                        'transform' => function ($value, $model) {
                            return $model->first_name . ' ' . $model->last_name;
                        },
                    ],
                    'email' => ['field' => 'email', 'default' => '-'],
                    'phone' => ['field' => 'phone', 'default' => '-'],
                    'position' => ['field' => 'position', 'default' => '-'],
                    'department' => ['field' => 'department', 'default' => '-'],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'position', 'label' => 'Position'],
                    ['key' => 'department', 'label' => 'Department'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Staff List - Geraye Home Care Services',
                'document_title' => 'All Staff Records',
                'filename_prefix' => 'staff',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'first_name',
                'fields' => [
                    'full_name' => [
                        'field' => 'first_name',
                        'transform' => function ($value, $model) {
                            return $model->first_name . ' ' . $model->last_name;
                        },
                    ],
                    'email' => ['field' => 'email', 'default' => '-'],
                    'phone' => ['field' => 'phone', 'default' => '-'],
                    'position' => ['field' => 'position', 'default' => '-'],
                    'department' => ['field' => 'department', 'default' => '-'],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'position', 'label' => 'Position'],
                    ['key' => 'department', 'label' => 'Department'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'single_record' => [
                'fields' => [
                    'Full Name' => [
                        'field' => 'first_name',
                        'transform' => function ($value, $model) {
                            return $model->first_name . ' ' . $model->last_name;
                        },
                    ],
                    'Email' => ['field' => 'email', 'default' => '-'],
                    'Phone' => ['field' => 'phone', 'default' => '-'],
                    'Position' => ['field' => 'position', 'default' => '-'],
                    'Department' => ['field' => 'department', 'default' => '-'],
                    'Status' => 'status',
                    'Hire Date' => [
                        'field' => 'hire_date',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Get export configuration for CaregiverAssignment model
     */
    public static function getCaregiverAssignmentConfig(): array
    {
        return [
            'searchable_fields' => ['patient.full_name', 'staff.first_name', 'staff.last_name'],
            'sortable_fields' => ['shift_start', 'shift_end', 'status', 'created_at'],
            'default_sort' => 'created_at',
            'select_fields' => [
                'patient_id', 'staff_id', 'shift_start', 'shift_end', 'status',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Patient Name', 'Staff Name', 'Shift Start', 'Shift End', 'Status',
                ],
                'fields' => [
                    'index',
                    'patient.full_name',
                    'staff_full_name',
                    'shift_start',
                    'shift_end',
                    'status',
                ],
                'with_relations' => ['staff', 'patient'],
                'filename_prefix' => 'assignments',
            ],

            'pdf' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'All Caregiver Assignments - Geraye',
                'document_title' => 'Caregiver Assignment Records Export',
                'filename_prefix' => 'assignments-all',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['staff', 'patient'],
                'fields' => [
                    'patient_name' => ['field' => 'patient.full_name', 'default' => 'N/A'],
                    'staff_member' => [
                        'field' => 'staff.first_name',
                        'transform' => function ($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        },
                    ],
                    'shift_start' => [
                        'field' => 'shift_start',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        },
                    ],
                    'shift_end' => [
                        'field' => 'shift_end',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        },
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'patient.full_name', 'label' => 'Patient Name'],
                    ['key' => 'staff_full_name', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Caregiver Assignments (Current View) - Geraye',
                'document_title' => 'Caregiver Assignments (Current View)',
                'filename_prefix' => 'assignments-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['staff', 'patient'],
                'fields' => [
                    'patient_name' => ['field' => 'patient.full_name', 'default' => 'N/A'],
                    'staff_member' => [
                        'field' => 'staff.first_name',
                        'transform' => function ($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        },
                    ],
                    'shift_start' => [
                        'field' => 'shift_start',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M j, Y g:i a') : 'N/A';
                        },
                    ],
                    'shift_end' => [
                        'field' => 'shift_end',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M j, Y g:i a') : 'N/A';
                        },
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'patient.full_name', 'label' => 'Patient Name'],
                    ['key' => 'staff_full_name', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'All Caregiver Assignments - Geraye',
                'document_title' => 'Caregiver Assignment Records Export',
                'filename_prefix' => 'assignments-all',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['staff', 'patient'],
                'fields' => [
                    'patient_name' => ['field' => 'patient.full_name', 'default' => 'N/A'],
                    'staff_member' => [
                        'field' => 'staff.first_name',
                        'transform' => function ($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        },
                    ],
                    'shift_start' => [
                        'field' => 'shift_start',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        },
                    ],
                    'shift_end' => [
                        'field' => 'shift_end',
                        'transform' => function ($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        },
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'patient.full_name', 'label' => 'Patient Name'],
                    ['key' => 'staff_full_name', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Caregiver Assignment Record - Geraye',
                'document_title' => 'Caregiver Assignment Record',
                'filename_prefix' => 'assignment-record',
                'with_relations' => ['staff', 'patient'],
                'fields' => [
                    'Patient Name' => ['field' => 'patient.full_name', 'default' => 'N/A'],
                    'Staff Member' => ['field' => 'staff.first_name', 'transform' => function ($value, $model) {return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');}],
                    'Shift Start' => ['field' => 'shift_start', 'transform' => function ($value, $model) {return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';}],
                    'Shift End' => ['field' => 'shift_end', 'transform' => function ($value, $model) {return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';}],
                    'Status' => 'status',
                ],
                'columns' => [
                    ['key' => 'patient.full_name', 'label' => 'Patient Name'],
                    ['key' => 'staff_full_name', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],
        ];
    }

    /**
     * Get export configuration for Service model
     */
    public static function getServiceConfig(): array
    {
        return [
            'searchable_fields' => ['name', 'description'],
            'sortable_fields' => ['name', 'category', 'price', 'duration', 'is_active', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'services',
            'select_fields' => [
                'name', 'description', 'category', 'price', 'duration', 'is_active',
            ],

            'csv' => [
                'headers' => [
                    'Name', 'Description', 'Category', 'Price', 'Duration', 'Active',
                ],
                'fields' => [
                    'name', 'description', 'category', 'price', 'duration', 'is_active',
                ],
                'filename_prefix' => 'services',
            ],

            'pdf' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Services Export - Geraye Home Care Services',
                'document_title' => 'Services Records Export',
                'filename_prefix' => 'services',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'name' => 'name',
                    'description' => ['field' => 'description', 'default' => '-'],
                    'category' => 'category',
                    'price' => [
                        'field' => 'price',
                        'transform' => function ($value) {
                            return '$' . number_format($value, 2);
                        },
                    ],
                    'duration' => [
                        'field' => 'duration',
                        'transform' => function ($value) {
                            return $value . ' minutes';
                        },
                    ],
                    'is_active' => [
                        'field' => 'is_active',
                        'transform' => function ($value) {
                            return $value ? 'Active' : 'Inactive';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => 'Service Name'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'category', 'label' => 'Category'],
                    ['key' => 'price', 'label' => 'Price'],
                    ['key' => 'duration', 'label' => 'Duration'],
                    ['key' => 'is_active', 'label' => 'Status'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Services List (Current View) - Geraye Home Care Services',
                'document_title' => 'Services List (Current View)',
                'filename_prefix' => 'services-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'name' => 'name',
                    'description' => ['field' => 'description', 'default' => '-'],
                    'category' => 'category',
                    'price' => [
                        'field' => 'price',
                        'transform' => function ($value) {
                            return '$' . number_format($value, 2);
                        },
                    ],
                    'duration' => [
                        'field' => 'duration',
                        'transform' => function ($value) {
                            return $value . ' min';
                        },
                    ],
                    'is_active' => [
                        'field' => 'is_active',
                        'transform' => function ($value) {
                            return $value ? 'Active' : 'Inactive';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Service Name'],
                    ['key' => 'category', 'label' => 'Category'],
                    ['key' => 'price', 'label' => 'Price'],
                    ['key' => 'duration', 'label' => 'Duration'],
                    ['key' => 'is_active', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'All Services - Geraye Home Care Services',
                'document_title' => 'All Services Records',
                'filename_prefix' => 'services-all',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'name',
                'fields' => [
                    'name' => 'name',
                    'description' => ['field' => 'description', 'default' => '-'],
                    'category' => 'category',
                    'price' => [
                        'field' => 'price',
                        'transform' => function ($value) {
                            return '$' . number_format($value, 2);
                        },
                    ],
                    'duration' => [
                        'field' => 'duration',
                        'transform' => function ($value) {
                            return $value . ' min';
                        },
                    ],
                    'is_active' => [
                        'field' => 'is_active',
                        'transform' => function ($value) {
                            return $value ? 'Active' : 'Inactive';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Service Name'],
                    ['key' => 'category', 'label' => 'Category'],
                    ['key' => 'price', 'label' => 'Price'],
                    ['key' => 'duration', 'label' => 'Duration'],
                    ['key' => 'is_active', 'label' => 'Status'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Service Record - Geraye Home Care Services',
                'document_title' => 'Service Record',
                'filename_prefix' => 'service-record',
                'fields' => [
                    'Service Name' => 'name',
                    'Description' => ['field' => 'description', 'default' => '-'],
                    'Category' => 'category',
                    'Price' => [
                        'field' => 'price',
                        'transform' => function ($value) {
                            return '$' . number_format($value, 2);
                        },
                    ],
                    'Duration' => [
                        'field' => 'duration',
                        'transform' => function ($value) {
                            return $value . ' minutes';
                        },
                    ],
                    'Status' => [
                        'field' => 'is_active',
                        'transform' => function ($value) {
                            return $value ? 'Active' : 'Inactive';
                        },
                    ],
                    'Created Date' => [
                        'field' => 'created_at',
                        'transform' => function ($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-';
                        },
                    ],
                    'Last Updated' => [
                        'field' => 'updated_at',
                        'transform' => function ($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => 'Service Name'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'category', 'label' => 'Category'],
                    ['key' => 'price', 'label' => 'Price'],
                    ['key' => 'duration', 'label' => 'Duration'],
                    ['key' => 'is_active', 'label' => 'Status'],
                    ['key' => 'created_at', 'label' => 'Created Date'],
                    ['key' => 'updated_at', 'label' => 'Last Updated'],
                ],
            ],
        ];
    }

    /**
     * Get export configuration for Invoice model
     */
    public static function getInvoiceConfig(): array
    {
        return [
            'searchable_fields' => ['invoice_number', 'patient.full_name', 'status'],
            'sortable_fields' => ['invoice_number', 'invoice_date', 'due_date', 'grand_total', 'status', 'created_at'],
            'default_sort' => 'created_at',
            'select_fields' => [
                'invoice_number', 'patient_id', 'invoice_date', 'due_date', 'subtotal', 'tax_amount', 'grand_total', 'status', 'paid_at'
            ],

            'csv' => [
                'headers' => [
                    '#', 'Invoice #', 'Patient', 'Invoice Date', 'Due Date', 'Subtotal', 'Tax', 'Grand Total', 'Status', 'Paid At',
                ],
                'fields' => [
                    'index',
                    'invoice_number',
                    'patient.full_name',
                    [
                        'field' => 'invoice_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'due_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'subtotal',
                        'transform' => function ($value) { return number_format((float)$value, 2); },
                    ],
                    [
                        'field' => 'tax_amount',
                        'transform' => function ($value) { return number_format((float)$value, 2); },
                    ],
                    [
                        'field' => 'grand_total',
                        'transform' => function ($value) { return number_format((float)$value, 2); },
                    ],
                    'status',
                    [
                        'field' => 'paid_at',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                ],
                'with_relations' => ['patient'],
                'filename_prefix' => 'invoices',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Invoices - Geraye Home Care Services',
                'document_title' => 'Invoices',
                'filename_prefix' => 'invoices',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['patient'],
                'fields' => [
                    'invoice_number' => 'invoice_number',
                    'patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'invoice_date' => [ 'field' => 'invoice_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'due_date' => [ 'field' => 'due_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'subtotal' => [ 'field' => 'subtotal', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'tax_amount' => [ 'field' => 'tax_amount', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'grand_total' => [ 'field' => 'grand_total', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'status' => 'status',
                    'paid_at' => [ 'field' => 'paid_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d H:i') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'patient.full_name', 'label' => 'Patient'],
                    ['key' => 'invoice_date', 'label' => 'Invoice Date'],
                    ['key' => 'due_date', 'label' => 'Due Date'],
                    ['key' => 'subtotal', 'label' => 'Subtotal'],
                    ['key' => 'tax_amount', 'label' => 'Tax'],
                    ['key' => 'grand_total', 'label' => 'Grand Total'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'paid_at', 'label' => 'Paid At'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Invoices (Current View) - Geraye Home Care Services',
                'document_title' => 'Invoices (Current View)',
                'filename_prefix' => 'invoices-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['patient'],
                'fields' => [
                    'invoice_number' => 'invoice_number',
                    'patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'invoice_date' => [ 'field' => 'invoice_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'due_date' => [ 'field' => 'due_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'grand_total' => [ 'field' => 'grand_total', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'patient.full_name', 'label' => 'Patient'],
                    ['key' => 'invoice_date', 'label' => 'Invoice Date'],
                    ['key' => 'due_date', 'label' => 'Due Date'],
                    ['key' => 'grand_total', 'label' => 'Grand Total'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Invoices - Geraye Home Care Services',
                'document_title' => 'Invoices',
                'filename_prefix' => 'invoices',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['patient'],
                'fields' => [
                    'invoice_number' => 'invoice_number',
                    'patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'invoice_date' => [ 'field' => 'invoice_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'due_date' => [ 'field' => 'due_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'grand_total' => [ 'field' => 'grand_total', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'patient.full_name', 'label' => 'Patient'],
                    ['key' => 'invoice_date', 'label' => 'Invoice Date'],
                    ['key' => 'due_date', 'label' => 'Due Date'],
                    ['key' => 'grand_total', 'label' => 'Grand Total'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf.invoice',
                'title' => 'Invoice Detail - Geraye Home Care Services',
                'document_title' => 'Invoice Detail',
                'filename_prefix' => 'invoice',
                'with_relations' => ['patient', 'items.visitService'],
                'fields' => [
                    'Invoice #' => 'invoice_number',
                    'Patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'Invoice Date' => [ 'field' => 'invoice_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Due Date' => [ 'field' => 'due_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Subtotal' => [ 'field' => 'subtotal', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'Tax' => [ 'field' => 'tax_amount', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'Grand Total' => [ 'field' => 'grand_total', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'Status' => 'status',
                    'Paid At' => [ 'field' => 'paid_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'patient.full_name', 'label' => 'Patient'],
                    ['key' => 'invoice_date', 'label' => 'Invoice Date'],
                    ['key' => 'due_date', 'label' => 'Due Date'],
                    ['key' => 'subtotal', 'label' => 'Subtotal'],
                    ['key' => 'tax_amount', 'label' => 'Tax'],
                    ['key' => 'grand_total', 'label' => 'Grand Total'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'paid_at', 'label' => 'Paid At'],
                ],
            ],
        ];
    }

    /**
     * Get export configuration for MarketingCampaign model
     */
    public static function getMarketingCampaignConfig(): array
    {
        return [
            'searchable_fields' => ['campaign_name', 'campaign_code', 'utm_campaign'],
            'sortable_fields' => ['campaign_name', 'start_date', 'end_date', 'status', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'marketing-campaigns',
            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Marketing Campaigns (Current View) - Geraye',
                'document_title' => 'Marketing Campaigns (Current View)',
                'filename_prefix' => 'marketing-campaigns-current',
                'orientation' => 'landscape',
                'hide_footer' => true,
                'include_index' => true,
                'with_relations' => ['platform', 'responsibleStaff', 'createdByStaff'],
                'fields' => [
                    'campaign_name' => 'campaign_name',
                    'campaign_code' => ['field' => 'campaign_code', 'default' => '-'],
                    'platform' => ['field' => 'platform.name', 'default' => '-'],
                    'campaign_type' => ['field' => 'campaign_type', 'default' => '-'],
                    'status' => ['field' => 'status', 'default' => '-'],
                    'urgency' => ['field' => 'urgency', 'default' => '-'],
                    'responsible_staff' => [
                        'field' => 'responsibleStaff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->responsibleStaff->first_name ?? '';
                            $ln = $model->responsibleStaff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                    'start_date' => [
                        'field' => 'start_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'end_date' => [
                        'field' => 'end_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'campaign_name', 'label' => 'Campaign Name'],
                    ['key' => 'campaign_code', 'label' => 'Campaign Code'],
                    ['key' => 'platform.name', 'label' => 'Platform'],
                    ['key' => 'campaign_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'urgency', 'label' => 'Urgency'],
                    ['key' => 'responsibleStaff.full_name', 'label' => 'Responsible Staff'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                ],
            ],
            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Marketing Campaigns - Geraye',
                'document_title' => 'All Marketing Campaigns',
                'filename_prefix' => 'marketing-campaigns',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['platform', 'responsibleStaff', 'createdByStaff'],
                'fields' => [
                    'campaign_name' => 'campaign_name',
                    'campaign_code' => ['field' => 'campaign_code', 'default' => '-'],
                    'platform' => ['field' => 'platform.name', 'default' => '-'],
                    'campaign_type' => ['field' => 'campaign_type', 'default' => '-'],
                    'status' => ['field' => 'status', 'default' => '-'],
                    'urgency' => ['field' => 'urgency', 'default' => '-'],
                    'responsible_staff' => [
                        'field' => 'responsibleStaff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->responsibleStaff->first_name ?? '';
                            $ln = $model->responsibleStaff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                    'start_date' => [
                        'field' => 'start_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'end_date' => [
                        'field' => 'end_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'campaign_name', 'label' => 'Campaign Name'],
                    ['key' => 'campaign_code', 'label' => 'Campaign Code'],
                    ['key' => 'platform.name', 'label' => 'Platform'],
                    ['key' => 'campaign_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'urgency', 'label' => 'Urgency'],
                    ['key' => 'responsibleStaff.full_name', 'label' => 'Responsible Staff'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                ],
            ],
            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Marketing Campaign Detail - Geraye',
                'document_title' => 'Marketing Campaign Detail',
                'filename_prefix' => 'marketing-campaign',
                'with_relations' => ['platform', 'responsibleStaff', 'createdByStaff'],
                'fields' => [
                    'Campaign Name' => 'campaign_name',
                    'Campaign Code' => ['field' => 'campaign_code', 'default' => '-'],
                    'Platform' => ['field' => 'platform.name', 'default' => '-'],
                    'Type' => ['field' => 'campaign_type', 'default' => '-'],
                    'Status' => ['field' => 'status', 'default' => '-'],
                    'Urgency' => ['field' => 'urgency', 'default' => '-'],
                    'Responsible Staff' => [
                        'field' => 'responsibleStaff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->responsibleStaff->first_name ?? '';
                            $ln = $model->responsibleStaff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                    'Start Date' => [
                        'field' => 'start_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('F j, Y') : '-'; },
                    ],
                    'End Date' => [
                        'field' => 'end_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('F j, Y') : '-'; },
                    ],
                    'Created By' => [
                        'field' => 'createdByStaff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->createdByStaff->first_name ?? '';
                            $ln = $model->createdByStaff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'campaign_name', 'label' => 'Campaign Name'],
                    ['key' => 'campaign_code', 'label' => 'Campaign Code'],
                    ['key' => 'platform.name', 'label' => 'Platform'],
                    ['key' => 'campaign_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'urgency', 'label' => 'Urgency'],
                    ['key' => 'responsibleStaff.full_name', 'label' => 'Responsible Staff'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                    ['key' => 'createdByStaff.full_name', 'label' => 'Created By'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export configuration for InventoryMaintenanceRecord model
     */
    public static function getInventoryMaintenanceRecordConfig(): array
    {
        return [
            'searchable_fields' => ['description', 'item.name', 'performedByStaff.first_name', 'performedByStaff.last_name'],
            'sortable_fields' => ['item_id', 'scheduled_date', 'actual_date', 'performed_by_staff_id', 'cost', 'next_due_date', 'status', 'created_at'],
            'default_sort' => 'created_at',
            'select_fields' => [
                'item_id', 'scheduled_date', 'actual_date', 'performed_by_staff_id', 'cost', 'description', 'next_due_date', 'status',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Item', 'Scheduled Date', 'Actual Date', 'Performed By', 'Cost', 'Description', 'Next Due Date', 'Status',
                ],
                'fields' => [
                    'index',
                    'item.name',
                    [
                        'field' => 'scheduled_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'actual_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'performedByStaff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->performedByStaff->first_name ?? '';
                            $ln = $model->performedByStaff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : 'N/A';
                        },
                    ],
                    [
                        'field' => 'cost',
                        'transform' => function ($value) { return number_format((float)$value, 2); },
                    ],
                    'description',
                    [
                        'field' => 'next_due_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'status',
                ],
                'with_relations' => ['item', 'performedByStaff'],
                'filename_prefix' => 'inventory-maintenance-records',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Inventory Maintenance Records - Geraye',
                'document_title' => 'Inventory Maintenance Records',
                'filename_prefix' => 'inventory-maintenance-records',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['item', 'performedByStaff'],
                'fields' => [
                    'item' => ['field' => 'item.name', 'default' => '-'],
                    'scheduled_date' => [ 'field' => 'scheduled_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'actual_date' => [ 'field' => 'actual_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'performed_by' => [
                        'field' => 'performedByStaff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->performedByStaff->first_name ?? '';
                            $ln = $model->performedByStaff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : 'N/A';
                        },
                    ],
                    'cost' => [ 'field' => 'cost', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'description' => 'description',
                    'next_due_date' => [ 'field' => 'next_due_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'scheduled_date', 'label' => 'Scheduled Date'],
                    ['key' => 'actual_date', 'label' => 'Actual Date'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'cost', 'label' => 'Cost'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'next_due_date', 'label' => 'Next Due Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Inventory Maintenance Records (Current View) - Geraye',
                'document_title' => 'Inventory Maintenance Records (Current View)',
                'filename_prefix' => 'inventory-maintenance-records-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['item', 'performedByStaff'],
                'fields' => [
                    'item' => ['field' => 'item.name', 'default' => '-'],
                    'scheduled_date' => [ 'field' => 'scheduled_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'actual_date' => [ 'field' => 'actual_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'performed_by' => [
                        'field' => 'performedByStaff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->performedByStaff->first_name ?? '';
                            $ln = $model->performedByStaff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : 'N/A';
                        },
                    ],
                    'cost' => [ 'field' => 'cost', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'scheduled_date', 'label' => 'Scheduled Date'],
                    ['key' => 'actual_date', 'label' => 'Actual Date'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'cost', 'label' => 'Cost'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Inventory Maintenance Records - Geraye',
                'document_title' => 'All Inventory Maintenance Records',
                'filename_prefix' => 'inventory-maintenance-records',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['item', 'performedByStaff'],
                'fields' => [
                    'item' => ['field' => 'item.name', 'default' => '-'],
                    'scheduled_date' => [ 'field' => 'scheduled_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'actual_date' => [ 'field' => 'actual_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'performed_by' => [
                        'field' => 'performedByStaff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->performedByStaff->first_name ?? '';
                            $ln = $model->performedByStaff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : 'N/A';
                        },
                    ],
                    'cost' => [ 'field' => 'cost', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'scheduled_date', 'label' => 'Scheduled Date'],
                    ['key' => 'actual_date', 'label' => 'Actual Date'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'cost', 'label' => 'Cost'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Inventory Maintenance Record Detail - Geraye',
                'document_title' => 'Inventory Maintenance Record',
                'filename_prefix' => 'inventory-maintenance-record',
                'with_relations' => ['item', 'performedByStaff'],
                'fields' => [
                    'Item' => ['field' => 'item.name', 'default' => '-'],
                    'Scheduled Date' => [ 'field' => 'scheduled_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Actual Date' => [ 'field' => 'actual_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Performed By' => [
                        'field' => 'performedByStaff.first_name',
                        'transform' => function ($value, $model) { $fn = $model->performedByStaff->first_name ?? ''; $ln = $model->performedByStaff->last_name ?? ''; $full = trim($fn . ' ' . $ln); return $full !== '' ? $full : 'N/A'; },
                    ],
                    'Cost' => [ 'field' => 'cost', 'transform' => function ($v) { return number_format((float)$v, 2); } ],
                    'Description' => 'description',
                    'Next Due Date' => [ 'field' => 'next_due_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Status' => 'status',
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'item.name', 'label' => 'Item'],
                    ['key' => 'scheduled_date', 'label' => 'Scheduled Date'],
                    ['key' => 'actual_date', 'label' => 'Actual Date'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'cost', 'label' => 'Cost'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'next_due_date', 'label' => 'Next Due Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }
}
