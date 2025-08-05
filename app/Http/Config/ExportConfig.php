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
                'phone_number', 'address', 'gender', 'emergency_contact', 'date_of_birth'
            ],
            
            'csv' => [
                'headers' => [
                    'Full Name', 'Patient Code', 'Fayda ID', 'Email', 'Source', 
                    'Phone', 'Address', 'Gender', 'Emergency Contact'
                ],
                'fields' => [
                    'full_name', 'patient_code', 'fayda_id', 'email', 'source',
                    'phone_number', 'address', 'gender', 'emergency_contact'
                ],
                'filename_prefix' => 'patients' // Changed from 'filename' to 'filename_prefix'
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
                ]
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
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->age : '-';
                        }
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
                ]
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
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->age : '-';
                        }
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
                ]
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
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->age . ' years' : '-';
                        }
                    ],
                    'Gender' => ['field' => 'gender', 'default' => '-'],
                    'Phone Number' => 'phone_number',
                    'Email' => 'email',
                    'Address' => ['field' => 'address', 'default' => '-'],
                    'Emergency Contact' => ['field' => 'emergency_contact', 'default' => '-'],
                    'Source' => ['field' => 'source', 'default' => '-'],
                    'Registered By Staff' => ['field' => 'registeredByStaff.full_name', 'default' => '-'],
                    'Registered Date' => ['field' => 'created_at', 'transform' => function($value) { return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-'; }],
                    'Last Updated' => ['field' => 'updated_at', 'transform' => function($value) { return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-'; }],
                ],
                'columns' => [
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'patient_code', 'label' => 'Patient Code'],
                    ['key' => 'fayda_id', 'label' => 'Fayda ID'],
                    ['key' => 'date_of_birth', 'label' => 'Date of Birth'],
                    ['key' => 'age', 'label' => 'Age', 'transform' => function($value, $model) { return $model->date_of_birth ? \Carbon\Carbon::parse($model->date_of_birth)->age . ' years' : '-'; }],
                    ['key' => 'gender', 'label' => 'Gender'],
                    ['key' => 'phone_number', 'label' => 'Phone Number'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'address', 'label' => 'Address'],
                    ['key' => 'emergency_contact', 'label' => 'Emergency Contact'],
                    ['key' => 'source', 'label' => 'Source'],
                    ['key' => 'registeredByStaff.full_name', 'label' => 'Registered By Staff'],
                    ['key' => 'created_at', 'label' => 'Registered Date', 'transform' => function($value) { return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-'; }],
                    ['key' => 'updated_at', 'label' => 'Last Updated', 'transform' => function($value) { return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-'; }],
                ]
            ]
        ];
    }

    /**
     * Get export configuration for Event model
     */
    public static function getEventConfig(): array
    {
        return [
            'searchable_fields' => ['title', 'description'],
            'sortable_fields' => ['title', 'event_date', 'broadcast_status', 'created_at'],
            'default_sort' => 'created_at',
            'select_fields' => [
                'title', 'description', 'event_date', 'is_free_service', 'broadcast_status'
            ],
            
            'csv' => [
                'headers' => [
                    'Title', 'Description', 'Event Date', 'Is Free Service', 'Broadcast Status'
                ],
                'fields' => [
                    'title', 'description', 'event_date', 'is_free_service', 'broadcast_status'
                ],
                'filename' => 'events.csv'
            ],
            
            'pdf' => [
                'title' => 'Events List',
                'document_title' => 'Events List',
                'filename' => 'events.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'title' => 'title',
                    'description' => 'description',
                    'event_date' => 'event_date',
                    'is_free_service' => [
                        'field' => 'is_free_service',
                        'transform' => function($value, $model) {
                            return $value ? 'Yes' : 'No';
                        }
                    ],
                    'broadcast_status' => 'broadcast_status',
                ],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'event_date', 'label' => 'Event Date'],
                    ['key' => 'is_free_service', 'label' => 'Free Service'],
                    ['key' => 'broadcast_status', 'label' => 'Broadcast Status'],
                ]
            ],
            
            'print_current' => [
                'title' => 'Events List (Current View)',
                'document_title' => 'Events List (Current View)',
                'filename' => 'events-current.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'title' => 'title',
                    'description' => 'description',
                    'event_date' => 'event_date',
                    'is_free_service' => [
                        'field' => 'is_free_service',
                        'transform' => function($value, $model) {
                            return $value ? 'Yes' : 'No';
                        }
                    ],
                    'broadcast_status' => 'broadcast_status',
                ],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'event_date', 'label' => 'Event Date'],
                    ['key' => 'is_free_service', 'label' => 'Free Service'],
                    ['key' => 'broadcast_status', 'label' => 'Broadcast Status'],
                ]
            ],
            
            'print_all' => [
                'title' => 'Events List',
                'document_title' => 'Events List',
                'filename' => 'events.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'default_sort' => 'title',
                'fields' => [
                    'title' => 'title',
                    'description' => 'description',
                    'event_date' => 'event_date',
                    'is_free_service' => [
                        'field' => 'is_free_service',
                        'transform' => function($value, $model) {
                            return $value ? 'Yes' : 'No';
                        }
                    ],
                    'broadcast_status' => 'broadcast_status',
                ],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'event_date', 'label' => 'Event Date'],
                    ['key' => 'is_free_service', 'label' => 'Free Service'],
                    ['key' => 'broadcast_status', 'label' => 'Broadcast Status'],
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Title' => 'title',
                    'Description' => 'description',
                    'Event Date' => 'event_date',
                    'Free Service' => [
                        'field' => 'is_free_service',
                        'transform' => function($value, $model) {
                            return $value ? 'Yes' : 'No';
                        }
                    ],
                    'Broadcast Status' => 'broadcast_status',
                ]
            ]
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
                'department', 'status', 'hire_date'
            ],
            
            'csv' => [
                'headers' => [
                    'Full Name', 'Email', 'Phone', 'Position', 'Department', 'Status', 'Hire Date'
                ],
                'fields' => [
                    ['field' => 'first_name', 'transform' => function($value, $model) {
                        return $model->first_name . ' ' . $model->last_name;
                    }],
                    'email', 'phone', 'position', 'department', 'status', 'hire_date'
                ],
                'filename' => 'staff.csv'
            ],
            
            'pdf' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Staff Export - Geraye Home Care Services',
                'document_title' => 'All Staff Records',
                'filename' => 'staff.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'full_name' => [
                        'field' => 'first_name',
                        'transform' => function($value, $model) {
                            return $model->first_name . ' ' . $model->last_name;
                        }
                    ],
                    'email' => ['field' => 'email', 'default' => '-'],
                    'phone' => ['field' => 'phone', 'default' => '-'],
                    'position' => ['field' => 'position', 'default' => '-'],
                    'department' => ['field' => 'department', 'default' => '-'],
                    'status' => 'status',
                    'hire_date' => [
                        'field' => 'hire_date',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                        }
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
                ]
            ],
            
            'current_page' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Staff List (Current View) - Geraye Home Care Services',
                'document_title' => 'Staff List (Current View)',
                'filename' => 'staff-current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'full_name' => [
                        'field' => 'first_name',
                        'transform' => function($value, $model) {
                            return $model->first_name . ' ' . $model->last_name;
                        }
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
                ]
            ],
            
            'all_records' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Staff List - Geraye Home Care Services',
                'document_title' => 'All Staff Records',
                'filename' => 'staff.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'first_name',
                'fields' => [
                    'full_name' => [
                        'field' => 'first_name',
                        'transform' => function($value, $model) {
                            return $model->first_name . ' ' . $model->last_name;
                        }
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
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Full Name' => [
                        'field' => 'first_name',
                        'transform' => function($value, $model) {
                            return $model->first_name . ' ' . $model->last_name;
                        }
                    ],
                    'Email' => ['field' => 'email', 'default' => '-'],
                    'Phone' => ['field' => 'phone', 'default' => '-'],
                    'Position' => ['field' => 'position', 'default' => '-'],
                    'Department' => ['field' => 'department', 'default' => '-'],
                    'Status' => 'status',
                    'Hire Date' => [
                        'field' => 'hire_date',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                ]
            ]
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
                'patient_id', 'staff_id', 'shift_start', 'shift_end', 'status'
            ],
            
            'csv' => [
                'headers' => [
                    'Patient Name', 'Staff Name', 'Shift Start', 'Shift End', 'Status'
                ],
                'fields' => [
                    ['field' => 'patient.full_name', 'default' => 'N/A'],
                    [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'shift_start', 'shift_end', 'status'
                ],
                'filename' => 'assignments.csv'
            ],
            
            'pdf' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'All Caregiver Assignments - Geraye',
                'document_title' => 'Caregiver Assignment Records Export',
                'filename_prefix' => 'assignments-all',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['staff', 'patient'],
                'fields' => [
                    'patient_name' => ['field' => 'patient.full_name', 'default' => 'N/A'],
                    'staff_member' => [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'shift_start' => [
                        'field' => 'shift_start',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        }
                    ],
                    'shift_end' => [
                        'field' => 'shift_end',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        }
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'patient.full_name', 'label' => 'Patient Name'],
                    ['key' => 'staff.first_name', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ]
            ],
            
            'print_current' => [
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
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'shift_start' => [
                        'field' => 'shift_start',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M j, Y g:i a') : 'N/A';
                        }
                    ],
                    'shift_end' => [
                        'field' => 'shift_end',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M j, Y g:i a') : 'N/A';
                        }
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'patient.full_name', 'label' => 'Patient Name'],
                    ['key' => 'staff.first_name', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ]
            ],
            
            'all_records' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'All Caregiver Assignments - Geraye',
                'document_title' => 'Caregiver Assignment Records Export',
                'filename_prefix' => 'assignments-all',
                'orientation' => 'landscape',
                'include_index' => false,
                'with_relations' => ['staff', 'patient'],
                'fields' => [
                    'patient_name' => ['field' => 'patient.full_name', 'default' => 'N/A'],
                    'staff_member' => [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'shift_start' => [
                        'field' => 'shift_start',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        }
                    ],
                    'shift_end' => [
                        'field' => 'shift_end',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        }
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'patient.full_name', 'label' => 'Patient Name'],
                    ['key' => 'staff.first_name', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ]
            ],
            
            'single_record' => [
                'view' => 'pdf-layout', // Changed from 'print-layout' to 'pdf-layout'
                'title' => 'Caregiver Assignment Record - Geraye',
                'document_title' => 'Caregiver Assignment Record',
                'filename_prefix' => 'assignment-record',
                'with_relations' => ['staff', 'patient'],
                'fields' => [
                    'Patient Name' => ['field' => 'patient.full_name', 'default' => 'N/A'],
                    'Staff Member' => ['field' => 'staff.first_name','transform' => function($value, $model) { return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? ''); }],
                    'Shift Start' => ['field' => 'shift_start','transform' => function($value, $model) { return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A'; }],
                    'Shift End' => ['field' => 'shift_end','transform' => function($value, $model) { return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A'; }],
                    'Status' => 'status',
                ],
                'columns' => [
                    ['key' => 'patient.full_name', 'label' => 'Patient Name'],
                    ['key' => 'staff.first_name', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ]
            ]
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
                'name', 'description', 'category', 'price', 'duration', 'is_active'
            ],
            
            'csv' => [
                'headers' => [
                    'Name', 'Description', 'Category', 'Price', 'Duration', 'Active'
                ],
                'fields' => [
                    'name', 'description', 'category', 'price', 'duration', 'is_active'
                ],
                'filename_prefix' => 'services'
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
                        'transform' => function($value) {
                            return '$' . number_format($value, 2);
                        }
                    ],
                    'duration' => [
                        'field' => 'duration',
                        'transform' => function($value) {
                            return $value . ' minutes';
                        }
                    ],
                    'is_active' => [
                        'field' => 'is_active',
                        'transform' => function($value) {
                            return $value ? 'Active' : 'Inactive';
                        }
                    ],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => 'Service Name'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'category', 'label' => 'Category'],
                    ['key' => 'price', 'label' => 'Price'],
                    ['key' => 'duration', 'label' => 'Duration'],
                    ['key' => 'is_active', 'label' => 'Status'],
                ]
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
                        'transform' => function($value) {
                            return '$' . number_format($value, 2);
                        }
                    ],
                    'duration' => [
                        'field' => 'duration',
                        'transform' => function($value) {
                            return $value . ' min';
                        }
                    ],
                    'is_active' => [
                        'field' => 'is_active',
                        'transform' => function($value) {
                            return $value ? 'Active' : 'Inactive';
                        }
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Service Name'],
                    ['key' => 'category', 'label' => 'Category'],
                    ['key' => 'price', 'label' => 'Price'],
                    ['key' => 'duration', 'label' => 'Duration'],
                    ['key' => 'is_active', 'label' => 'Status'],
                ]
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
                        'transform' => function($value) {
                            return '$' . number_format($value, 2);
                        }
                    ],
                    'duration' => [
                        'field' => 'duration',
                        'transform' => function($value) {
                            return $value . ' min';
                        }
                    ],
                    'is_active' => [
                        'field' => 'is_active',
                        'transform' => function($value) {
                            return $value ? 'Active' : 'Inactive';
                        }
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Service Name'],
                    ['key' => 'category', 'label' => 'Category'],
                    ['key' => 'price', 'label' => 'Price'],
                    ['key' => 'duration', 'label' => 'Duration'],
                    ['key' => 'is_active', 'label' => 'Status'],
                ]
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
                        'transform' => function($value) {
                            return '$' . number_format($value, 2);
                        }
                    ],
                    'Duration' => [
                        'field' => 'duration',
                        'transform' => function($value) {
                            return $value . ' minutes';
                        }
                    ],
                    'Status' => [
                        'field' => 'is_active',
                        'transform' => function($value) {
                            return $value ? 'Active' : 'Inactive';
                        }
                    ],
                    'Created Date' => [
                        'field' => 'created_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-';
                        }
                    ],
                    'Last Updated' => [
                        'field' => 'updated_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : '-';
                        }
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
                ]
            ]
        ];
    }
}
