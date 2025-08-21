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
     * Get export configuration for Partner model
     */
    public static function getPartnerConfig(): array
    {
        return [
            'searchable_fields' => ['name', 'type', 'engagement_status'],
            'sortable_fields' => ['name', 'type', 'engagement_status', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'partners',
            'select_fields' => [
                'name', 'type', 'contact_person', 'email', 'phone', 'address', 'engagement_status', 'account_manager_id', 'notes',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Name', 'Type', 'Contact Person', 'Email', 'Phone', 'Address', 'Engagement Status', 'Account Manager', 'Notes',
                ],
                'fields' => [
                    'index',
                    'name',
                    'type',
                    'contact_person',
                    'email',
                    'phone',
                    'address',
                    'engagement_status',
                    [
                        'field' => 'accountManager.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->accountManager->first_name ?? '';
                            $ln = $model->accountManager->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                    'notes',
                ],
                'with_relations' => ['accountManager'],
                'filename_prefix' => 'partners',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Partners Export - Geraye Home Care Services',
                'document_title' => 'Partners Export',
                'filename_prefix' => 'partners',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['accountManager'],
                'fields' => [
                    'name' => 'name',
                    'type' => 'type',
                    'contact_person' => ['field' => 'contact_person', 'default' => '-'],
                    'email' => ['field' => 'email', 'default' => '-'],
                    'phone' => ['field' => 'phone', 'default' => '-'],
                    'address' => ['field' => 'address', 'default' => '-'],
                    'engagement_status' => 'engagement_status',
                    'account_manager' => [
                        'field' => 'accountManager.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->accountManager->first_name ?? '';
                            $ln = $model->accountManager->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                    'notes' => ['field' => 'notes', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'type', 'label' => 'Type'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'address', 'label' => 'Address'],
                    ['key' => 'engagement_status', 'label' => 'Engagement Status'],
                    ['key' => 'account_manager.full_name', 'label' => 'Account Manager'],
                    ['key' => 'notes', 'label' => 'Notes'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Partners List (Current View) - Geraye Home Care Services',
                'document_title' => 'Partners List (Current View)',
                'filename_prefix' => 'partners-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['accountManager'],
                'fields' => [
                    'name' => 'name',
                    'type' => 'type',
                    'contact_person' => ['field' => 'contact_person', 'default' => '-'],
                    'email' => ['field' => 'email', 'default' => '-'],
                    'phone' => ['field' => 'phone', 'default' => '-'],
                    'engagement_status' => 'engagement_status',
                    'account_manager' => [
                        'field' => 'accountManager.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->accountManager->first_name ?? '';
                            $ln = $model->accountManager->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'type', 'label' => 'Type'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'engagement_status', 'label' => 'Engagement Status'],
                    ['key' => 'account_manager.full_name', 'label' => 'Account Manager'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Partners - Geraye Home Care Services',
                'document_title' => 'All Partners',
                'filename_prefix' => 'partners',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'name',
                'with_relations' => ['accountManager'],
                'fields' => [
                    'name' => 'name',
                    'type' => 'type',
                    'contact_person' => ['field' => 'contact_person', 'default' => '-'],
                    'email' => ['field' => 'email', 'default' => '-'],
                    'phone' => ['field' => 'phone', 'default' => '-'],
                    'address' => ['field' => 'address', 'default' => '-'],
                    'engagement_status' => 'engagement_status',
                    'account_manager' => [
                        'field' => 'accountManager.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->accountManager->first_name ?? '';
                            $ln = $model->accountManager->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                    'notes' => ['field' => 'notes', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'type', 'label' => 'Type'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'address', 'label' => 'Address'],
                    ['key' => 'engagement_status', 'label' => 'Engagement Status'],
                    ['key' => 'account_manager.full_name', 'label' => 'Account Manager'],
                    ['key' => 'notes', 'label' => 'Notes'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Record - Geraye Home Care Services',
                'document_title' => 'Partner Record',
                'filename_prefix' => 'partner-record',
                'with_relations' => ['accountManager'],
                'fields' => [
                    'Name' => 'name',
                    'Type' => 'type',
                    'Contact Person' => ['field' => 'contact_person', 'default' => '-'],
                    'Email' => ['field' => 'email', 'default' => '-'],
                    'Phone' => ['field' => 'phone', 'default' => '-'],
                    'Address' => ['field' => 'address', 'default' => '-'],
                    'Engagement Status' => 'engagement_status',
                    'Account Manager' => ['field' => 'accountManager.full_name', 'default' => '-'],
                    'Notes' => ['field' => 'notes', 'default' => '-'],
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'type', 'label' => 'Type'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'address', 'label' => 'Address'],
                    ['key' => 'engagement_status', 'label' => 'Engagement Status'],
                    ['key' => 'account_manager.full_name', 'label' => 'Account Manager'],
                    ['key' => 'notes', 'label' => 'Notes'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for PartnerAgreement model
     */
    public static function getPartnerAgreementConfig(): array
    {
        return [
            'searchable_fields' => ['agreement_title', 'agreement_type', 'status', 'partner.name', 'signedBy.first_name', 'signedBy.last_name'],
            'sortable_fields' => ['agreement_title', 'agreement_type', 'status', 'start_date', 'end_date', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'partner-agreements',
            'select_fields' => [
                'agreement_title', 'agreement_type', 'status', 'start_date', 'end_date', 'priority_service_level', 'commission_type', 'commission_rate', 'terms_document_path',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Title', 'Type', 'Status', 'Start Date', 'End Date', 'Partner', 'Signed By',
                ],
                'fields' => [
                    'index',
                    'agreement_title',
                    'agreement_type',
                    'status',
                    [
                        'field' => 'start_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'end_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'partner.name',
                        'transform' => function ($value, $model) {
                            return $model->partner->name ?? '-';
                        },
                    ],
                    [
                        'field' => 'signedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->signedBy->first_name ?? '';
                            $ln = $model->signedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                ],
                'with_relations' => ['partner', 'signedBy'],
                'filename_prefix' => 'partner-agreements',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Agreements Export - Geraye Home Care Services',
                'document_title' => 'Partner Agreements Export',
                'filename_prefix' => 'partner-agreements',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['partner', 'signedBy'],
                'fields' => [
                    'agreement_title' => 'agreement_title',
                    'agreement_type' => 'agreement_type',
                    'status' => 'status',
                    'start_date' => ['field' => 'start_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'end_date' => ['field' => 'end_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'signed_by' => [
                        'field' => 'signedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->signedBy->first_name ?? '';
                            $ln = $model->signedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'agreement_title', 'label' => 'Title'],
                    ['key' => 'agreement_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'signedBy.full_name', 'label' => 'Signed By'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Agreements List (Current View) - Geraye Home Care Services',
                'document_title' => 'Partner Agreements List (Current View)',
                'filename_prefix' => 'partner-agreements-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['partner', 'signedBy'],
                'fields' => [
                    'agreement_title' => 'agreement_title',
                    'agreement_type' => 'agreement_type',
                    'status' => 'status',
                    'start_date' => ['field' => 'start_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'end_date' => ['field' => 'end_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'signed_by' => [
                        'field' => 'signedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->signedBy->first_name ?? '';
                            $ln = $model->signedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'agreement_title', 'label' => 'Title'],
                    ['key' => 'agreement_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'signedBy.full_name', 'label' => 'Signed By'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Partner Agreements - Geraye Home Care Services',
                'document_title' => 'All Partner Agreements',
                'filename_prefix' => 'partner-agreements',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'agreement_title',
                'with_relations' => ['partner', 'signedBy'],
                'fields' => [
                    'agreement_title' => 'agreement_title',
                    'agreement_type' => 'agreement_type',
                    'status' => 'status',
                    'start_date' => ['field' => 'start_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'end_date' => ['field' => 'end_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'signed_by' => [
                        'field' => 'signedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->signedBy->first_name ?? '';
                            $ln = $model->signedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'agreement_title', 'label' => 'Title'],
                    ['key' => 'agreement_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'signedBy.full_name', 'label' => 'Signed By'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Agreement Record - Geraye Home Care Services',
                'document_title' => 'Partner Agreement Record',
                'filename_prefix' => 'partner-agreement-record',
                'with_relations' => ['partner', 'signedBy'],
                'fields' => [
                    'Title' => 'agreement_title',
                    'Type' => 'agreement_type',
                    'Status' => 'status',
                    'Start Date' => ['field' => 'start_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'End Date' => ['field' => 'end_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Priority Service Level' => ['field' => 'priority_service_level', 'default' => '-'],
                    'Commission Type' => ['field' => 'commission_type', 'default' => '-'],
                    'Commission Rate' => ['field' => 'commission_rate', 'default' => '-'],
                    'Terms Document Path' => ['field' => 'terms_document_path', 'default' => '-'],
                    'Partner' => ['field' => 'partner.name', 'default' => '-'],
                    'Signed By' => ['field' => 'signedBy.full_name', 'default' => '-'],
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'agreement_title', 'label' => 'Title'],
                    ['key' => 'agreement_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                    ['key' => 'priority_service_level', 'label' => 'Priority Service Level'],
                    ['key' => 'commission_type', 'label' => 'Commission Type'],
                    ['key' => 'commission_rate', 'label' => 'Commission Rate'],
                    ['key' => 'terms_document_path', 'label' => 'Terms Document Path'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'signedBy.full_name', 'label' => 'Signed By'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for Referral model
     */
    public static function getReferralConfig(): array
    {
        return [
            'searchable_fields' => ['status', 'referral_date', 'partner.name', 'patient.full_name'],
            'sortable_fields' => ['status', 'referral_date', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'referrals',
            'select_fields' => [
                'partner_id', 'agreement_id', 'referred_patient_id', 'referral_date', 'status', 'notes',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Partner', 'Agreement', 'Referred Patient', 'Referral Date', 'Status', 'Notes',
                ],
                'fields' => [
                    'index',
                    [
                        'field' => 'partner.name',
                        'transform' => function ($value, $model) {
                            return $model->partner->name ?? '-';
                        },
                    ],
                    [
                        'field' => 'agreement.agreement_title',
                        'transform' => function ($value, $model) {
                            return $model->agreement->agreement_title ?? '-';
                        },
                    ],
                    [
                        'field' => 'patient.full_name',
                        'transform' => function ($value, $model) {
                            return $model->patient->full_name ?? '-';
                        },
                    ],
                    [
                        'field' => 'referral_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'status',
                    'notes',
                ],
                'with_relations' => ['partner', 'agreement', 'patient'],
                'filename_prefix' => 'referrals',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Referrals Export - Geraye Home Care Services',
                'document_title' => 'Referrals Export',
                'filename_prefix' => 'referrals',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['partner', 'agreement', 'patient'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'referral_date' => ['field' => 'referral_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                    'notes' => ['field' => 'notes', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'patient.full_name', 'label' => 'Referred Patient'],
                    ['key' => 'referral_date', 'label' => 'Referral Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'notes', 'label' => 'Notes'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Referrals List (Current View) - Geraye Home Care Services',
                'document_title' => 'Referrals List (Current View)',
                'filename_prefix' => 'referrals-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['partner', 'agreement', 'patient'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'referral_date' => ['field' => 'referral_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'patient.full_name', 'label' => 'Referred Patient'],
                    ['key' => 'referral_date', 'label' => 'Referral Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Referrals - Geraye Home Care Services',
                'document_title' => 'All Referrals',
                'filename_prefix' => 'referrals',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'referral_date',
                'with_relations' => ['partner', 'agreement', 'patient'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'referral_date' => ['field' => 'referral_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                    'notes' => ['field' => 'notes', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'patient.full_name', 'label' => 'Referred Patient'],
                    ['key' => 'referral_date', 'label' => 'Referral Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'notes', 'label' => 'Notes'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Referral Record - Geraye Home Care Services',
                'document_title' => 'Referral Record',
                'filename_prefix' => 'referral-record',
                'with_relations' => ['partner', 'agreement', 'patient'],
                'fields' => [
                    'Partner' => ['field' => 'partner.name', 'default' => '-'],
                    'Agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'Referred Patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'Referral Date' => ['field' => 'referral_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Status' => 'status',
                    'Notes' => ['field' => 'notes', 'default' => '-'],
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'patient.full_name', 'label' => 'Referred Patient'],
                    ['key' => 'referral_date', 'label' => 'Referral Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'notes', 'label' => 'Notes'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for PartnerCommission model
     */
    public static function getPartnerCommissionConfig(): array
    {
        return [
            'searchable_fields' => ['status', 'calculation_date', 'agreement.agreement_title', 'referral.referral_date', 'invoice.invoice_number'],
            'sortable_fields' => ['status', 'calculation_date', 'commission_amount', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'partner-commissions',
            'select_fields' => [
                'agreement_id', 'referral_id', 'invoice_id', 'commission_amount', 'calculation_date', 'payout_date', 'status',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Agreement', 'Referral Date', 'Invoice #', 'Amount', 'Calculation Date', 'Payout Date', 'Status',
                ],
                'fields' => [
                    'index',
                    [
                        'field' => 'agreement.agreement_title',
                        'transform' => function ($value, $model) {
                            return $model->agreement->agreement_title ?? '-';
                        },
                    ],
                    [
                        'field' => 'referral.referral_date',
                        'transform' => function ($value, $model) {
                            return $model->referral->referral_date ?? '-';
                        },
                    ],
                    [
                        'field' => 'invoice.invoice_number',
                        'transform' => function ($value, $model) {
                            return $model->invoice->invoice_number ?? '-';
                        },
                    ],
                    'commission_amount',
                    [
                        'field' => 'calculation_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'payout_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'status',
                ],
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'filename_prefix' => 'partner-commissions',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Commissions Export - Geraye Home Care Services',
                'document_title' => 'Partner Commissions Export',
                'filename_prefix' => 'partner-commissions',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'fields' => [
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'referral' => ['field' => 'referral.referral_date', 'default' => '-'],
                    'invoice' => ['field' => 'invoice.invoice_number', 'default' => '-'],
                    'commission_amount' => 'commission_amount',
                    'calculation_date' => ['field' => 'calculation_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'payout_date' => ['field' => 'payout_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'referral.referral_date', 'label' => 'Referral Date'],
                    ['key' => 'invoice.invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'commission_amount', 'label' => 'Amount'],
                    ['key' => 'calculation_date', 'label' => 'Calculation Date'],
                    ['key' => 'payout_date', 'label' => 'Payout Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Commissions List (Current View) - Geraye Home Care Services',
                'document_title' => 'Partner Commissions List (Current View)',
                'filename_prefix' => 'partner-commissions-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'fields' => [
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'referral' => ['field' => 'referral.referral_date', 'default' => '-'],
                    'invoice' => ['field' => 'invoice.invoice_number', 'default' => '-'],
                    'commission_amount' => 'commission_amount',
                    'calculation_date' => ['field' => 'calculation_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'payout_date' => ['field' => 'payout_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'referral.referral_date', 'label' => 'Referral Date'],
                    ['key' => 'invoice.invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'commission_amount', 'label' => 'Amount'],
                    ['key' => 'calculation_date', 'label' => 'Calculation Date'],
                    ['key' => 'payout_date', 'label' => 'Payout Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Partner Commissions - Geraye Home Care Services',
                'document_title' => 'All Partner Commissions',
                'filename_prefix' => 'partner-commissions',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'calculation_date',
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'fields' => [
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'referral' => ['field' => 'referral.referral_date', 'default' => '-'],
                    'invoice' => ['field' => 'invoice.invoice_number', 'default' => '-'],
                    'commission_amount' => 'commission_amount',
                    'calculation_date' => ['field' => 'calculation_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'payout_date' => ['field' => 'payout_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'referral.referral_date', 'label' => 'Referral Date'],
                    ['key' => 'invoice.invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'commission_amount', 'label' => 'Amount'],
                    ['key' => 'calculation_date', 'label' => 'Calculation Date'],
                    ['key' => 'payout_date', 'label' => 'Payout Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Commission Record - Geraye Home Care Services',
                'document_title' => 'Partner Commission Record',
                'filename_prefix' => 'partner-commission-record',
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'fields' => [
                    'Agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'Referral Date' => ['field' => 'referral.referral_date', 'default' => '-'],
                    'Invoice #' => ['field' => 'invoice.invoice_number', 'default' => '-'],
                    'Amount' => 'commission_amount',
                    'Calculation Date' => ['field' => 'calculation_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Payout Date' => ['field' => 'payout_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Status' => 'status',
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'referral.referral_date', 'label' => 'Referral Date'],
                    ['key' => 'invoice.invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'commission_amount', 'label' => 'Amount'],
                    ['key' => 'calculation_date', 'label' => 'Calculation Date'],
                    ['key' => 'payout_date', 'label' => 'Payout Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for PartnerEngagement model
     */
    public static function getPartnerEngagementConfig(): array
    {
        return [
            'searchable_fields' => ['engagement_type', 'summary', 'partner.name', 'staff.first_name', 'staff.last_name'],
            'sortable_fields' => ['engagement_type', 'engagement_date', 'follow_up_date', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'partner-engagements',
            'select_fields' => [
                'partner_id', 'staff_id', 'engagement_type', 'summary', 'engagement_date', 'follow_up_date',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Partner', 'Staff', 'Type', 'Summary', 'Engagement Date', 'Follow Up Date',
                ],
                'fields' => [
                    'index',
                    [
                        'field' => 'partner.name',
                        'transform' => function ($value, $model) {
                            return $model->partner->name ?? '-';
                        },
                    ],
                    [
                        'field' => 'staff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->staff->first_name ?? '';
                            $ln = $model->staff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                    'engagement_type',
                    'summary',
                    [
                        'field' => 'engagement_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                    [
                        'field' => 'follow_up_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                ],
                'with_relations' => ['partner', 'staff'],
                'filename_prefix' => 'partner-engagements',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Engagements Export - Geraye Home Care Services',
                'document_title' => 'Partner Engagements Export',
                'filename_prefix' => 'partner-engagements',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['partner', 'staff'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'staff' => ['field' => 'staff.first_name', 'transform' => function ($value, $model) { $fn = $model->staff->first_name ?? ''; $ln = $model->staff->last_name ?? ''; $full = trim($fn . ' ' . $ln); return $full !== '' ? $full : '-'; }],
                    'engagement_type' => 'engagement_type',
                    'summary' => 'summary',
                    'engagement_date' => ['field' => 'engagement_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d H:i') : '-'; } ],
                    'follow_up_date' => ['field' => 'follow_up_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'engagement_type', 'label' => 'Type'],
                    ['key' => 'summary', 'label' => 'Summary'],
                    ['key' => 'engagement_date', 'label' => 'Engagement Date'],
                    ['key' => 'follow_up_date', 'label' => 'Follow Up Date'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Engagements List (Current View) - Geraye Home Care Services',
                'document_title' => 'Partner Engagements List (Current View)',
                'filename_prefix' => 'partner-engagements-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['partner', 'staff'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'staff' => ['field' => 'staff.first_name', 'transform' => function ($value, $model) { $fn = $model->staff->first_name ?? ''; $ln = $model->staff->last_name ?? ''; $full = trim($fn . ' ' . $ln); return $full !== '' ? $full : '-'; }],
                    'engagement_type' => 'engagement_type',
                    'summary' => 'summary',
                    'engagement_date' => ['field' => 'engagement_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d H:i') : '-'; } ],
                    'follow_up_date' => ['field' => 'follow_up_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'engagement_type', 'label' => 'Type'],
                    ['key' => 'summary', 'label' => 'Summary'],
                    ['key' => 'engagement_date', 'label' => 'Engagement Date'],
                    ['key' => 'follow_up_date', 'label' => 'Follow Up Date'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Partner Engagements - Geraye Home Care Services',
                'document_title' => 'All Partner Engagements',
                'filename_prefix' => 'partner-engagements',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'engagement_date',
                'with_relations' => ['partner', 'staff'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'staff' => ['field' => 'staff.first_name', 'transform' => function ($value, $model) { $fn = $model->staff->first_name ?? ''; $ln = $model->staff->last_name ?? ''; $full = trim($fn . ' ' . $ln); return $full !== '' ? $full : '-'; }],
                    'engagement_type' => 'engagement_type',
                    'summary' => 'summary',
                    'engagement_date' => ['field' => 'engagement_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d H:i') : '-'; } ],
                    'follow_up_date' => ['field' => 'follow_up_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'engagement_type', 'label' => 'Type'],
                    ['key' => 'summary', 'label' => 'Summary'],
                    ['key' => 'engagement_date', 'label' => 'Engagement Date'],
                    ['key' => 'follow_up_date', 'label' => 'Follow Up Date'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Engagement Record - Geraye Home Care Services',
                'document_title' => 'Partner Engagement Record',
                'filename_prefix' => 'partner-engagement-record',
                'with_relations' => ['partner', 'staff'],
                'fields' => [
                    'Partner' => ['field' => 'partner.name', 'default' => '-'],
                    'Staff' => ['field' => 'staff.full_name', 'default' => '-'],
                    'Type' => 'engagement_type',
                    'Summary' => 'summary',
                    'Engagement Date' => ['field' => 'engagement_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Follow Up Date' => ['field' => 'follow_up_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'engagement_type', 'label' => 'Type'],
                    ['key' => 'summary', 'label' => 'Summary'],
                    ['key' => 'engagement_date', 'label' => 'Engagement Date'],
                    ['key' => 'follow_up_date', 'label' => 'Follow Up Date'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for Referral model
     */
    public static function getReferralConfig(): array
    {
        return [
            'searchable_fields' => ['status', 'referral_date', 'partner.name', 'patient.full_name'],
            'sortable_fields' => ['status', 'referral_date', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'referrals',
            'select_fields' => [
                'partner_id', 'agreement_id', 'referred_patient_id', 'referral_date', 'status', 'notes',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Partner', 'Agreement', 'Referred Patient', 'Referral Date', 'Status', 'Notes',
                ],
                'fields' => [
                    'index',
                    [
                        'field' => 'partner.name',
                        'transform' => function ($value, $model) {
                            return $model->partner->name ?? '-';
                        },
                    ],
                    [
                        'field' => 'agreement.agreement_title',
                        'transform' => function ($value, $model) {
                            return $model->agreement->agreement_title ?? '-';
                        },
                    ],
                    [
                        'field' => 'patient.full_name',
                        'transform' => function ($value, $model) {
                            return $model->patient->full_name ?? '-';
                        },
                    ],
                    [
                        'field' => 'referral_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'status',
                    'notes',
                ],
                'with_relations' => ['partner', 'agreement', 'patient'],
                'filename_prefix' => 'referrals',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Referrals Export - Geraye Home Care Services',
                'document_title' => 'Referrals Export',
                'filename_prefix' => 'referrals',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['partner', 'agreement', 'patient'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'referral_date' => ['field' => 'referral_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                    'notes' => ['field' => 'notes', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'patient.full_name', 'label' => 'Referred Patient'],
                    ['key' => 'referral_date', 'label' => 'Referral Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'notes', 'label' => 'Notes'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Referrals List (Current View) - Geraye Home Care Services',
                'document_title' => 'Referrals List (Current View)',
                'filename_prefix' => 'referrals-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['partner', 'agreement', 'patient'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'referral_date' => ['field' => 'referral_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'patient.full_name', 'label' => 'Referred Patient'],
                    ['key' => 'referral_date', 'label' => 'Referral Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Referrals - Geraye Home Care Services',
                'document_title' => 'All Referrals',
                'filename_prefix' => 'referrals',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'referral_date',
                'with_relations' => ['partner', 'agreement', 'patient'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'referral_date' => ['field' => 'referral_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                    'notes' => ['field' => 'notes', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'patient.full_name', 'label' => 'Referred Patient'],
                    ['key' => 'referral_date', 'label' => 'Referral Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'notes', 'label' => 'Notes'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Referral Record - Geraye Home Care Services',
                'document_title' => 'Referral Record',
                'filename_prefix' => 'referral-record',
                'with_relations' => ['partner', 'agreement', 'patient'],
                'fields' => [
                    'Partner' => ['field' => 'partner.name', 'default' => '-'],
                    'Agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'Referred Patient' => ['field' => 'patient.full_name', 'default' => '-'],
                    'Referral Date' => ['field' => 'referral_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Status' => 'status',
                    'Notes' => ['field' => 'notes', 'default' => '-'],
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'patient.full_name', 'label' => 'Referred Patient'],
                    ['key' => 'referral_date', 'label' => 'Referral Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'notes', 'label' => 'Notes'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for PartnerCommission model
     */
    public static function getPartnerCommissionConfig(): array
    {
        return [
            'searchable_fields' => ['status', 'calculation_date', 'agreement.agreement_title', 'referral.referral_date', 'invoice.invoice_number'],
            'sortable_fields' => ['status', 'calculation_date', 'commission_amount', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'partner-commissions',
            'select_fields' => [
                'agreement_id', 'referral_id', 'invoice_id', 'commission_amount', 'calculation_date', 'payout_date', 'status',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Agreement', 'Referral Date', 'Invoice #', 'Amount', 'Calculation Date', 'Payout Date', 'Status',
                ],
                'fields' => [
                    'index',
                    [
                        'field' => 'agreement.agreement_title',
                        'transform' => function ($value, $model) {
                            return $model->agreement->agreement_title ?? '-';
                        },
                    ],
                    [
                        'field' => 'referral.referral_date',
                        'transform' => function ($value, $model) {
                            return $model->referral->referral_date ?? '-';
                        },
                    ],
                    [
                        'field' => 'invoice.invoice_number',
                        'transform' => function ($value, $model) {
                            return $model->invoice->invoice_number ?? '-';
                        },
                    ],
                    'commission_amount',
                    [
                        'field' => 'calculation_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'payout_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    'status',
                ],
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'filename_prefix' => 'partner-commissions',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Commissions Export - Geraye Home Care Services',
                'document_title' => 'Partner Commissions Export',
                'filename_prefix' => 'partner-commissions',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'fields' => [
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'referral' => ['field' => 'referral.referral_date', 'default' => '-'],
                    'invoice' => ['field' => 'invoice.invoice_number', 'default' => '-'],
                    'commission_amount' => 'commission_amount',
                    'calculation_date' => ['field' => 'calculation_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'payout_date' => ['field' => 'payout_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'referral.referral_date', 'label' => 'Referral Date'],
                    ['key' => 'invoice.invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'commission_amount', 'label' => 'Amount'],
                    ['key' => 'calculation_date', 'label' => 'Calculation Date'],
                    ['key' => 'payout_date', 'label' => 'Payout Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Commissions List (Current View) - Geraye Home Care Services',
                'document_title' => 'Partner Commissions List (Current View)',
                'filename_prefix' => 'partner-commissions-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'fields' => [
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'referral' => ['field' => 'referral.referral_date', 'default' => '-'],
                    'invoice' => ['field' => 'invoice.invoice_number', 'default' => '-'],
                    'commission_amount' => 'commission_amount',
                    'calculation_date' => ['field' => 'calculation_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'payout_date' => ['field' => 'payout_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'referral.referral_date', 'label' => 'Referral Date'],
                    ['key' => 'invoice.invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'commission_amount', 'label' => 'Amount'],
                    ['key' => 'calculation_date', 'label' => 'Calculation Date'],
                    ['key' => 'payout_date', 'label' => 'Payout Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Partner Commissions - Geraye Home Care Services',
                'document_title' => 'All Partner Commissions',
                'filename_prefix' => 'partner-commissions',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'calculation_date',
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'fields' => [
                    'agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'referral' => ['field' => 'referral.referral_date', 'default' => '-'],
                    'invoice' => ['field' => 'invoice.invoice_number', 'default' => '-'],
                    'commission_amount' => 'commission_amount',
                    'calculation_date' => ['field' => 'calculation_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'payout_date' => ['field' => 'payout_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'referral.referral_date', 'label' => 'Referral Date'],
                    ['key' => 'invoice.invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'commission_amount', 'label' => 'Amount'],
                    ['key' => 'calculation_date', 'label' => 'Calculation Date'],
                    ['key' => 'payout_date', 'label' => 'Payout Date'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Commission Record - Geraye Home Care Services',
                'document_title' => 'Partner Commission Record',
                'filename_prefix' => 'partner-commission-record',
                'with_relations' => ['agreement', 'referral', 'invoice'],
                'fields' => [
                    'Agreement' => ['field' => 'agreement.agreement_title', 'default' => '-'],
                    'Referral Date' => ['field' => 'referral.referral_date', 'default' => '-'],
                    'Invoice #' => ['field' => 'invoice.invoice_number', 'default' => '-'],
                    'Amount' => 'commission_amount',
                    'Calculation Date' => ['field' => 'calculation_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Payout Date' => ['field' => 'payout_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Status' => 'status',
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'agreement.agreement_title', 'label' => 'Agreement'],
                    ['key' => 'referral.referral_date', 'label' => 'Referral Date'],
                    ['key' => 'invoice.invoice_number', 'label' => 'Invoice #'],
                    ['key' => 'commission_amount', 'label' => 'Amount'],
                    ['key' => 'calculation_date', 'label' => 'Calculation Date'],
                    ['key' => 'payout_date', 'label' => 'Payout Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for PartnerEngagement model
     */
    public static function getPartnerEngagementConfig(): array
    {
        return [
            'searchable_fields' => ['engagement_type', 'summary', 'partner.name', 'staff.first_name', 'staff.last_name'],
            'sortable_fields' => ['engagement_type', 'engagement_date', 'follow_up_date', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'partner-engagements',
            'select_fields' => [
                'partner_id', 'staff_id', 'engagement_type', 'summary', 'engagement_date', 'follow_up_date',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Partner', 'Staff', 'Type', 'Summary', 'Engagement Date', 'Follow Up Date',
                ],
                'fields' => [
                    'index',
                    [
                        'field' => 'partner.name',
                        'transform' => function ($value, $model) {
                            return $model->partner->name ?? '-';
                        },
                    ],
                    [
                        'field' => 'staff.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->staff->first_name ?? '';
                            $ln = $model->staff->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                    'engagement_type',
                    'summary',
                    [
                        'field' => 'engagement_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-'; },
                    ],
                    [
                        'field' => 'follow_up_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                ],
                'with_relations' => ['partner', 'staff'],
                'filename_prefix' => 'partner-engagements',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Engagements Export - Geraye Home Care Services',
                'document_title' => 'Partner Engagements Export',
                'filename_prefix' => 'partner-engagements',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['partner', 'staff'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'staff' => ['field' => 'staff.first_name', 'transform' => function ($value, $model) { $fn = $model->staff->first_name ?? ''; $ln = $model->staff->last_name ?? ''; $full = trim($fn . ' ' . $ln); return $full !== '' ? $full : '-'; }],
                    'engagement_type' => 'engagement_type',
                    'summary' => 'summary',
                    'engagement_date' => ['field' => 'engagement_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d H:i') : '-'; } ],
                    'follow_up_date' => ['field' => 'follow_up_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'engagement_type', 'label' => 'Type'],
                    ['key' => 'summary', 'label' => 'Summary'],
                    ['key' => 'engagement_date', 'label' => 'Engagement Date'],
                    ['key' => 'follow_up_date', 'label' => 'Follow Up Date'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Engagements List (Current View) - Geraye Home Care Services',
                'document_title' => 'Partner Engagements List (Current View)',
                'filename_prefix' => 'partner-engagements-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['partner', 'staff'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'staff' => ['field' => 'staff.first_name', 'transform' => function ($value, $model) { $fn = $model->staff->first_name ?? ''; $ln = $model->staff->last_name ?? ''; $full = trim($fn . ' ' . $ln); return $full !== '' ? $full : '-'; }],
                    'engagement_type' => 'engagement_type',
                    'summary' => 'summary',
                    'engagement_date' => ['field' => 'engagement_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d H:i') : '-'; } ],
                    'follow_up_date' => ['field' => 'follow_up_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'engagement_type', 'label' => 'Type'],
                    ['key' => 'summary', 'label' => 'Summary'],
                    ['key' => 'engagement_date', 'label' => 'Engagement Date'],
                    ['key' => 'follow_up_date', 'label' => 'Follow Up Date'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Partner Engagements - Geraye Home Care Services',
                'document_title' => 'All Partner Engagements',
                'filename_prefix' => 'partner-engagements',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'engagement_date',
                'with_relations' => ['partner', 'staff'],
                'fields' => [
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'staff' => ['field' => 'staff.first_name', 'transform' => function ($value, $model) { $fn = $model->staff->first_name ?? ''; $ln = $model->staff->last_name ?? ''; $full = trim($fn . ' ' . $ln); return $full !== '' ? $full : '-'; }],
                    'engagement_type' => 'engagement_type',
                    'summary' => 'summary',
                    'engagement_date' => ['field' => 'engagement_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d H:i') : '-'; } ],
                    'follow_up_date' => ['field' => 'follow_up_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'engagement_type', 'label' => 'Type'],
                    ['key' => 'summary', 'label' => 'Summary'],
                    ['key' => 'engagement_date', 'label' => 'Engagement Date'],
                    ['key' => 'follow_up_date', 'label' => 'Follow Up Date'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Engagement Record - Geraye Home Care Services',
                'document_title' => 'Partner Engagement Record',
                'filename_prefix' => 'partner-engagement-record',
                'with_relations' => ['partner', 'staff'],
                'fields' => [
                    'Partner' => ['field' => 'partner.name', 'default' => '-'],
                    'Staff' => ['field' => 'staff.full_name', 'default' => '-'],
                    'Type' => 'engagement_type',
                    'Summary' => 'summary',
                    'Engagement Date' => ['field' => 'engagement_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Follow Up Date' => ['field' => 'follow_up_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'engagement_type', 'label' => 'Type'],
                    ['key' => 'summary', 'label' => 'Summary'],
                    ['key' => 'engagement_date', 'label' => 'Engagement Date'],
                    ['key' => 'follow_up_date', 'label' => 'Follow Up Date'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for PartnerAgreement model
     */
    public static function getPartnerAgreementConfig(): array
    {
        return [
            'searchable_fields' => ['agreement_title', 'agreement_type', 'status', 'partner.name', 'signedBy.first_name', 'signedBy.last_name'],
            'sortable_fields' => ['agreement_title', 'agreement_type', 'status', 'start_date', 'end_date', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'partner-agreements',
            'select_fields' => [
                'agreement_title', 'agreement_type', 'status', 'start_date', 'end_date', 'priority_service_level', 'commission_type', 'commission_rate', 'terms_document_path',
            ],

            'csv' => [
                'headers' => [
                    '#', 'Title', 'Type', 'Status', 'Start Date', 'End Date', 'Partner', 'Signed By',
                ],
                'fields' => [
                    'index',
                    'agreement_title',
                    'agreement_type',
                    'status',
                    [
                        'field' => 'start_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'end_date',
                        'transform' => function ($value) { return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-'; },
                    ],
                    [
                        'field' => 'partner.name',
                        'transform' => function ($value, $model) {
                            return $model->partner->name ?? '-';
                        },
                    ],
                    [
                        'field' => 'signedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->signedBy->first_name ?? '';
                            $ln = $model->signedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                ],
                'with_relations' => ['partner', 'signedBy'],
                'filename_prefix' => 'partner-agreements',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Agreements Export - Geraye Home Care Services',
                'document_title' => 'Partner Agreements Export',
                'filename_prefix' => 'partner-agreements',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['partner', 'signedBy'],
                'fields' => [
                    'agreement_title' => 'agreement_title',
                    'agreement_type' => 'agreement_type',
                    'status' => 'status',
                    'start_date' => ['field' => 'start_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'end_date' => ['field' => 'end_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'signed_by' => [
                        'field' => 'signedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->signedBy->first_name ?? '';
                            $ln = $model->signedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'agreement_title', 'label' => 'Title'],
                    ['key' => 'agreement_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'signedBy.full_name', 'label' => 'Signed By'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Agreements List (Current View) - Geraye Home Care Services',
                'document_title' => 'Partner Agreements List (Current View)',
                'filename_prefix' => 'partner-agreements-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['partner', 'signedBy'],
                'fields' => [
                    'agreement_title' => 'agreement_title',
                    'agreement_type' => 'agreement_type',
                    'status' => 'status',
                    'start_date' => ['field' => 'start_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'end_date' => ['field' => 'end_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'signed_by' => [
                        'field' => 'signedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->signedBy->first_name ?? '';
                            $ln = $model->signedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'agreement_title', 'label' => 'Title'],
                    ['key' => 'agreement_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'signedBy.full_name', 'label' => 'Signed By'],
                ],
            ],

            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Partner Agreements - Geraye Home Care Services',
                'document_title' => 'All Partner Agreements',
                'filename_prefix' => 'partner-agreements',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'agreement_title',
                'with_relations' => ['partner', 'signedBy'],
                'fields' => [
                    'agreement_title' => 'agreement_title',
                    'agreement_type' => 'agreement_type',
                    'status' => 'status',
                    'start_date' => ['field' => 'start_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'end_date' => ['field' => 'end_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-'; } ],
                    'partner' => ['field' => 'partner.name', 'default' => '-'],
                    'signed_by' => [
                        'field' => 'signedBy.first_name',
                        'transform' => function ($value, $model) {
                            $fn = $model->signedBy->first_name ?? '';
                            $ln = $model->signedBy->last_name ?? '';
                            $full = trim($fn . ' ' . $ln);
                            return $full !== '' ? $full : '-';
                        },
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'agreement_title', 'label' => 'Title'],
                    ['key' => 'agreement_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'signedBy.full_name', 'label' => 'Signed By'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Partner Agreement Record - Geraye Home Care Services',
                'document_title' => 'Partner Agreement Record',
                'filename_prefix' => 'partner-agreement-record',
                'with_relations' => ['partner', 'signedBy'],
                'fields' => [
                    'Title' => 'agreement_title',
                    'Type' => 'agreement_type',
                    'Status' => 'status',
                    'Start Date' => ['field' => 'start_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'End Date' => ['field' => 'end_date', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y') : '-'; } ],
                    'Priority Service Level' => ['field' => 'priority_service_level', 'default' => '-'],
                    'Commission Type' => ['field' => 'commission_type', 'default' => '-'],
                    'Commission Rate' => ['field' => 'commission_rate', 'default' => '-'],
                    'Terms Document Path' => ['field' => 'terms_document_path', 'default' => '-'],
                    'Partner' => ['field' => 'partner.name', 'default' => '-'],
                    'Signed By' => ['field' => 'signedBy.full_name', 'default' => '-'],
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'agreement_title', 'label' => 'Title'],
                    ['key' => 'agreement_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'end_date', 'label' => 'End Date'],
                    ['key' => 'priority_service_level', 'label' => 'Priority Service Level'],
                    ['key' => 'commission_type', 'label' => 'Commission Type'],
                    ['key' => 'commission_rate', 'label' => 'Commission Rate'],
                    ['key' => 'terms_document_path', 'label' => 'Terms Document Path'],
                    ['key' => 'partner.name', 'label' => 'Partner'],
                    ['key' => 'signedBy.full_name', 'label' => 'Signed By'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    public static function getEventStaffAssignmentConfig(): array
    {
    {
        return [
            'searchable_fields' => ['role', 'event.title', 'staff.first_name', 'staff.last_name'],
            'sortable_fields' => ['role', 'event_id', 'staff_id', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'event-staff-assignments',
            'with_relations' => ['event', 'staff'],

            // Fallback PDF config (legacy path, not used by centralized flows)
            'pdf' => [
                'view' => 'exports.universal-report',
                'title' => 'Event Staff Assignments - Geraye Home Care Services',
                'document_title' => 'Event Staff Assignments',
                'filename_prefix' => 'event-staff-assignments',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'event' => ['field' => 'event.title', 'default' => '-'],
                    'staff' => ['field' => 'staff.full_name', 'default' => '-'],
                    'role' => ['field' => 'role', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'event.title', 'label' => 'Event'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'role', 'label' => 'Role'],
                ],
            ],

            'current_page' => [
                'view' => 'exports.universal-report',
                'title' => 'Event Staff Assignments (Current View) - Geraye Home Care Services',
                'document_title' => 'Event Staff Assignments (Current View)',
                'filename_prefix' => 'event-staff-assignments-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'with_relations' => ['event', 'staff'],
                'fields' => [
                    'event' => ['field' => 'event.title', 'default' => '-'],
                    'staff' => ['field' => 'staff.full_name', 'default' => '-'],
                    'role' => ['field' => 'role', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'event.title', 'label' => 'Event'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'role', 'label' => 'Role'],
                ],
            ],

            'all_records' => [
                'view' => 'exports.universal-report',
                'title' => 'All Event Staff Assignments - Geraye Home Care Services',
                'document_title' => 'All Event Staff Assignments',
                'filename_prefix' => 'event-staff-assignments',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'created_at',
                'with_relations' => ['event', 'staff'],
                'fields' => [
                    'event' => ['field' => 'event.title', 'default' => '-'],
                    'staff' => ['field' => 'staff.full_name', 'default' => '-'],
                    'role' => ['field' => 'role', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'event.title', 'label' => 'Event'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'role', 'label' => 'Role'],
                ],
            ],

            'single_record' => [
                'view' => 'exports.universal-single-record',
                'title' => 'Event Staff Assignment - Geraye Home Care Services',
                'document_title' => 'Event Staff Assignment',
                'filename_prefix' => 'event-staff-assignment',
                'with_relations' => ['event', 'staff'],
                'fields' => [
                    'Event' => ['field' => 'event.title', 'default' => '-'],
                    'Staff' => ['field' => 'staff.full_name', 'default' => '-'],
                    'Role' => ['field' => 'role', 'default' => '-'],
                    'Created At' => ['field' => 'created_at', 'transform' => function ($v) {return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-';}],
                    'Updated At' => ['field' => 'updated_at', 'transform' => function ($v) {return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-';}],
                ],
                'columns' => [
                    ['key' => 'event.title', 'label' => 'Event'],
                    ['key' => 'staff.full_name', 'label' => 'Staff'],
                    ['key' => 'role', 'label' => 'Role'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
                ],
            ],
        ];
    }

    /**
     * Get export/print configuration for EventRecommendation model
     */
    public static function getEventRecommendationConfig(): array
    {
        return [
            // Use ACTUAL DB columns here because the printing trait queries directly
            'searchable_fields' => ['patient_name', 'source_channel', 'recommended_by_name', 'phone_number', 'status'],
            'sortable_fields' => ['patient_name', 'source_channel', 'recommended_by_name', 'phone_number', 'status', 'created_at'],
            'default_sort' => 'created_at',
            'filename_prefix' => 'event-recommendations',

            // Default PDF config (fallback)
            'pdf' => [
                'view' => 'pdf-layout',
                'title' => 'Event Recommendations - Geraye Home Care Services',
                'document_title' => 'Event Recommendations',
                'filename_prefix' => 'event-recommendations',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'patient_name' => 'patient_name',
                    'source_channel' => ['field' => 'source_channel', 'default' => '-'],
                    'recommended_by_name' => ['field' => 'recommended_by_name', 'default' => '-'],
                    'phone_number' => ['field' => 'phone_number', 'default' => '-'],
                    'status' => ['field' => 'status', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'patient_name', 'label' => 'Patient Name'],
                    ['key' => 'source_channel', 'label' => 'Source'],
                    ['key' => 'recommended_by_name', 'label' => 'Recommended By'],
                    ['key' => 'phone_number', 'label' => 'Patient Phone'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            // Current paginated view
            'current_page' => [
                'view' => 'pdf-layout',
                'title' => 'Event Recommendations (Current View) - Geraye Home Care Services',
                'document_title' => 'Event Recommendations (Current View)',
                'filename_prefix' => 'event-recommendations-current',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'patient_name' => 'patient_name',
                    'source_channel' => ['field' => 'source_channel', 'default' => '-'],
                    'recommended_by_name' => ['field' => 'recommended_by_name', 'default' => '-'],
                    'phone_number' => ['field' => 'phone_number', 'default' => '-'],
                    'status' => ['field' => 'status', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'patient_name', 'label' => 'Patient Name'],
                    ['key' => 'source_channel', 'label' => 'Source'],
                    ['key' => 'recommended_by_name', 'label' => 'Recommended By'],
                    ['key' => 'phone_number', 'label' => 'Patient Phone'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            // All records view
            'all_records' => [
                'view' => 'pdf-layout',
                'title' => 'All Event Recommendations - Geraye Home Care Services',
                'document_title' => 'All Event Recommendations',
                'filename_prefix' => 'event-recommendations',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'created_at',
                'fields' => [
                    'patient_name' => 'patient_name',
                    'source' => ['field' => 'source', 'default' => '-'],
                    'recommended_by' => ['field' => 'recommended_by', 'default' => '-'],
                    'patient_phone' => ['field' => 'patient_phone', 'default' => '-'],
                    'status' => ['field' => 'status', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'patient_name', 'label' => 'Patient Name'],
                    ['key' => 'source', 'label' => 'Source'],
                    ['key' => 'recommended_by', 'label' => 'Recommended By'],
                    ['key' => 'patient_phone', 'label' => 'Patient Phone'],
                    ['key' => 'status', 'label' => 'Status'],
                ],
            ],

            // Single record view (optional)
            'single_record' => [
                'view' => 'pdf-layout',
                'title' => 'Event Recommendation Detail - Geraye Home Care Services',
                'document_title' => 'Event Recommendation',
                'filename_prefix' => 'event-recommendation-record',
                'fields' => [
                    'Patient Name' => 'patient_name',
                    'Source' => ['field' => 'source_channel', 'default' => '-'],
                    'Recommended By' => ['field' => 'recommended_by_name', 'default' => '-'],
                    'Patient Phone' => ['field' => 'phone_number', 'default' => '-'],
                    'Notes' => ['field' => 'notes', 'default' => '-'],
                    'Status' => ['field' => 'status', 'default' => '-'],
                    'Created At' => [ 'field' => 'created_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                    'Updated At' => [ 'field' => 'updated_at', 'transform' => function ($v) { return $v ? \Carbon\Carbon::parse($v)->format('F j, Y, g:i a') : '-'; } ],
                ],
                'columns' => [
                    ['key' => 'patient_name', 'label' => 'Patient Name'],
                    ['key' => 'source_channel', 'label' => 'Source'],
                    ['key' => 'recommended_by_name', 'label' => 'Recommended By'],
                    ['key' => 'phone_number', 'label' => 'Patient Phone'],
                    ['key' => 'notes', 'label' => 'Notes'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'created_at', 'label' => 'Created At'],
                    ['key' => 'updated_at', 'label' => 'Updated At'],
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
                    'full_name',
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
                    'full_name' => 'full_name',
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
                    'full_name' => 'full_name',
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
                    'full_name' => 'full_name',
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
                    'Full Name' => 'full_name',
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
