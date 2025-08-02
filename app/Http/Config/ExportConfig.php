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
                'filename' => 'patients.csv'
            ],
            
            'pdf' => [
                'title' => 'Patient Export - Geraye Home Care Services',
                'document_title' => 'Patient Records Export',
                'filename' => 'patients.pdf',
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
            
            'print_current' => [
                'title' => 'Patient List (Current View) - Geraye Home Care Services',
                'document_title' => 'Patient List (Current View)',
                'filename' => 'patients-current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
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
            
            'print_all' => [
                'title' => 'Patient List - Geraye Home Care Services',
                'document_title' => 'Patient Records Export',
                'filename' => 'patients.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'full_name',
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
                    'Emergency Contact' => 'emergency_contact',
                    'Source' => ['field' => 'source', 'default' => '-'],
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
            
            'print_current' => [
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
            
            'print_all' => [
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
            'with_relations' => ['staff', 'patient'],
            
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
                'title' => 'All Caregiver Assignments - Geraye',
                'document_title' => 'Caregiver Assignment Records Export',
                'filename' => 'assignments-all.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
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
                    ['key' => 'patient_name', 'label' => 'Patient Name'],
                    ['key' => 'staff_member', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ]
            ],
            
            'print_current' => [
                'title' => 'Caregiver Assignments (Current View) - Geraye',
                'document_title' => 'Caregiver Assignments (Current View)',
                'filename' => 'assignments-current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
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
                    ['key' => 'patient_name', 'label' => 'Patient Name'],
                    ['key' => 'staff_member', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ]
            ],
            
            'print_all' => [
                'title' => 'All Caregiver Assignments - Geraye',
                'document_title' => 'Caregiver Assignment Records Export',
                'filename' => 'assignments-all.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'shift_start',
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
                    ['key' => 'patient_name', 'label' => 'Patient Name'],
                    ['key' => 'staff_member', 'label' => 'Staff Member'],
                    ['key' => 'shift_start', 'label' => 'Shift Start'],
                    ['key' => 'shift_end', 'label' => 'Shift End'],
                    ['key' => 'status', 'label' => 'Status'],
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Patient Name' => ['field' => 'patient.full_name', 'default' => 'N/A'],
                    'Staff Member' => [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'Shift Start' => [
                        'field' => 'shift_start',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        }
                    ],
                    'Shift End' => [
                        'field' => 'shift_end',
                        'transform' => function($value, $model) {
                            return $value ? \Carbon\Carbon::parse($value)->format('F j, Y, g:i a') : 'N/A';
                        }
                    ],
                    'Status' => 'status',
                ]
            ]
        ];
    }
}
