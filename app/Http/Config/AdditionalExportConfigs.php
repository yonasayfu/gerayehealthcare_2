<?php

namespace App\Http\Config;

class AdditionalExportConfigs
{
    /**
     * Get export configuration for MarketingCampaign model
     */
    public static function getMarketingCampaignConfig(): array
    {
        return [
            'searchable_fields' => ['campaign_name', 'campaign_code'],
            'sortable_fields' => ['campaign_name', 'status', 'start_date', 'created_at'],
            'default_sort' => 'created_at',
            'with_relations' => ['platform', 'assignedStaff', 'createdByStaff'],
            
            'csv' => [
                'headers' => ['Campaign Code', 'Campaign Name', 'Platform', 'Status', 'Start Date', 'Budget'],
                'fields' => [
                    ['field' => 'campaign_code', 'default' => '-'],
                    'campaign_name',
                    ['field' => 'platform.name', 'default' => '-'],
                    'status',
                    'start_date',
                    ['field' => 'budget_allocated', 'default' => '0']
                ],
                'filename' => 'marketing_campaigns.csv'
            ],
            
            'pdf' => [
                'title' => 'Marketing Campaigns List',
                'document_title' => 'Marketing Campaigns List',
                'filename' => 'marketing_campaigns.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'campaign_code' => ['field' => 'campaign_code', 'default' => '-'],
                    'campaign_name' => 'campaign_name',
                    'platform_name' => ['field' => 'platform.name', 'default' => '-'],
                    'status' => 'status',
                    'start_date' => ['field' => 'start_date', 'default' => '-'],
                    'budget_allocated' => [
                        'field' => 'budget_allocated',
                        'transform' => function($value, $model) {
                            return number_format($value ?? 0, 2);
                        }
                    ],
                ],
                'columns' => [
                    ['key' => 'campaign_code', 'label' => 'Code'],
                    ['key' => 'campaign_name', 'label' => 'Campaign Name'],
                    ['key' => 'platform_name', 'label' => 'Platform'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                    ['key' => 'budget_allocated', 'label' => 'Budget'],
                ]
            ],
            
            'print_current' => [
                'title' => 'Marketing Campaigns (Current View)',
                'document_title' => 'Marketing Campaigns (Current View)',
                'filename' => 'campaigns_current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'campaign_name' => 'campaign_name',
                    'platform_name' => ['field' => 'platform.name', 'default' => '-'],
                    'status' => 'status',
                    'start_date' => ['field' => 'start_date', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'campaign_name', 'label' => 'Campaign Name'],
                    ['key' => 'platform_name', 'label' => 'Platform'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                ]
            ],
            
            'print_all' => [
                'title' => 'All Marketing Campaigns',
                'document_title' => 'All Marketing Campaigns',
                'filename' => 'campaigns_all.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'campaign_name',
                'fields' => [
                    'campaign_name' => 'campaign_name',
                    'platform_name' => ['field' => 'platform.name', 'default' => '-'],
                    'status' => 'status',
                    'start_date' => ['field' => 'start_date', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'campaign_name', 'label' => 'Campaign Name'],
                    ['key' => 'platform_name', 'label' => 'Platform'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'start_date', 'label' => 'Start Date'],
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Campaign Code' => ['field' => 'campaign_code', 'default' => '-'],
                    'Campaign Name' => 'campaign_name',
                    'Platform' => ['field' => 'platform.name', 'default' => '-'],
                    'Status' => 'status',
                    'Start Date' => ['field' => 'start_date', 'default' => '-'],
                    'End Date' => ['field' => 'end_date', 'default' => '-'],
                    'Budget Allocated' => [
                        'field' => 'budget_allocated',
                        'transform' => function($value, $model) {
                            return '$' . number_format($value ?? 0, 2);
                        }
                    ],
                ]
            ]
        ];
    }

    /**
     * Get export configuration for InsuranceCompany model
     */
    public static function getInsuranceCompanyConfig(): array
    {
        return [
            'searchable_fields' => ['name', 'contact_person'],
            'sortable_fields' => ['name', 'contact_person', 'created_at'],
            'default_sort' => 'name',
            'select_fields' => ['name', 'contact_person', 'contact_email', 'contact_phone', 'address'],
            
            'csv' => [
                'headers' => ['Name', 'Contact Person', 'Contact Email', 'Contact Phone', 'Address'],
                'fields' => ['name', 'contact_person', 'contact_email', 'contact_phone', 'address'],
                'filename' => 'insurance_companies.csv'
            ],
            
            'pdf' => [
                'title' => 'Insurance Companies List',
                'document_title' => 'Insurance Companies List',
                'filename' => 'insurance_companies.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'name' => 'name',
                    'contact_person' => 'contact_person',
                    'contact_email' => 'contact_email',
                    'contact_phone' => 'contact_phone',
                    'address' => 'address',
                ],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'contact_email', 'label' => 'Contact Email'],
                    ['key' => 'contact_phone', 'label' => 'Contact Phone'],
                    ['key' => 'address', 'label' => 'Address'],
                ]
            ],
            
            'print_current' => [
                'title' => 'Insurance Companies (Current View)',
                'document_title' => 'Insurance Companies (Current View)',
                'filename' => 'insurance_companies_current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'name' => 'name',
                    'contact_person' => 'contact_person',
                    'contact_email' => 'contact_email',
                    'contact_phone' => 'contact_phone',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'contact_email', 'label' => 'Email'],
                    ['key' => 'contact_phone', 'label' => 'Phone'],
                ]
            ],
            
            'print_all' => [
                'title' => 'All Insurance Companies',
                'document_title' => 'All Insurance Companies',
                'filename' => 'insurance_companies_all.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'name',
                'fields' => [
                    'name' => 'name',
                    'contact_person' => 'contact_person',
                    'contact_email' => 'contact_email',
                    'contact_phone' => 'contact_phone',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'contact_email', 'label' => 'Email'],
                    ['key' => 'contact_phone', 'label' => 'Phone'],
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Name' => 'name',
                    'Contact Person' => 'contact_person',
                    'Contact Email' => 'contact_email',
                    'Contact Phone' => 'contact_phone',
                    'Address' => 'address',
                ]
            ]
        ];
    }

    /**
     * Get export configuration for EventStaffAssignment model
     */
    public static function getEventStaffAssignmentConfig(): array
    {
        return [
            'searchable_fields' => ['event.title', 'staff.first_name', 'staff.last_name'],
            'sortable_fields' => ['assignment_date', 'status', 'created_at'],
            'default_sort' => 'created_at',
            'with_relations' => ['event', 'staff'],
            
            'csv' => [
                'headers' => ['Event Title', 'Staff Name', 'Assignment Date', 'Status', 'Role'],
                'fields' => [
                    ['field' => 'event.title', 'default' => 'N/A'],
                    [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'assignment_date', 'status', 'role'
                ],
                'filename' => 'event_staff_assignments.csv'
            ],
            
            'pdf' => [
                'title' => 'Event Staff Assignments',
                'document_title' => 'Event Staff Assignments',
                'filename' => 'event_staff_assignments.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'event_title' => ['field' => 'event.title', 'default' => 'N/A'],
                    'staff_name' => [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'assignment_date' => ['field' => 'assignment_date', 'default' => '-'],
                    'status' => 'status',
                    'role' => ['field' => 'role', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'event_title', 'label' => 'Event Title'],
                    ['key' => 'staff_name', 'label' => 'Staff Name'],
                    ['key' => 'assignment_date', 'label' => 'Assignment Date'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'role', 'label' => 'Role'],
                ]
            ],
            
            'print_current' => [
                'title' => 'Event Staff Assignments (Current View)',
                'document_title' => 'Event Staff Assignments (Current View)',
                'filename' => 'event_assignments_current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'event_title' => ['field' => 'event.title', 'default' => 'N/A'],
                    'staff_name' => [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'event_title', 'label' => 'Event Title'],
                    ['key' => 'staff_name', 'label' => 'Staff Name'],
                    ['key' => 'status', 'label' => 'Status'],
                ]
            ],
            
            'print_all' => [
                'title' => 'All Event Staff Assignments',
                'document_title' => 'All Event Staff Assignments',
                'filename' => 'event_assignments_all.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'assignment_date',
                'fields' => [
                    'event_title' => ['field' => 'event.title', 'default' => 'N/A'],
                    'staff_name' => [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'status' => 'status',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'event_title', 'label' => 'Event Title'],
                    ['key' => 'staff_name', 'label' => 'Staff Name'],
                    ['key' => 'status', 'label' => 'Status'],
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Event Title' => ['field' => 'event.title', 'default' => 'N/A'],
                    'Staff Name' => [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'Assignment Date' => ['field' => 'assignment_date', 'default' => '-'],
                    'Status' => 'status',
                    'Role' => ['field' => 'role', 'default' => '-'],
                ]
            ]
        ];
    }

    /**
     * Get export configuration for MarketingTask model
     */
    public static function getMarketingTaskConfig(): array
    {
        return [
            'searchable_fields' => ['task_name', 'description'],
            'sortable_fields' => ['task_name', 'priority', 'due_date', 'status', 'created_at'],
            'default_sort' => 'created_at',
            'with_relations' => ['assignedStaff', 'campaign'],
            
            'csv' => [
                'headers' => ['Task Name', 'Campaign', 'Assigned Staff', 'Priority', 'Status', 'Due Date'],
                'fields' => [
                    'task_name',
                    ['field' => 'campaign.campaign_name', 'default' => '-'],
                    ['field' => 'assignedStaff.full_name', 'default' => '-'],
                    'priority', 'status', 'due_date'
                ],
                'filename' => 'marketing_tasks.csv'
            ],
            
            'pdf' => [
                'title' => 'Marketing Tasks List',
                'document_title' => 'Marketing Tasks List',
                'filename' => 'marketing_tasks.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'task_name' => 'task_name',
                    'campaign_name' => ['field' => 'campaign.campaign_name', 'default' => '-'],
                    'assigned_staff' => ['field' => 'assignedStaff.full_name', 'default' => '-'],
                    'priority' => 'priority',
                    'status' => 'status',
                    'due_date' => ['field' => 'due_date', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'task_name', 'label' => 'Task Name'],
                    ['key' => 'campaign_name', 'label' => 'Campaign'],
                    ['key' => 'assigned_staff', 'label' => 'Assigned Staff'],
                    ['key' => 'priority', 'label' => 'Priority'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'due_date', 'label' => 'Due Date'],
                ]
            ],
            
            'print_current' => [
                'title' => 'Marketing Tasks (Current View)',
                'document_title' => 'Marketing Tasks (Current View)',
                'filename' => 'marketing_tasks_current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'task_name' => 'task_name',
                    'priority' => 'priority',
                    'status' => 'status',
                    'due_date' => ['field' => 'due_date', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'task_name', 'label' => 'Task Name'],
                    ['key' => 'priority', 'label' => 'Priority'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'due_date', 'label' => 'Due Date'],
                ]
            ],
            
            'print_all' => [
                'title' => 'All Marketing Tasks',
                'document_title' => 'All Marketing Tasks',
                'filename' => 'marketing_tasks_all.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'task_name',
                'fields' => [
                    'task_name' => 'task_name',
                    'priority' => 'priority',
                    'status' => 'status',
                    'due_date' => ['field' => 'due_date', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'task_name', 'label' => 'Task Name'],
                    ['key' => 'priority', 'label' => 'Priority'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'due_date', 'label' => 'Due Date'],
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Task Name' => 'task_name',
                    'Description' => ['field' => 'description', 'default' => '-'],
                    'Campaign' => ['field' => 'campaign.campaign_name', 'default' => '-'],
                    'Assigned Staff' => ['field' => 'assignedStaff.full_name', 'default' => '-'],
                    'Priority' => 'priority',
                    'Status' => 'status',
                    'Due Date' => ['field' => 'due_date', 'default' => '-'],
                ]
            ]
        ];
    }

    /**
     * Get export configuration for LeadSource model
     */
    public static function getLeadSourceConfig(): array
    {
        return [
            'searchable_fields' => ['source_name', 'description'],
            'sortable_fields' => ['source_name', 'status', 'created_at'],
            'default_sort' => 'source_name',
            'select_fields' => ['source_name', 'description', 'status', 'conversion_rate'],
            
            'csv' => [
                'headers' => ['Source Name', 'Description', 'Status', 'Conversion Rate'],
                'fields' => ['source_name', 'description', 'status', 'conversion_rate'],
                'filename' => 'lead_sources.csv'
            ],
            
            'pdf' => [
                'title' => 'Lead Sources List',
                'document_title' => 'Lead Sources List',
                'filename' => 'lead_sources.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'source_name' => 'source_name',
                    'description' => ['field' => 'description', 'default' => '-'],
                    'status' => 'status',
                    'conversion_rate' => [
                        'field' => 'conversion_rate',
                        'transform' => function($value, $model) {
                            return number_format($value ?? 0, 2) . '%';
                        }
                    ],
                ],
                'columns' => [
                    ['key' => 'source_name', 'label' => 'Source Name'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'conversion_rate', 'label' => 'Conversion Rate'],
                ]
            ],
            
            'print_current' => [
                'title' => 'Lead Sources (Current View)',
                'document_title' => 'Lead Sources (Current View)',
                'filename' => 'lead_sources_current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'source_name' => 'source_name',
                    'status' => 'status',
                    'conversion_rate' => [
                        'field' => 'conversion_rate',
                        'transform' => function($value, $model) {
                            return number_format($value ?? 0, 2) . '%';
                        }
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'source_name', 'label' => 'Source Name'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'conversion_rate', 'label' => 'Conversion Rate'],
                ]
            ],
            
            'print_all' => [
                'title' => 'All Lead Sources',
                'document_title' => 'All Lead Sources',
                'filename' => 'lead_sources_all.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'source_name',
                'fields' => [
                    'source_name' => 'source_name',
                    'status' => 'status',
                    'conversion_rate' => [
                        'field' => 'conversion_rate',
                        'transform' => function($value, $model) {
                            return number_format($value ?? 0, 2) . '%';
                        }
                    ],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'source_name', 'label' => 'Source Name'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'conversion_rate', 'label' => 'Conversion Rate'],
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Source Name' => 'source_name',
                    'Description' => ['field' => 'description', 'default' => '-'],
                    'Status' => 'status',
                    'Conversion Rate' => [
                        'field' => 'conversion_rate',
                        'transform' => function($value, $model) {
                            return number_format($value ?? 0, 2) . '%';
                        }
                    ],
                ]
            ]
        ];
    }

    public static function getSupplierConfig(): array
    {
        return [
            'searchable_fields' => ['name', 'contact_person', 'email'],
            'sortable_fields' => ['name', 'contact_person', 'email', 'phone', 'created_at'],
            'select_fields' => ['id', 'name', 'contact_person', 'email', 'phone', 'address', 'created_at', 'updated_at'],
            'relationships' => [],
            'csv_headers' => ['Name', 'Contact Person', 'Email', 'Phone', 'Address'],
            'csv_fields' => [
                'Name' => 'name',
                'Contact Person' => ['field' => 'contact_person', 'default' => '-'],
                'Email' => ['field' => 'email', 'default' => '-'],
                'Phone' => ['field' => 'phone', 'default' => '-'],
                'Address' => ['field' => 'address', 'default' => '-']
            ],
            'pdf_columns' => [
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'contact_person', 'label' => 'Contact Person'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'phone', 'label' => 'Phone'],
                ['key' => 'address', 'label' => 'Address']
            ],
            'pdf_fields' => [
                'name' => 'name',
                'contact_person' => ['field' => 'contact_person', 'default' => '-'],
                'email' => ['field' => 'email', 'default' => '-'],
                'phone' => ['field' => 'phone', 'default' => '-'],
                'address' => ['field' => 'address', 'default' => '-']
            ],
            'print_all_config' => [
                'title' => 'Suppliers List',
                'filename' => 'suppliers.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_current_config' => [
                'title' => 'Suppliers List (Current View)',
                'filename' => 'suppliers-current.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_single_config' => [
                'title' => 'Supplier Details',
                'filename_prefix' => 'supplier-',
                'paper' => 'a4',
                'orientation' => 'portrait',
                'fields' => [
                    'Name' => 'name',
                    'Contact Person' => ['field' => 'contact_person', 'default' => '-'],
                    'Email' => ['field' => 'email', 'default' => '-'],
                    'Phone' => ['field' => 'phone', 'default' => '-'],
                    'Address' => ['field' => 'address', 'default' => '-'],
                    'Created At' => [
                        'field' => 'created_at',
                        'transform' => function($value) {
                            return \Carbon\Carbon::parse($value)->format('M d, Y H:i');
                        }
                    ],
                    'Updated At' => [
                        'field' => 'updated_at',
                        'transform' => function($value) {
                            return \Carbon\Carbon::parse($value)->format('M d, Y H:i');
                        }
                    ]
                ]
            ]
        ];
    }

    public static function getInventoryItemConfig(): array
    {
        return [
            'searchable_fields' => ['name', 'serial_number', 'item_category', 'item_type'],
            'sortable_fields' => ['name', 'item_category', 'item_type', 'serial_number', 'purchase_date', 'status', 'created_at'],
            'select_fields' => ['id', 'name', 'description', 'item_category', 'item_type', 'serial_number', 'purchase_date', 'warranty_expiry', 'supplier_id', 'status', 'assigned_to_type', 'assigned_to_id', 'last_maintenance_date', 'next_maintenance_due', 'maintenance_schedule', 'notes', 'created_at', 'updated_at'],
            'relationships' => [],
            'csv_headers' => ['Name', 'Description', 'Category', 'Type', 'Serial Number', 'Purchase Date', 'Warranty Expiry', 'Status'],
            'csv_fields' => [
                'Name' => 'name',
                'Description' => ['field' => 'description', 'default' => '-'],
                'Category' => ['field' => 'item_category', 'default' => '-'],
                'Type' => ['field' => 'item_type', 'default' => '-'],
                'Serial Number' => ['field' => 'serial_number', 'default' => '-'],
                'Purchase Date' => [
                    'field' => 'purchase_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'Warranty Expiry' => [
                    'field' => 'warranty_expiry',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'Status' => 'status'
            ],
            'pdf_columns' => [
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'item_category', 'label' => 'Category'],
                ['key' => 'item_type', 'label' => 'Type'],
                ['key' => 'serial_number', 'label' => 'Serial Number'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'purchase_date', 'label' => 'Purchase Date'],
                ['key' => 'warranty_expiry', 'label' => 'Warranty Expiry']
            ],
            'pdf_fields' => [
                'name' => 'name',
                'item_category' => ['field' => 'item_category', 'default' => '-'],
                'item_type' => ['field' => 'item_type', 'default' => '-'],
                'serial_number' => ['field' => 'serial_number', 'default' => '-'],
                'status' => 'status',
                'purchase_date' => [
                    'field' => 'purchase_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'warranty_expiry' => [
                    'field' => 'warranty_expiry',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ]
            ],
            'print_all_config' => [
                'title' => 'Inventory Items List',
                'filename' => 'inventory-items.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_current_config' => [
                'title' => 'Inventory Items List (Current View)',
                'filename' => 'inventory-items-current.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_single_config' => [
                'title' => 'Inventory Item Details',
                'filename_prefix' => 'inventory-item-',
                'paper' => 'a4',
                'orientation' => 'portrait',
                'fields' => [
                    'Name' => 'name',
                    'Description' => ['field' => 'description', 'default' => '-'],
                    'Category' => ['field' => 'item_category', 'default' => '-'],
                    'Type' => ['field' => 'item_type', 'default' => '-'],
                    'Serial Number' => ['field' => 'serial_number', 'default' => '-'],
                    'Purchase Date' => [
                        'field' => 'purchase_date',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Warranty Expiry' => [
                        'field' => 'warranty_expiry',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Status' => 'status',
                    'Last Maintenance' => [
                        'field' => 'last_maintenance_date',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Next Maintenance Due' => [
                        'field' => 'next_maintenance_due',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Notes' => ['field' => 'notes', 'default' => '-']
                ]
            ]
        ];
    }

    public static function getInventoryMaintenanceRecordConfig(): array
    {
        return [
            'searchable_fields' => ['performed_by', 'description'],
            'sortable_fields' => ['scheduled_date', 'actual_date', 'performed_by', 'cost', 'status', 'created_at'],
            'select_fields' => ['id', 'item_id', 'scheduled_date', 'actual_date', 'performed_by', 'cost', 'description', 'next_due_date', 'status', 'created_at', 'updated_at'],
            'relationships' => ['item'],
            'csv_headers' => ['Item Name', 'Scheduled Date', 'Actual Date', 'Performed By', 'Cost', 'Description', 'Next Due Date', 'Status'],
            'csv_fields' => [
                'Item Name' => [
                    'field' => 'item.name',
                    'default' => 'N/A'
                ],
                'Scheduled Date' => [
                    'field' => 'scheduled_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'Actual Date' => [
                    'field' => 'actual_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'Performed By' => ['field' => 'performed_by', 'default' => '-'],
                'Cost' => [
                    'field' => 'cost',
                    'transform' => function($value) {
                        return $value ? number_format($value, 2) : '0.00';
                    }
                ],
                'Description' => ['field' => 'description', 'default' => '-'],
                'Next Due Date' => [
                    'field' => 'next_due_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'Status' => 'status'
            ],
            'pdf_columns' => [
                ['key' => 'item_name', 'label' => 'Item'],
                ['key' => 'scheduled_date', 'label' => 'Scheduled Date'],
                ['key' => 'actual_date', 'label' => 'Actual Date'],
                ['key' => 'performed_by', 'label' => 'Performed By'],
                ['key' => 'cost', 'label' => 'Cost'],
                ['key' => 'status', 'label' => 'Status']
            ],
            'pdf_fields' => [
                'item_name' => [
                    'field' => 'item.name',
                    'default' => 'N/A'
                ],
                'scheduled_date' => [
                    'field' => 'scheduled_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'actual_date' => [
                    'field' => 'actual_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'performed_by' => ['field' => 'performed_by', 'default' => '-'],
                'cost' => [
                    'field' => 'cost',
                    'transform' => function($value) {
                        return $value ? number_format($value, 2) : '0.00';
                    }
                ],
                'status' => 'status'
            ],
            'print_all_config' => [
                'title' => 'Inventory Maintenance Records List',
                'filename' => 'inventory-maintenance-records.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_current_config' => [
                'title' => 'Inventory Maintenance Records List (Current View)',
                'filename' => 'inventory-maintenance-records-current.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_single_config' => [
                'title' => 'Inventory Maintenance Record Details',
                'filename_prefix' => 'inventory-maintenance-record-',
                'paper' => 'a4',
                'orientation' => 'portrait',
                'fields' => [
                    'Item' => [
                        'field' => 'item.name',
                        'default' => 'N/A'
                    ],
                    'Scheduled Date' => [
                        'field' => 'scheduled_date',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Actual Date' => [
                        'field' => 'actual_date',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Performed By' => ['field' => 'performed_by', 'default' => '-'],
                    'Cost' => [
                        'field' => 'cost',
                        'transform' => function($value) {
                            return $value ? '$' . number_format($value, 2) : '$0.00';
                        }
                    ],
                    'Description' => ['field' => 'description', 'default' => '-'],
                    'Next Due Date' => [
                        'field' => 'next_due_date',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Status' => 'status'
                ]
            ]
        ];
    }

    public static function getInventoryRequestConfig(): array
    {
        return [
            'searchable_fields' => ['reason', 'status', 'priority'],
            'sortable_fields' => ['quantity_requested', 'quantity_approved', 'status', 'priority', 'needed_by_date', 'approved_at', 'fulfilled_at', 'created_at'],
            'select_fields' => ['id', 'requester_id', 'approver_id', 'item_id', 'quantity_requested', 'quantity_approved', 'reason', 'status', 'priority', 'needed_by_date', 'approved_at', 'fulfilled_at', 'created_at', 'updated_at'],
            'relationships' => ['requester', 'approver', 'item'],
            'csv_headers' => ['Item Name', 'Requester', 'Approver', 'Quantity Requested', 'Quantity Approved', 'Status', 'Priority', 'Reason', 'Needed By Date'],
            'csv_fields' => [
                'Item Name' => [
                    'field' => 'item.name',
                    'default' => 'N/A'
                ],
                'Requester' => [
                    'field' => 'requester.first_name',
                    'transform' => function($value, $model) {
                        return ($model->requester->first_name ?? '') . ' ' . ($model->requester->last_name ?? '');
                    }
                ],
                'Approver' => [
                    'field' => 'approver.first_name',
                    'transform' => function($value, $model) {
                        return $model->approver ? (($model->approver->first_name ?? '') . ' ' . ($model->approver->last_name ?? '')) : '-';
                    }
                ],
                'Quantity Requested' => 'quantity_requested',
                'Quantity Approved' => ['field' => 'quantity_approved', 'default' => '-'],
                'Status' => 'status',
                'Priority' => 'priority',
                'Reason' => ['field' => 'reason', 'default' => '-'],
                'Needed By Date' => [
                    'field' => 'needed_by_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ]
            ],
            'pdf_columns' => [
                ['key' => 'item_name', 'label' => 'Item'],
                ['key' => 'requester_name', 'label' => 'Requester'],
                ['key' => 'quantity_requested', 'label' => 'Qty Requested'],
                ['key' => 'quantity_approved', 'label' => 'Qty Approved'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'priority', 'label' => 'Priority'],
                ['key' => 'needed_by_date', 'label' => 'Needed By']
            ],
            'pdf_fields' => [
                'item_name' => [
                    'field' => 'item.name',
                    'default' => 'N/A'
                ],
                'requester_name' => [
                    'field' => 'requester.first_name',
                    'transform' => function($value, $model) {
                        return ($model->requester->first_name ?? '') . ' ' . ($model->requester->last_name ?? '');
                    }
                ],
                'quantity_requested' => 'quantity_requested',
                'quantity_approved' => ['field' => 'quantity_approved', 'default' => '-'],
                'status' => 'status',
                'priority' => 'priority',
                'needed_by_date' => [
                    'field' => 'needed_by_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ]
            ],
            'print_all_config' => [
                'title' => 'Inventory Requests List',
                'filename' => 'inventory-requests.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_current_config' => [
                'title' => 'Inventory Requests List (Current View)',
                'filename' => 'inventory-requests-current.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_single_config' => [
                'title' => 'Inventory Request Details',
                'filename_prefix' => 'inventory-request-',
                'paper' => 'a4',
                'orientation' => 'portrait',
                'fields' => [
                    'Item' => [
                        'field' => 'item.name',
                        'default' => 'N/A'
                    ],
                    'Requester' => [
                        'field' => 'requester.first_name',
                        'transform' => function($value, $model) {
                            return ($model->requester->first_name ?? '') . ' ' . ($model->requester->last_name ?? '');
                        }
                    ],
                    'Approver' => [
                        'field' => 'approver.first_name',
                        'transform' => function($value, $model) {
                            return $model->approver ? (($model->approver->first_name ?? '') . ' ' . ($model->approver->last_name ?? '')) : '-';
                        }
                    ],
                    'Quantity Requested' => 'quantity_requested',
                    'Quantity Approved' => ['field' => 'quantity_approved', 'default' => '-'],
                    'Status' => 'status',
                    'Priority' => 'priority',
                    'Reason' => ['field' => 'reason', 'default' => '-'],
                    'Needed By Date' => [
                        'field' => 'needed_by_date',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Approved At' => [
                        'field' => 'approved_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y H:i') : '-';
                        }
                    ],
                    'Fulfilled At' => [
                        'field' => 'fulfilled_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y H:i') : '-';
                        }
                    ]
                ]
            ]
        ];
    }

    public static function getInventoryTransactionConfig(): array
    {
        return [
            'searchable_fields' => ['transaction_type', 'from_location', 'to_location', 'notes'],
            'sortable_fields' => ['quantity', 'transaction_type', 'from_location', 'to_location', 'created_at'],
            'select_fields' => ['id', 'item_id', 'request_id', 'from_location', 'to_location', 'from_assigned_to_type', 'from_assigned_to_id', 'to_assigned_to_type', 'to_assigned_to_id', 'quantity', 'transaction_type', 'performed_by_id', 'notes', 'created_at', 'updated_at'],
            'relationships' => ['item', 'performedBy'],
            'csv_headers' => ['Item Name', 'Transaction Type', 'Quantity', 'From Location', 'To Location', 'Performed By', 'Notes', 'Date'],
            'csv_fields' => [
                'Item Name' => [
                    'field' => 'item.name',
                    'default' => 'N/A'
                ],
                'Transaction Type' => 'transaction_type',
                'Quantity' => 'quantity',
                'From Location' => ['field' => 'from_location', 'default' => '-'],
                'To Location' => ['field' => 'to_location', 'default' => '-'],
                'Performed By' => [
                    'field' => 'performedBy.first_name',
                    'transform' => function($value, $model) {
                        return ($model->performedBy->first_name ?? '') . ' ' . ($model->performedBy->last_name ?? '');
                    }
                ],
                'Notes' => ['field' => 'notes', 'default' => '-'],
                'Date' => [
                    'field' => 'created_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-';
                    }
                ]
            ],
            'pdf_columns' => [
                ['key' => 'item_name', 'label' => 'Item'],
                ['key' => 'transaction_type', 'label' => 'Type'],
                ['key' => 'quantity', 'label' => 'Quantity'],
                ['key' => 'from_location', 'label' => 'From'],
                ['key' => 'to_location', 'label' => 'To'],
                ['key' => 'performed_by', 'label' => 'Performed By'],
                ['key' => 'created_at', 'label' => 'Date']
            ],
            'pdf_fields' => [
                'item_name' => [
                    'field' => 'item.name',
                    'default' => 'N/A'
                ],
                'transaction_type' => 'transaction_type',
                'quantity' => 'quantity',
                'from_location' => ['field' => 'from_location', 'default' => '-'],
                'to_location' => ['field' => 'to_location', 'default' => '-'],
                'performed_by' => [
                    'field' => 'performedBy.first_name',
                    'transform' => function($value, $model) {
                        return ($model->performedBy->first_name ?? '') . ' ' . ($model->performedBy->last_name ?? '');
                    }
                ],
                'created_at' => [
                    'field' => 'created_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                    }
                ]
            ],
            'print_all_config' => [
                'title' => 'Inventory Transactions List',
                'filename' => 'inventory-transactions.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_current_config' => [
                'title' => 'Inventory Transactions List (Current View)',
                'filename' => 'inventory-transactions-current.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_single_config' => [
                'title' => 'Inventory Transaction Details',
                'filename_prefix' => 'inventory-transaction-',
                'paper' => 'a4',
                'orientation' => 'portrait',
                'fields' => [
                    'Item' => [
                        'field' => 'item.name',
                        'default' => 'N/A'
                    ],
                    'Transaction Type' => 'transaction_type',
                    'Quantity' => 'quantity',
                    'From Location' => ['field' => 'from_location', 'default' => '-'],
                    'To Location' => ['field' => 'to_location', 'default' => '-'],
                    'From Assigned To Type' => ['field' => 'from_assigned_to_type', 'default' => '-'],
                    'From Assigned To ID' => ['field' => 'from_assigned_to_id', 'default' => '-'],
                    'To Assigned To Type' => ['field' => 'to_assigned_to_type', 'default' => '-'],
                    'To Assigned To ID' => ['field' => 'to_assigned_to_id', 'default' => '-'],
                    'Performed By' => [
                        'field' => 'performedBy.first_name',
                        'transform' => function($value, $model) {
                            return ($model->performedBy->first_name ?? '') . ' ' . ($model->performedBy->last_name ?? '');
                        }
                    ],
                    'Notes' => ['field' => 'notes', 'default' => '-'],
                    'Request ID' => ['field' => 'request_id', 'default' => '-'],
                    'Date' => [
                        'field' => 'created_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y H:i') : '-';
                        }
                    ]
                ]
            ]
        ];
    }

    public static function getMarketingLeadConfig(): array
    {
        return [
            'searchable_fields' => ['first_name', 'last_name', 'email', 'lead_code', 'phone_number'],
            'sortable_fields' => ['first_name', 'last_name', 'email', 'lead_code', 'status', 'created_at'],
            'select_fields' => ['id', 'lead_code', 'first_name', 'last_name', 'email', 'phone_number', 'status', 'source_campaign_id', 'landing_page_id', 'assigned_staff_id', 'converted_patient_id', 'created_at', 'updated_at'],
            'relationships' => ['sourceCampaign', 'landingPage', 'assignedStaff', 'convertedPatient'],
            'csv_headers' => ['Lead Code', 'Name', 'Email', 'Phone', 'Status', 'Source Campaign', 'Landing Page', 'Assigned Staff', 'Converted Patient', 'Created Date'],
            'csv_fields' => [
                'Lead Code' => 'lead_code',
                'Name' => [
                    'field' => 'first_name',
                    'transform' => function($value, $model) {
                        return ($model->first_name ?? '') . ' ' . ($model->last_name ?? '');
                    }
                ],
                'Email' => 'email',
                'Phone' => ['field' => 'phone_number', 'default' => '-'],
                'Status' => 'status',
                'Source Campaign' => [
                    'field' => 'sourceCampaign.name',
                    'default' => 'N/A'
                ],
                'Landing Page' => [
                    'field' => 'landingPage.name',
                    'default' => 'N/A'
                ],
                'Assigned Staff' => [
                    'field' => 'assignedStaff.first_name',
                    'transform' => function($value, $model) {
                        return $model->assignedStaff ? (($model->assignedStaff->first_name ?? '') . ' ' . ($model->assignedStaff->last_name ?? '')) : 'N/A';
                    }
                ],
                'Converted Patient' => [
                    'field' => 'convertedPatient.full_name',
                    'default' => '-'
                ],
                'Created Date' => [
                    'field' => 'created_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ]
            ],
            'pdf_columns' => [
                ['key' => 'lead_code', 'label' => 'Lead Code'],
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'phone', 'label' => 'Phone'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'source_campaign', 'label' => 'Source Campaign'],
                ['key' => 'landing_page', 'label' => 'Landing Page'],
                ['key' => 'assigned_staff', 'label' => 'Assigned Staff']
            ],
            'pdf_fields' => [
                'lead_code' => 'lead_code',
                'name' => [
                    'field' => 'first_name',
                    'transform' => function($value, $model) {
                        return ($model->first_name ?? '') . ' ' . ($model->last_name ?? '');
                    }
                ],
                'email' => 'email',
                'phone' => ['field' => 'phone_number', 'default' => '-'],
                'status' => 'status',
                'source_campaign' => [
                    'field' => 'sourceCampaign.name',
                    'default' => 'N/A'
                ],
                'landing_page' => [
                    'field' => 'landingPage.name',
                    'default' => 'N/A'
                ],
                'assigned_staff' => [
                    'field' => 'assignedStaff.first_name',
                    'transform' => function($value, $model) {
                        return $model->assignedStaff ? (($model->assignedStaff->first_name ?? '') . ' ' . ($model->assignedStaff->last_name ?? '')) : 'N/A';
                    }
                ]
            ],
            'print_all_config' => [
                'title' => 'Marketing Leads List',
                'filename' => 'marketing-leads.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_current_config' => [
                'title' => 'Marketing Leads List (Current View)',
                'filename' => 'marketing-leads-current.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_single_config' => [
                'title' => 'Marketing Lead Details',
                'filename_prefix' => 'marketing-lead-',
                'paper' => 'a4',
                'orientation' => 'portrait',
                'fields' => [
                    'Lead Code' => 'lead_code',
                    'First Name' => 'first_name',
                    'Last Name' => 'last_name',
                    'Email' => 'email',
                    'Phone Number' => ['field' => 'phone_number', 'default' => '-'],
                    'Status' => 'status',
                    'Source Campaign' => [
                        'field' => 'sourceCampaign.name',
                        'default' => 'N/A'
                    ],
                    'Landing Page' => [
                        'field' => 'landingPage.name',
                        'default' => 'N/A'
                    ],
                    'Assigned Staff' => [
                        'field' => 'assignedStaff.first_name',
                        'transform' => function($value, $model) {
                            return $model->assignedStaff ? (($model->assignedStaff->first_name ?? '') . ' ' . ($model->assignedStaff->last_name ?? '')) : 'N/A';
                        }
                    ],
                    'Converted Patient' => [
                        'field' => 'convertedPatient.full_name',
                        'default' => '-'
                    ],
                    'Created Date' => [
                        'field' => 'created_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y H:i') : '-';
                        }
                    ]
                ]
            ]
        ];
    }

    public static function getTaskDelegationConfig(): array
    {
        return [
            'searchable_fields' => ['title', 'notes', 'status'],
            'sortable_fields' => ['title', 'due_date', 'status', 'created_at'],
            'select_fields' => ['id', 'title', 'assigned_to', 'due_date', 'status', 'notes', 'created_at', 'updated_at'],
            'relationships' => ['assignee'],
            'csv_headers' => ['Title', 'Assigned To', 'Due Date', 'Status', 'Notes', 'Created Date'],
            'csv_fields' => [
                'Title' => 'title',
                'Assigned To' => [
                    'field' => 'assignee.first_name',
                    'transform' => function($value, $model) {
                        return ($model->assignee->first_name ?? '') . ' ' . ($model->assignee->last_name ?? '');
                    }
                ],
                'Due Date' => [
                    'field' => 'due_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'Status' => 'status',
                'Notes' => ['field' => 'notes', 'default' => '-'],
                'Created Date' => [
                    'field' => 'created_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ]
            ],
            'pdf_columns' => [
                ['key' => 'title', 'label' => 'Title'],
                ['key' => 'assigned_to', 'label' => 'Assigned To'],
                ['key' => 'due_date', 'label' => 'Due Date'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'notes', 'label' => 'Notes']
            ],
            'pdf_fields' => [
                'title' => 'title',
                'assigned_to' => [
                    'field' => 'assignee.first_name',
                    'transform' => function($value, $model) {
                        return ($model->assignee->first_name ?? '') . ' ' . ($model->assignee->last_name ?? '');
                    }
                ],
                'due_date' => [
                    'field' => 'due_date',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'status' => 'status',
                'notes' => ['field' => 'notes', 'default' => '-']
            ],
            'print_all_config' => [
                'title' => 'Task Delegations List',
                'filename' => 'task-delegations.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_current_config' => [
                'title' => 'Task Delegations List (Current View)',
                'filename' => 'task-delegations-current.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_single_config' => [
                'title' => 'Task Delegation Details',
                'filename_prefix' => 'task-delegation-',
                'paper' => 'a4',
                'orientation' => 'portrait',
                'fields' => [
                    'Title' => 'title',
                    'Assigned To' => [
                        'field' => 'assignee.first_name',
                        'transform' => function($value, $model) {
                            return ($model->assignee->first_name ?? '') . ' ' . ($model->assignee->last_name ?? '');
                        }
                    ],
                    'Due Date' => [
                        'field' => 'due_date',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Status' => 'status',
                    'Notes' => ['field' => 'notes', 'default' => '-'],
                    'Created Date' => [
                        'field' => 'created_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y H:i') : '-';
                        }
                    ],
                    'Updated Date' => [
                        'field' => 'updated_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y H:i') : '-';
                        }
                    ]
                ]
            ]
        ];
    }

    public static function getInsuranceClaimConfig(): array
    {
        return [
            'searchable_fields' => ['claim_status', 'receipt_number', 'denial_reason'],
            'sortable_fields' => ['claim_status', 'coverage_amount', 'paid_amount', 'submitted_at', 'processed_at', 'created_at'],
            'select_fields' => ['id', 'patient_id', 'invoice_id', 'insurance_company_id', 'policy_id', 'claim_status', 'coverage_amount', 'paid_amount', 'submitted_at', 'processed_at', 'payment_due_date', 'payment_received_at', 'payment_method', 'reimbursement_required', 'receipt_number', 'is_pre_authorized', 'pre_authorization_code', 'denial_reason', 'created_at', 'updated_at'],
            'relationships' => ['patient', 'invoice', 'insuranceCompany', 'policy'],
            'csv_headers' => ['Patient', 'Claim Status', 'Coverage Amount', 'Paid Amount', 'Receipt Number', 'Submitted Date', 'Processed Date', 'Payment Method', 'Pre-Authorized'],
            'csv_fields' => [
                'Patient' => [
                    'field' => 'patient.full_name',
                    'default' => 'N/A'
                ],
                'Claim Status' => 'claim_status',
                'Coverage Amount' => [
                    'field' => 'coverage_amount',
                    'transform' => function($value) {
                        return $value ? number_format($value, 2) : '0.00';
                    }
                ],
                'Paid Amount' => [
                    'field' => 'paid_amount',
                    'transform' => function($value) {
                        return $value ? number_format($value, 2) : '0.00';
                    }
                ],
                'Receipt Number' => ['field' => 'receipt_number', 'default' => '-'],
                'Submitted Date' => [
                    'field' => 'submitted_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'Processed Date' => [
                    'field' => 'processed_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ],
                'Payment Method' => ['field' => 'payment_method', 'default' => '-'],
                'Pre-Authorized' => [
                    'field' => 'is_pre_authorized',
                    'transform' => function($value) {
                        return $value ? 'Yes' : 'No';
                    }
                ]
            ],
            'pdf_columns' => [
                ['key' => 'patient_name', 'label' => 'Patient'],
                ['key' => 'claim_status', 'label' => 'Status'],
                ['key' => 'coverage_amount', 'label' => 'Coverage'],
                ['key' => 'paid_amount', 'label' => 'Paid'],
                ['key' => 'receipt_number', 'label' => 'Receipt #'],
                ['key' => 'submitted_at', 'label' => 'Submitted'],
                ['key' => 'processed_at', 'label' => 'Processed']
            ],
            'pdf_fields' => [
                'patient_name' => [
                    'field' => 'patient.full_name',
                    'default' => 'N/A'
                ],
                'claim_status' => 'claim_status',
                'coverage_amount' => [
                    'field' => 'coverage_amount',
                    'transform' => function($value) {
                        return $value ? '$' . number_format($value, 2) : '$0.00';
                    }
                ],
                'paid_amount' => [
                    'field' => 'paid_amount',
                    'transform' => function($value) {
                        return $value ? '$' . number_format($value, 2) : '$0.00';
                    }
                ],
                'receipt_number' => ['field' => 'receipt_number', 'default' => '-'],
                'submitted_at' => [
                    'field' => 'submitted_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                    }
                ],
                'processed_at' => [
                    'field' => 'processed_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                    }
                ]
            ],
            'print_all_config' => [
                'title' => 'Insurance Claims List',
                'filename' => 'insurance-claims.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_current_config' => [
                'title' => 'Insurance Claims List (Current View)',
                'filename' => 'insurance-claims-current.pdf',
                'paper' => 'a4',
                'orientation' => 'landscape'
            ],
            'print_single_config' => [
                'title' => 'Insurance Claim Details',
                'filename_prefix' => 'insurance-claim-',
                'paper' => 'a4',
                'orientation' => 'portrait',
                'fields' => [
                    'Patient' => [
                        'field' => 'patient.full_name',
                        'default' => 'N/A'
                    ],
                    'Claim Status' => 'claim_status',
                    'Coverage Amount' => [
                        'field' => 'coverage_amount',
                        'transform' => function($value) {
                            return $value ? '$' . number_format($value, 2) : '$0.00';
                        }
                    ],
                    'Paid Amount' => [
                        'field' => 'paid_amount',
                        'transform' => function($value) {
                            return $value ? '$' . number_format($value, 2) : '$0.00';
                        }
                    ],
                    'Receipt Number' => ['field' => 'receipt_number', 'default' => '-'],
                    'Payment Method' => ['field' => 'payment_method', 'default' => '-'],
                    'Pre-Authorized' => [
                        'field' => 'is_pre_authorized',
                        'transform' => function($value) {
                            return $value ? 'Yes' : 'No';
                        }
                    ],
                    'Pre-Authorization Code' => ['field' => 'pre_authorization_code', 'default' => '-'],
                    'Reimbursement Required' => [
                        'field' => 'reimbursement_required',
                        'transform' => function($value) {
                            return $value ? 'Yes' : 'No';
                        }
                    ],
                    'Submitted Date' => [
                        'field' => 'submitted_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Processed Date' => [
                        'field' => 'processed_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Payment Due Date' => [
                        'field' => 'payment_due_date',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Payment Received Date' => [
                        'field' => 'payment_received_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-';
                        }
                    ],
                    'Denial Reason' => ['field' => 'denial_reason', 'default' => '-']
                ]
            ]
        ];
    }

    public static function getVisitServiceConfig(): array
    {
        return [
            'searchable_fields' => ['service_description', 'visit_notes', 'status'],
            'sortable_fields' => ['scheduled_at', 'status', 'cost', 'created_at'],
            'select_fields' => ['id', 'patient_id', 'staff_id', 'assignment_id', 'scheduled_at', 'status', 'visit_notes', 'service_description', 'cost', 'prescription_file', 'vitals_file', 'created_at', 'updated_at'],
            'relationships' => ['patient', 'staff', 'assignment'],
            'csv_headers' => ['Patient Name', 'Staff Name', 'Scheduled At', 'Status', 'Service Description', 'Cost', 'Visit Notes', 'Created Date'],
            'csv_fields' => [
                'Patient Name' => [
                    'field' => 'patient.full_name',
                    'default' => 'N/A'
                ],
                'Staff Name' => [
                    'field' => 'staff.first_name',
                    'transform' => function($value, $model) {
                        return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                    }
                ],
                'Scheduled At' => [
                    'field' => 'scheduled_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-';
                    }
                ],
                'Status' => 'status',
                'Service Description' => ['field' => 'service_description', 'default' => '-'],
                'Cost' => [
                    'field' => 'cost',
                    'transform' => function($value) {
                        return $value ? '$' . number_format($value, 2) : '$0.00';
                    }
                ],
                'Visit Notes' => ['field' => 'visit_notes', 'default' => '-'],
                'Created Date' => [
                    'field' => 'created_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
                    }
                ]
            ],
            'pdf_columns' => [
                ['key' => 'patient_name', 'label' => 'Patient'],
                ['key' => 'staff_name', 'label' => 'Staff'],
                ['key' => 'scheduled_at', 'label' => 'Scheduled'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'service_description', 'label' => 'Service'],
                ['key' => 'cost', 'label' => 'Cost']
            ],
            'pdf_fields' => [
                'patient_name' => [
                    'field' => 'patient.full_name',
                    'default' => 'N/A'
                ],
                'staff_name' => [
                    'field' => 'staff.first_name',
                    'transform' => function($value, $model) {
                        return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                    }
                ],
                'scheduled_at' => [
                    'field' => 'scheduled_at',
                    'transform' => function($value) {
                        return $value ? \Carbon\Carbon::parse($value)->format('M d, Y H:i') : '-';
                    }
                ],
                'status' => 'status',
                'service_description' => ['field' => 'service_description', 'default' => '-'],
                'cost' => [
                    'field' => 'cost',
                    'transform' => function($value) {
                        return $value ? '$' . number_format($value, 2) : '$0.00';
                'orientation' => 'landscape'
            ],
            'print_single_config' => [
                'title' => 'Visit Service Details',
                'filename_prefix' => 'visit-service-',
                'paper' => 'a4',
                'orientation' => 'portrait',
                'fields' => [
                    'Patient' => [
                        'field' => 'patient.full_name',
                        'default' => 'N/A'
                    ],
                    'Staff' => [
                        'field' => 'staff.first_name',
                        'transform' => function($value, $model) {
                            return ($model->staff->first_name ?? '') . ' ' . ($model->staff->last_name ?? '');
                        }
                    ],
                    'Scheduled At' => [
                        'field' => 'scheduled_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y H:i') : '-';
                        }
                    ],
                    'Status' => 'status',
                    'Service Description' => ['field' => 'service_description', 'default' => '-'],
                    'Cost' => [
                        'field' => 'cost',
                        'transform' => function($value) {
                            return $value ? '$' . number_format($value, 2) : '$0.00';
                        }
                    ],
                    'Visit Notes' => ['field' => 'visit_notes', 'default' => '-'],
                    'Prescription File' => ['field' => 'prescription_file', 'default' => '-'],
                    'Vitals File' => ['field' => 'vitals_file', 'default' => '-'],
                    'Created Date' => [
                        'field' => 'created_at',
                        'transform' => function($value) {
                            return $value ? \Carbon\Carbon::parse($value)->format('M d, Y H:i') : '-';
                        }
                    ]
                ]
            ]
        ];
    }

    private static $exchangeRateConfig = null;

    public static function getExchangeRateConfig(): array
    {
        if (self::$exchangeRateConfig === null) {
            self::$exchangeRateConfig = [
                'searchable_fields' => ['currency_code', 'source'],
                'sortable_fields' => ['currency_code', 'rate_to_etb', 'source', 'date_effective', 'created_at'],
                'csv_headers' => ['Currency Code', 'Rate to ETB', 'Source', 'Date Effective'],
                'csv_fields' => ['currency_code', 'rate_to_etb', 'source', 'date_effective'],
                'pdf_columns' => [
                    ['key' => 'currency_code', 'label' => 'Currency Code', 'printWidth' => '20%'],
                    ['key' => 'rate_to_etb', 'label' => 'Rate to ETB', 'printWidth' => '25%'],
                    ['key' => 'source', 'label' => 'Source', 'printWidth' => '30%'],
                    ['key' => 'date_effective', 'label' => 'Date Effective', 'printWidth' => '25%'],
                ],
                'field_transformations' => [
                    'rate_to_etb' => fn($value) => number_format($value, 4),
                    'date_effective' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : 'N/A',
                ],
                'print_layout' => [
                    'title' => 'Exchange Rates List',
                    'document_title' => 'Exchange Rates List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'exchange_rates',
                ],
                'single_print_layout' => [
                    'title' => 'Exchange Rate Details',
                    'document_title' => 'Exchange Rate Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'exchange_rate',
                    'fields' => [
                        ['label' => 'Currency Code', 'key' => 'currency_code'],
                        ['label' => 'Rate to ETB', 'key' => 'rate_to_etb', 'transform' => fn($value) => number_format($value, 4)],
                        ['label' => 'Source', 'key' => 'source'],
                        ['label' => 'Date Effective', 'key' => 'date_effective', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : 'N/A'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => [],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
            ];
        }

        return self::$exchangeRateConfig;
    }

    private static $ethiopianCalendarDayConfig = null;

    public static function getEthiopianCalendarDayConfig(): array
    {
        if (self::$ethiopianCalendarDayConfig === null) {
            self::$ethiopianCalendarDayConfig = [
                'searchable_fields' => ['description', 'ethiopian_date'],
                'sortable_fields' => ['gregorian_date', 'ethiopian_date', 'description', 'is_holiday', 'region', 'created_at'],
                'csv_headers' => ['Gregorian Date', 'Ethiopian Date', 'Description', 'Is Holiday', 'Region'],
                'csv_fields' => ['gregorian_date', 'ethiopian_date', 'description', 'is_holiday', 'region'],
                'pdf_columns' => [
                    ['key' => 'gregorian_date', 'label' => 'Gregorian Date', 'printWidth' => '20%'],
                    ['key' => 'ethiopian_date', 'label' => 'Ethiopian Date', 'printWidth' => '20%'],
                    ['key' => 'description', 'label' => 'Description', 'printWidth' => '30%'],
                    ['key' => 'is_holiday', 'label' => 'Is Holiday', 'printWidth' => '15%'],
                    ['key' => 'region', 'label' => 'Region', 'printWidth' => '15%'],
                ],
                'field_transformations' => [
                    'gregorian_date' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : 'N/A',
                    'is_holiday' => fn($value) => $value ? 'Yes' : 'No',
                ],
                'print_layout' => [
                    'title' => 'Ethiopian Calendar Days List',
                    'document_title' => 'Ethiopian Calendar Days List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'ethiopian_calendar_days',
                ],
                'single_print_layout' => [
                    'title' => 'Ethiopian Calendar Day Details',
                    'document_title' => 'Ethiopian Calendar Day Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'ethiopian_calendar_day',
                    'fields' => [
                        ['label' => 'Gregorian Date', 'key' => 'gregorian_date', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : 'N/A'],
                        ['label' => 'Ethiopian Date', 'key' => 'ethiopian_date'],
                        ['label' => 'Description', 'key' => 'description'],
                        ['label' => 'Is Holiday', 'key' => 'is_holiday', 'transform' => fn($value) => $value ? 'Yes' : 'No'],
                        ['label' => 'Region', 'key' => 'region'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => [],
                'default_sort' => ['gregorian_date', 'desc'],
                'per_page' => 5,
            ];
        }

        return self::$ethiopianCalendarDayConfig;
    }

    private static $marketingBudgetConfig = null;

    public static function getMarketingBudgetConfig(): array
    {
        if (self::$marketingBudgetConfig === null) {
            self::$marketingBudgetConfig = [
                'searchable_fields' => ['budget_name'],
                'sortable_fields' => ['budget_name', 'allocated_amount', 'spent_amount', 'period_start', 'period_end', 'status', 'created_at'],
                'csv_headers' => ['Budget Name', 'Campaign', 'Platform', 'Allocated Amount', 'Spent Amount', 'Period Start', 'Period End', 'Status'],
                'csv_fields' => ['budget_name', 'campaign_name', 'platform_name', 'allocated_amount', 'spent_amount', 'period_start', 'period_end', 'status'],
                'pdf_columns' => [
                    ['key' => 'budget_name', 'label' => 'Budget Name', 'printWidth' => '15%'],
                    ['key' => 'campaign_name', 'label' => 'Campaign', 'printWidth' => '15%'],
                    ['key' => 'platform_name', 'label' => 'Platform', 'printWidth' => '12%'],
                    ['key' => 'allocated_amount', 'label' => 'Allocated', 'printWidth' => '12%'],
                    ['key' => 'spent_amount', 'label' => 'Spent', 'printWidth' => '12%'],
                    ['key' => 'period_start', 'label' => 'Start Date', 'printWidth' => '12%'],
                    ['key' => 'period_end', 'label' => 'End Date', 'printWidth' => '12%'],
                    ['key' => 'status', 'label' => 'Status', 'printWidth' => '10%'],
                ],
                'field_transformations' => [
                    'campaign_name' => fn($model) => $model->campaign->campaign_name ?? '-',
                    'platform_name' => fn($model) => $model->platform->name ?? '-',
                    'allocated_amount' => fn($value) => '$' . number_format($value, 2),
                    'spent_amount' => fn($value) => '$' . number_format($value, 2),
                    'period_start' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : 'N/A',
                    'period_end' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-',
                ],
                'print_layout' => [
                    'title' => 'Marketing Budgets List',
                    'document_title' => 'Marketing Budgets List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'marketing_budgets',
                ],
                'single_print_layout' => [
                    'title' => 'Marketing Budget Details',
                    'document_title' => 'Marketing Budget Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'marketing_budget',
                    'fields' => [
                        ['label' => 'Budget Name', 'key' => 'budget_name'],
                        ['label' => 'Campaign', 'key' => 'campaign_name', 'transform' => fn($model) => $model->campaign->campaign_name ?? '-'],
                        ['label' => 'Platform', 'key' => 'platform_name', 'transform' => fn($model) => $model->platform->name ?? '-'],
                        ['label' => 'Allocated Amount', 'key' => 'allocated_amount', 'transform' => fn($value) => '$' . number_format($value, 2)],
                        ['label' => 'Spent Amount', 'key' => 'spent_amount', 'transform' => fn($value) => '$' . number_format($value, 2)],
                        ['label' => 'Period Start', 'key' => 'period_start', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : 'N/A'],
                        ['label' => 'Period End', 'key' => 'period_end', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : '-'],
                        ['label' => 'Status', 'key' => 'status'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => ['campaign', 'platform'],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
                'additional_filters' => [
                    'campaign_id' => 'campaign_id',
                    'platform_id' => 'platform_id',
                    'status' => 'status',
                    'period_start' => 'period_start',
                    'period_end' => 'period_end',
                ],
            ];
        }

        return self::$marketingBudgetConfig;
    }

    private static $eventParticipantConfig = null;

    public static function getEventParticipantConfig(): array
    {
        if (self::$eventParticipantConfig === null) {
            self::$eventParticipantConfig = [
                'searchable_fields' => ['status'],
                'sortable_fields' => ['event_id', 'patient_id', 'status', 'created_at'],
                'csv_headers' => ['Event ID', 'Patient ID', 'Status'],
                'csv_fields' => ['event_id', 'patient_id', 'status'],
                'pdf_columns' => [
                    ['key' => 'event_id', 'label' => 'Event ID', 'printWidth' => '30%'],
                    ['key' => 'patient_id', 'label' => 'Patient ID', 'printWidth' => '30%'],
                    ['key' => 'status', 'label' => 'Status', 'printWidth' => '40%'],
                ],
                'field_transformations' => [
                    'status' => fn($value) => ucfirst($value),
                ],
                'print_layout' => [
                    'title' => 'Event Participants List',
                    'document_title' => 'Event Participants List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'event_participants',
                ],
                'single_print_layout' => [
                    'title' => 'Event Participant Details',
                    'document_title' => 'Event Participant Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'event_participant',
                    'fields' => [
                        ['label' => 'Event ID', 'key' => 'event_id'],
                        ['label' => 'Patient ID', 'key' => 'patient_id'],
                        ['label' => 'Status', 'key' => 'status', 'transform' => fn($value) => ucfirst($value)],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => [],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
            ];
        }

        return self::$eventParticipantConfig;
    }

    private static $corporateClientConfig = null;

    public static function getCorporateClientConfig(): array
    {
        if (self::$corporateClientConfig === null) {
            self::$corporateClientConfig = [
                'searchable_fields' => ['organization_name', 'contact_email'],
                'sortable_fields' => ['organization_name', 'contact_person', 'contact_email', 'contact_phone', 'tin_number', 'created_at'],
                'csv_headers' => ['Organization Name', 'Contact Person', 'Contact Email', 'Contact Phone', 'TIN Number', 'Trade License Number', 'Address'],
                'csv_fields' => ['organization_name', 'contact_person', 'contact_email', 'contact_phone', 'tin_number', 'trade_license_number', 'address'],
                'pdf_columns' => [
                    ['key' => 'organization_name', 'label' => 'Organization Name', 'printWidth' => '20%'],
                    ['key' => 'contact_person', 'label' => 'Contact Person', 'printWidth' => '15%'],
                    ['key' => 'contact_email', 'label' => 'Contact Email', 'printWidth' => '20%'],
                    ['key' => 'contact_phone', 'label' => 'Contact Phone', 'printWidth' => '15%'],
                    ['key' => 'tin_number', 'label' => 'TIN Number', 'printWidth' => '15%'],
                    ['key' => 'trade_license_number', 'label' => 'Trade License', 'printWidth' => '15%'],
                ],
                'field_transformations' => [
                    'contact_person' => fn($value) => $value ?: '-',
                    'contact_email' => fn($value) => $value ?: '-',
                    'contact_phone' => fn($value) => $value ?: '-',
                    'tin_number' => fn($value) => $value ?: '-',
                    'trade_license_number' => fn($value) => $value ?: '-',
                    'address' => fn($value) => $value ?: '-',
                ],
                'print_layout' => [
                    'title' => 'Corporate Clients List',
                    'document_title' => 'Corporate Clients List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'corporate_clients',
                ],
                'single_print_layout' => [
                    'title' => 'Corporate Client Details',
                    'document_title' => 'Corporate Client Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'corporate_client',
                    'fields' => [
                        ['label' => 'Organization Name', 'key' => 'organization_name'],
                        ['label' => 'Organization Name (Amharic)', 'key' => 'organization_name_amharic'],
                        ['label' => 'Contact Person', 'key' => 'contact_person'],
                        ['label' => 'Contact Email', 'key' => 'contact_email'],
                        ['label' => 'Contact Phone', 'key' => 'contact_phone'],
                        ['label' => 'TIN Number', 'key' => 'tin_number'],
                        ['label' => 'Trade License Number', 'key' => 'trade_license_number'],
                        ['label' => 'Address', 'key' => 'address'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => [],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
            ];
        }

        return self::$corporateClientConfig;
    }

    private static $inventoryAlertConfig = null;

    public static function getInventoryAlertConfig(): array
    {
        if (self::$inventoryAlertConfig === null) {
            self::$inventoryAlertConfig = [
                'searchable_fields' => ['alert_type', 'message'],
                'sortable_fields' => ['alert_type', 'threshold_value', 'message', 'is_active', 'triggered_at', 'created_at'],
                'csv_headers' => ['ID', 'Item', 'Alert Type', 'Threshold Value', 'Message', 'Is Active', 'Triggered At'],
                'csv_fields' => ['id', 'item_name', 'alert_type', 'threshold_value', 'message', 'is_active_text', 'triggered_at'],
                'pdf_columns' => [
                    ['key' => 'item_name', 'label' => 'Item', 'printWidth' => '20%'],
                    ['key' => 'alert_type', 'label' => 'Alert Type', 'printWidth' => '15%'],
                    ['key' => 'threshold_value', 'label' => 'Threshold', 'printWidth' => '10%'],
                    ['key' => 'message', 'label' => 'Message', 'printWidth' => '30%'],
                    ['key' => 'is_active_text', 'label' => 'Active', 'printWidth' => '10%'],
                    ['key' => 'triggered_at', 'label' => 'Triggered At', 'printWidth' => '15%'],
                ],
                'field_transformations' => [
                    'item_name' => fn($value, $model) => $model->item?->name ?? 'N/A',
                    'is_active_text' => fn($value, $model) => $model->is_active ? 'Yes' : 'No',
                    'threshold_value' => fn($value) => $value ?: '-',
                    'triggered_at' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-',
                ],
                'print_layout' => [
                    'title' => 'Inventory Alerts List',
                    'document_title' => 'Inventory Alerts List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'inventory_alerts',
                ],
                'single_print_layout' => [
                    'title' => 'Inventory Alert Details',
                    'document_title' => 'Inventory Alert Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'inventory_alert',
                    'fields' => [
                        ['label' => 'ID', 'key' => 'id'],
                        ['label' => 'Item', 'key' => 'item_name', 'transform' => fn($value, $model) => $model->item?->name ?? 'N/A'],
                        ['label' => 'Alert Type', 'key' => 'alert_type'],
                        ['label' => 'Threshold Value', 'key' => 'threshold_value'],
                        ['label' => 'Message', 'key' => 'message'],
                        ['label' => 'Is Active', 'key' => 'is_active', 'transform' => fn($value) => $value ? 'Yes' : 'No'],
                        ['label' => 'Triggered At', 'key' => 'triggered_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => ['item'],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
                'additional_filters' => [
                    'item_search' => fn($query, $value) => $query->whereHas('item', fn($q) => $q->where('name', 'like', "%{$value}%")),
                ],
            ];
        }

        return self::$inventoryAlertConfig;
    }

    private static $marketingCampaignConfig = null;

    public static function getMarketingCampaignConfig(): array
    {
        if (self::$marketingCampaignConfig === null) {
            self::$marketingCampaignConfig = [
                'searchable_fields' => ['campaign_name', 'campaign_code', 'utm_campaign'],
                'sortable_fields' => ['campaign_name', 'campaign_code', 'campaign_type', 'status', 'start_date', 'end_date', 'budget_allocated', 'budget_spent', 'created_at'],
                'csv_headers' => ['Campaign Name', 'Campaign Code', 'Platform', 'Campaign Type', 'Status', 'Start Date', 'End Date', 'Budget Allocated', 'Budget Spent', 'Assigned Staff', 'Created By'],
                'csv_fields' => ['campaign_name', 'campaign_code', 'platform_name', 'campaign_type', 'status', 'start_date', 'end_date', 'budget_allocated_formatted', 'budget_spent_formatted', 'assigned_staff_name', 'created_by_name'],
                'pdf_columns' => [
                    ['key' => 'campaign_name', 'label' => 'Campaign Name', 'printWidth' => '15%'],
                    ['key' => 'campaign_code', 'label' => 'Code', 'printWidth' => '10%'],
                    ['key' => 'platform_name', 'label' => 'Platform', 'printWidth' => '10%'],
                    ['key' => 'campaign_type', 'label' => 'Type', 'printWidth' => '10%'],
                    ['key' => 'status', 'label' => 'Status', 'printWidth' => '8%'],
                    ['key' => 'start_date', 'label' => 'Start Date', 'printWidth' => '10%'],
                    ['key' => 'end_date', 'label' => 'End Date', 'printWidth' => '10%'],
                    ['key' => 'budget_allocated_formatted', 'label' => 'Budget Allocated', 'printWidth' => '12%'],
                    ['key' => 'budget_spent_formatted', 'label' => 'Budget Spent', 'printWidth' => '10%'],
                    ['key' => 'assigned_staff_name', 'label' => 'Assigned Staff', 'printWidth' => '15%'],
                ],
                'field_transformations' => [
                    'platform_name' => fn($value, $model) => $model->platform?->name ?? '-',
                    'assigned_staff_name' => fn($value, $model) => $model->assignedStaff?->full_name ?? '-',
                    'created_by_name' => fn($value, $model) => $model->createdByStaff?->full_name ?? '-',
                    'campaign_code' => fn($value) => $value ?: '-',
                    'campaign_type' => fn($value) => $value ?: '-',
                    'status' => fn($value) => $value ?: '-',
                    'start_date' => fn($value) => $value ?: '-',
                    'end_date' => fn($value) => $value ?: '-',
                    'budget_allocated_formatted' => fn($value, $model) => number_format($model->budget_allocated ?? 0, 2),
                    'budget_spent_formatted' => fn($value, $model) => number_format($model->budget_spent ?? 0, 2),
                ],
                'print_layout' => [
                    'title' => 'Marketing Campaigns List',
                    'document_title' => 'Marketing Campaigns List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'marketing_campaigns',
                ],
                'single_print_layout' => [
                    'title' => 'Marketing Campaign Details',
                    'document_title' => 'Marketing Campaign Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'marketing_campaign',
                    'fields' => [
                        ['label' => 'Campaign Name', 'key' => 'campaign_name'],
                        ['label' => 'Campaign Code', 'key' => 'campaign_code'],
                        ['label' => 'Platform', 'key' => 'platform_name', 'transform' => fn($value, $model) => $model->platform?->name ?? '-'],
                        ['label' => 'Campaign Type', 'key' => 'campaign_type'],
                        ['label' => 'Status', 'key' => 'status'],
                        ['label' => 'Start Date', 'key' => 'start_date'],
                        ['label' => 'End Date', 'key' => 'end_date'],
                        ['label' => 'Budget Allocated', 'key' => 'budget_allocated', 'transform' => fn($value) => number_format($value ?? 0, 2)],
                        ['label' => 'Budget Spent', 'key' => 'budget_spent', 'transform' => fn($value) => number_format($value ?? 0, 2)],
                        ['label' => 'Assigned Staff', 'key' => 'assigned_staff_name', 'transform' => fn($value, $model) => $model->assignedStaff?->full_name ?? '-'],
                        ['label' => 'Created By', 'key' => 'created_by_name', 'transform' => fn($value, $model) => $model->createdByStaff?->full_name ?? '-'],
                        ['label' => 'Description', 'key' => 'description'],
                        ['label' => 'UTM Campaign', 'key' => 'utm_campaign'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => ['platform', 'assignedStaff', 'createdByStaff'],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
                'additional_filters' => [
                    'platform_id' => fn($query, $value) => $query->where('platform_id', $value),
                    'status' => fn($query, $value) => $query->where('status', $value),
                    'campaign_type' => fn($query, $value) => $query->where('campaign_type', $value),
                    'start_date' => fn($query, $value) => $query->where('start_date', '>=', $value),
                    'end_date' => fn($query, $value) => $query->where('end_date', '<=', $value),
                ],
            ];
        }

        return self::$marketingCampaignConfig;
    }

    private static $eligibilityCriteriaConfig = null;

    public static function getEligibilityCriteriaConfig(): array
    {
        if (self::$eligibilityCriteriaConfig === null) {
            self::$eligibilityCriteriaConfig = [
                'searchable_fields' => ['criteria_name'],
                'sortable_fields' => ['criteria_name', 'operator', 'value', 'created_at'],
                'csv_headers' => ['Event ID', 'Criteria Name', 'Operator', 'Value'],
                'csv_fields' => ['event_id', 'criteria_name', 'operator', 'value'],
                'pdf_columns' => [
                    ['key' => 'event_id', 'label' => 'Event ID', 'printWidth' => '20%'],
                    ['key' => 'criteria_name', 'label' => 'Criteria Name', 'printWidth' => '30%'],
                    ['key' => 'operator', 'label' => 'Operator', 'printWidth' => '25%'],
                    ['key' => 'value', 'label' => 'Value', 'printWidth' => '25%'],
                ],
                'field_transformations' => [
                    'event_id' => fn($value) => $value ?: '-',
                    'criteria_name' => fn($value) => $value ?: '-',
                    'operator' => fn($value) => $value ?: '-',
                    'value' => fn($value) => $value ?: '-',
                ],
                'print_layout' => [
                    'title' => 'Eligibility Criteria List',
                    'document_title' => 'Eligibility Criteria List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'eligibility_criteria',
                ],
                'single_print_layout' => [
                    'title' => 'Eligibility Criteria Details',
                    'document_title' => 'Eligibility Criteria Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'eligibility_criteria',
                    'fields' => [
                        ['label' => 'Event ID', 'key' => 'event_id'],
                        ['label' => 'Criteria Name', 'key' => 'criteria_name'],
                        ['label' => 'Operator', 'key' => 'operator'],
                        ['label' => 'Value', 'key' => 'value'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => [],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
            ];
        }

        return self::$eligibilityCriteriaConfig;
    }

    private static $eventBroadcastConfig = null;

    public static function getEventBroadcastConfig(): array
    {
        if (self::$eventBroadcastConfig === null) {
            self::$eventBroadcastConfig = [
                'searchable_fields' => ['message', 'channel'],
                'sortable_fields' => ['event_id', 'channel', 'sent_by_staff_id', 'created_at'],
                'csv_headers' => ['Event ID', 'Channel', 'Message', 'Sent By Staff ID'],
                'csv_fields' => ['event_id', 'channel', 'message', 'sent_by_staff_id'],
                'pdf_columns' => [
                    ['key' => 'event_id', 'label' => 'Event ID', 'printWidth' => '15%'],
                    ['key' => 'channel', 'label' => 'Channel', 'printWidth' => '20%'],
                    ['key' => 'message', 'label' => 'Message', 'printWidth' => '45%'],
                    ['key' => 'sent_by_staff_id', 'label' => 'Sent By Staff ID', 'printWidth' => '20%'],
                ],
                'field_transformations' => [
                    'event_id' => fn($value) => $value ?: '-',
                    'channel' => fn($value) => $value ?: '-',
                    'message' => fn($value) => $value ?: '-',
                    'sent_by_staff_id' => fn($value) => $value ?: '-',
                ],
                'print_layout' => [
                    'title' => 'Event Broadcasts List',
                    'document_title' => 'Event Broadcasts List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'event_broadcasts',
                ],
                'single_print_layout' => [
                    'title' => 'Event Broadcast Details',
                    'document_title' => 'Event Broadcast Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'event_broadcast',
                    'fields' => [
                        ['label' => 'Event ID', 'key' => 'event_id'],
                        ['label' => 'Channel', 'key' => 'channel'],
                        ['label' => 'Message', 'key' => 'message'],
                        ['label' => 'Sent By Staff ID', 'key' => 'sent_by_staff_id'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => [],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
            ];
        }

        return self::$eventBroadcastConfig;
    }

    private static $eventRecommendationConfig = null;

    public static function getEventRecommendationConfig(): array
    {
        if (self::$eventRecommendationConfig === null) {
            self::$eventRecommendationConfig = [
                'searchable_fields' => ['patient_name', 'source_channel'],
                'sortable_fields' => ['patient_name', 'source_channel', 'status', 'created_at'],
                'csv_headers' => ['Event ID', 'Source Channel', 'Recommended By', 'Patient Name', 'Patient Phone', 'Notes', 'Status'],
                'csv_fields' => ['event_id', 'source_channel', 'recommended_by_name', 'patient_name', 'patient_phone', 'notes', 'status'],
                'pdf_columns' => [
                    ['key' => 'patient_name', 'label' => 'Patient Name', 'printWidth' => '20%'],
                    ['key' => 'source_channel', 'label' => 'Source Channel', 'printWidth' => '15%'],
                    ['key' => 'recommended_by_name', 'label' => 'Recommended By', 'printWidth' => '15%'],
                    ['key' => 'patient_phone', 'label' => 'Phone', 'printWidth' => '15%'],
                    ['key' => 'notes', 'label' => 'Notes', 'printWidth' => '20%'],
                    ['key' => 'status', 'label' => 'Status', 'printWidth' => '15%'],
                ],
                'field_transformations' => [
                    'event_id' => fn($value) => $value ?: '-',
                    'source_channel' => fn($value) => $value ?: '-',
                    'recommended_by_name' => fn($value) => $value ?: '-',
                    'patient_name' => fn($value) => $value ?: '-',
                    'patient_phone' => fn($value) => $value ?: '-',
                    'notes' => fn($value) => $value ?: '-',
                    'status' => fn($value) => $value ?: '-',
                ],
                'print_layout' => [
                    'title' => 'Event Recommendations List',
                    'document_title' => 'Event Recommendations List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'event_recommendations',
                ],
                'single_print_layout' => [
                    'title' => 'Event Recommendation Details',
                    'document_title' => 'Event Recommendation Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'event_recommendation',
                    'fields' => [
                        ['label' => 'Event ID', 'key' => 'event_id'],
                        ['label' => 'Source Channel', 'key' => 'source_channel'],
                        ['label' => 'Recommended By', 'key' => 'recommended_by_name'],
                        ['label' => 'Patient Name', 'key' => 'patient_name'],
                        ['label' => 'Patient Phone', 'key' => 'patient_phone'],
                        ['label' => 'Notes', 'key' => 'notes'],
                        ['label' => 'Status', 'key' => 'status'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => [],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
            ];
        }

        return self::$eventRecommendationConfig;
    }

    private static $campaignContentConfig = null;

    public static function getCampaignContentConfig(): array
    {
        if (self::$campaignContentConfig === null) {
            self::$campaignContentConfig = [
                'searchable_fields' => ['title', 'description'],
                'sortable_fields' => ['title', 'content_type', 'status', 'scheduled_post_date', 'created_at'],
                'csv_headers' => ['Title', 'Description', 'Campaign', 'Platform', 'Content Type', 'Status', 'Scheduled Post Date'],
                'csv_fields' => ['title', 'description', 'campaign_name', 'platform_name', 'content_type', 'status', 'scheduled_post_date'],
                'pdf_columns' => [
                    ['key' => 'title', 'label' => 'Title', 'printWidth' => '20%'],
                    ['key' => 'campaign_name', 'label' => 'Campaign', 'printWidth' => '15%'],
                    ['key' => 'platform_name', 'label' => 'Platform', 'printWidth' => '15%'],
                    ['key' => 'content_type', 'label' => 'Type', 'printWidth' => '10%'],
                    ['key' => 'status', 'label' => 'Status', 'printWidth' => '10%'],
                    ['key' => 'scheduled_post_date', 'label' => 'Scheduled Date', 'printWidth' => '15%'],
                    ['key' => 'description', 'label' => 'Description', 'printWidth' => '15%'],
                ],
                'field_transformations' => [
                    'title' => fn($value) => $value ?: '-',
                    'description' => fn($value) => $value ? (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) : '-',
                    'campaign_name' => fn($value) => $value ?: '-',
                    'platform_name' => fn($value) => $value ?: '-',
                    'content_type' => fn($value) => $value ?: '-',
                    'status' => fn($value) => $value ?: '-',
                    'scheduled_post_date' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-',
                ],
                'print_layout' => [
                    'title' => 'Campaign Contents List',
                    'document_title' => 'Campaign Contents List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'campaign_contents',
                ],
                'single_print_layout' => [
                    'title' => 'Campaign Content Details',
                    'document_title' => 'Campaign Content Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'campaign_content',
                    'fields' => [
                        ['label' => 'Title', 'key' => 'title'],
                        ['label' => 'Description', 'key' => 'description'],
                        ['label' => 'Campaign', 'key' => 'campaign_name'],
                        ['label' => 'Platform', 'key' => 'platform_name'],
                        ['label' => 'Content Type', 'key' => 'content_type'],
                        ['label' => 'Status', 'key' => 'status'],
                        ['label' => 'Scheduled Post Date', 'key' => 'scheduled_post_date', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'Not scheduled'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => ['campaign', 'platform'],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
                'additional_filters' => [
                    'campaign_id' => fn($query, $value) => $query->where('campaign_id', $value),
                    'platform_id' => fn($query, $value) => $query->where('platform_id', $value),
                    'content_type' => fn($query, $value) => $query->where('content_type', $value),
                    'status' => fn($query, $value) => $query->where('status', $value),
                    'scheduled_post_date_start' => fn($query, $value) => $query->where('scheduled_post_date', '>=', $value),
                    'scheduled_post_date_end' => fn($query, $value) => $query->where('scheduled_post_date', '<=', $value),
                ],
            ];
        }

        return self::$campaignContentConfig;
    }

    private static $campaignMetricConfig = null;

    public static function getCampaignMetricConfig(): array
    {
        if (self::$campaignMetricConfig === null) {
            self::$campaignMetricConfig = [
                'searchable_fields' => ['date'],
                'sortable_fields' => ['date', 'impressions', 'clicks', 'conversions', 'revenue_generated', 'total_cost'],
                'csv_headers' => ['Date', 'Impressions', 'Clicks', 'Conversions', 'Revenue Generated', 'Total Cost', 'CTR', 'Conversion Rate'],
                'csv_fields' => ['date', 'impressions', 'clicks', 'conversions', 'revenue_generated', 'total_cost', 'ctr', 'conversion_rate'],
                'pdf_columns' => [
                    ['key' => 'date', 'label' => 'Date', 'printWidth' => '15%'],
                    ['key' => 'impressions', 'label' => 'Impressions', 'printWidth' => '12%'],
                    ['key' => 'clicks', 'label' => 'Clicks', 'printWidth' => '12%'],
                    ['key' => 'conversions', 'label' => 'Conversions', 'printWidth' => '12%'],
                    ['key' => 'revenue_generated', 'label' => 'Revenue', 'printWidth' => '15%'],
                    ['key' => 'total_cost', 'label' => 'Cost', 'printWidth' => '12%'],
                    ['key' => 'ctr', 'label' => 'CTR %', 'printWidth' => '11%'],
                    ['key' => 'conversion_rate', 'label' => 'Conv. Rate %', 'printWidth' => '11%'],
                ],
                'field_transformations' => [
                    'date' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-',
                    'impressions' => fn($value) => number_format($value ?: 0),
                    'clicks' => fn($value) => number_format($value ?: 0),
                    'conversions' => fn($value) => number_format($value ?: 0),
                    'revenue_generated' => fn($value) => '$' . number_format($value ?: 0, 2),
                    'total_cost' => fn($value) => '$' . number_format($value ?: 0, 2),
                    'ctr' => fn($value) => $value ? number_format($value, 2) . '%' : '0.00%',
                    'conversion_rate' => fn($value) => $value ? number_format($value, 2) . '%' : '0.00%',
                ],
                'print_layout' => [
                    'title' => 'Campaign Performance Report',
                    'document_title' => 'Campaign Performance Report',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'campaign_performance',
                ],
                'single_print_layout' => [
                    'title' => 'Campaign Metric Details',
                    'document_title' => 'Campaign Metric Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'campaign_metric',
                    'fields' => [
                        ['label' => 'Date', 'key' => 'date', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : 'N/A'],
                        ['label' => 'Impressions', 'key' => 'impressions', 'transform' => fn($value) => number_format($value ?: 0)],
                        ['label' => 'Clicks', 'key' => 'clicks', 'transform' => fn($value) => number_format($value ?: 0)],
                        ['label' => 'Conversions', 'key' => 'conversions', 'transform' => fn($value) => number_format($value ?: 0)],
                        ['label' => 'Revenue Generated', 'key' => 'revenue_generated', 'transform' => fn($value) => '$' . number_format($value ?: 0, 2)],
                        ['label' => 'Total Cost', 'key' => 'total_cost', 'transform' => fn($value) => '$' . number_format($value ?: 0, 2)],
                        ['label' => 'CTR', 'key' => 'ctr', 'transform' => fn($value) => $value ? number_format($value, 2) . '%' : '0.00%'],
                        ['label' => 'Conversion Rate', 'key' => 'conversion_rate', 'transform' => fn($value) => $value ? number_format($value, 2) . '%' : '0.00%'],
                    ],
                ],
                'relationships' => ['campaign', 'campaign.platform'],
                'default_sort' => ['date', 'desc'],
                'per_page' => 5,
                'additional_filters' => [
                    'campaign_id' => fn($query, $value) => $query->where('campaign_id', $value),
                    'platform_id' => fn($query, $value) => $query->whereHas('campaign.platform', function ($q) use ($value) { $q->where('id', $value); }),
                    'start_date' => fn($query, $value) => $query->where('date', '>=', $value),
                    'end_date' => fn($query, $value) => $query->where('date', '<=', $value),
                ],
                'custom_query_builder' => function($query, $request) {
                    // Group by date and aggregate metrics for performance reports
                    return $query->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(conversions) as conversions, SUM(revenue_generated) as revenue_generated, SUM(cost_per_click * clicks) as total_cost')
                                 ->selectRaw('CASE WHEN SUM(impressions) > 0 THEN (SUM(clicks) * 100.0 / SUM(impressions)) ELSE 0 END as ctr')
                                 ->selectRaw('CASE WHEN SUM(clicks) > 0 THEN (SUM(conversions) * 100.0 / SUM(clicks)) ELSE 0 END as conversion_rate')
                                 ->groupBy('date');
                },
            ];
        }

        return self::$campaignMetricConfig;
    }

    private static $insuranceCompanyConfig = null;

    public static function getInsuranceCompanyConfig(): array
    {
        if (self::$insuranceCompanyConfig === null) {
            self::$insuranceCompanyConfig = [
                'searchable_fields' => ['name', 'contact_email'],
                'sortable_fields' => ['name', 'contact_person', 'contact_email', 'contact_phone', 'created_at'],
                'csv_headers' => ['Name', 'Name (Amharic)', 'Contact Person', 'Contact Email', 'Contact Phone', 'Address', 'Address (Amharic)'],
                'csv_fields' => ['name', 'name_amharic', 'contact_person', 'contact_email', 'contact_phone', 'address', 'address_amharic'],
                'pdf_columns' => [
                    ['key' => 'name', 'label' => 'Name', 'printWidth' => '20%'],
                    ['key' => 'contact_person', 'label' => 'Contact Person', 'printWidth' => '15%'],
                    ['key' => 'contact_email', 'label' => 'Email', 'printWidth' => '20%'],
                    ['key' => 'contact_phone', 'label' => 'Phone', 'printWidth' => '15%'],
                    ['key' => 'address', 'label' => 'Address', 'printWidth' => '30%'],
                ],
                'field_transformations' => [
                    'name' => fn($value) => $value ?: '-',
                    'name_amharic' => fn($value) => $value ?: '-',
                    'contact_person' => fn($value) => $value ?: '-',
                    'contact_email' => fn($value) => $value ?: '-',
                    'contact_phone' => fn($value) => $value ?: '-',
                    'address' => fn($value) => $value ? (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) : '-',
                    'address_amharic' => fn($value) => $value ? (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) : '-',
                ],
                'print_layout' => [
                    'title' => 'Insurance Companies List',
                    'document_title' => 'Insurance Companies List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'insurance_companies',
                ],
                'single_print_layout' => [
                    'title' => 'Insurance Company Details',
                    'document_title' => 'Insurance Company Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'insurance_company',
                    'fields' => [
                        ['label' => 'Name', 'key' => 'name'],
                        ['label' => 'Name (Amharic)', 'key' => 'name_amharic'],
                        ['label' => 'Contact Person', 'key' => 'contact_person'],
                        ['label' => 'Contact Email', 'key' => 'contact_email'],
                        ['label' => 'Contact Phone', 'key' => 'contact_phone'],
                        ['label' => 'Address', 'key' => 'address'],
                        ['label' => 'Address (Amharic)', 'key' => 'address_amharic'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => [],
                'default_sort' => ['name', 'asc'],
                'per_page' => 5,
            ];
        }

        return self::$insuranceCompanyConfig;
    }

    private static $insurancePolicyConfig = null;

    public static function getInsurancePolicyConfig(): array
    {
        if (self::$insurancePolicyConfig === null) {
            self::$insurancePolicyConfig = [
                'searchable_fields' => ['service_type', 'coverage_type'],
                'sortable_fields' => ['service_type', 'coverage_percentage', 'coverage_type', 'is_active', 'created_at'],
                'csv_headers' => ['Service Type', 'Service Type (Amharic)', 'Coverage Percentage', 'Coverage Type', 'Is Active', 'Insurance Company', 'Corporate Client', 'Notes'],
                'csv_fields' => ['service_type', 'service_type_amharic', 'coverage_percentage', 'coverage_type', 'is_active_text', 'insurance_company_name', 'corporate_client_name', 'notes'],
                'pdf_columns' => [
                    ['key' => 'service_type', 'label' => 'Service Type', 'printWidth' => '20%'],
                    ['key' => 'coverage_percentage', 'label' => 'Coverage %', 'printWidth' => '12%'],
                    ['key' => 'coverage_type', 'label' => 'Coverage Type', 'printWidth' => '15%'],
                    ['key' => 'is_active_text', 'label' => 'Status', 'printWidth' => '10%'],
                    ['key' => 'insurance_company_name', 'label' => 'Insurance Company', 'printWidth' => '20%'],
                    ['key' => 'corporate_client_name', 'label' => 'Corporate Client', 'printWidth' => '23%'],
                ],
                'field_transformations' => [
                    'service_type' => fn($value) => $value ?: '-',
                    'service_type_amharic' => fn($value) => $value ?: '-',
                    'coverage_percentage' => fn($value) => $value ? $value . '%' : '0%',
                    'coverage_type' => fn($value) => $value ?: '-',
                    'is_active_text' => fn($value) => $value ? 'Active' : 'Inactive',
                    'insurance_company_name' => fn($value) => $value ?: '-',
                    'corporate_client_name' => fn($value) => $value ?: '-',
                    'notes' => fn($value) => $value ? (strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value) : '-',
                ],
                'print_layout' => [
                    'title' => 'Insurance Policies List',
                    'document_title' => 'Insurance Policies List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'insurance_policies',
                ],
                'single_print_layout' => [
                    'title' => 'Insurance Policy Details',
                    'document_title' => 'Insurance Policy Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'insurance_policy',
                    'fields' => [
                        ['label' => 'Service Type', 'key' => 'service_type'],
                        ['label' => 'Service Type (Amharic)', 'key' => 'service_type_amharic'],
                        ['label' => 'Coverage Percentage', 'key' => 'coverage_percentage', 'transform' => fn($value) => $value ? $value . '%' : '0%'],
                        ['label' => 'Coverage Type', 'key' => 'coverage_type'],
                        ['label' => 'Status', 'key' => 'is_active', 'transform' => fn($value) => $value ? 'Active' : 'Inactive'],
                        ['label' => 'Insurance Company', 'key' => 'insurance_company_name'],
                        ['label' => 'Corporate Client', 'key' => 'corporate_client_name'],
                        ['label' => 'Notes', 'key' => 'notes'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => ['insuranceCompany', 'corporateClient'],
                'default_sort' => ['service_type', 'asc'],
                'per_page' => 5,
            ];
        }

        return self::$insurancePolicyConfig;
    }

    private static $employeeInsuranceRecordConfig = null;

    public static function getEmployeeInsuranceRecordConfig(): array
    {
        if (self::$employeeInsuranceRecordConfig === null) {
            self::$employeeInsuranceRecordConfig = [
                'searchable_fields' => ['kebele_id', 'employee_id_number'],
                'sortable_fields' => ['kebele_id', 'woreda', 'region', 'federal_id', 'ministry_department', 'employee_id_number', 'verified', 'created_at'],
                'csv_headers' => ['Patient Name', 'Policy', 'Kebele ID', 'Woreda', 'Region', 'Federal ID', 'Ministry Department', 'Employee ID Number', 'Verified', 'Verified At'],
                'csv_fields' => ['patient_name', 'policy_service_type', 'kebele_id', 'woreda', 'region', 'federal_id', 'ministry_department', 'employee_id_number', 'verified_text', 'verified_at'],
                'pdf_columns' => [
                    ['key' => 'patient_name', 'label' => 'Patient', 'printWidth' => '15%'],
                    ['key' => 'kebele_id', 'label' => 'Kebele ID', 'printWidth' => '12%'],
                    ['key' => 'woreda', 'label' => 'Woreda', 'printWidth' => '12%'],
                    ['key' => 'region', 'label' => 'Region', 'printWidth' => '12%'],
                    ['key' => 'federal_id', 'label' => 'Federal ID', 'printWidth' => '12%'],
                    ['key' => 'ministry_department', 'label' => 'Ministry/Dept', 'printWidth' => '15%'],
                    ['key' => 'employee_id_number', 'label' => 'Employee ID', 'printWidth' => '12%'],
                    ['key' => 'verified_text', 'label' => 'Verified', 'printWidth' => '10%'],
                ],
                'field_transformations' => [
                    'patient_name' => fn($value) => $value ?: '-',
                    'policy_service_type' => fn($value) => $value ?: '-',
                    'kebele_id' => fn($value) => $value ?: '-',
                    'woreda' => fn($value) => $value ?: '-',
                    'region' => fn($value) => $value ?: '-',
                    'federal_id' => fn($value) => $value ?: '-',
                    'ministry_department' => fn($value) => $value ? (strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value) : '-',
                    'employee_id_number' => fn($value) => $value ?: '-',
                    'verified_text' => fn($value) => $value ? 'Yes' : 'No',
                    'verified_at' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-',
                ],
                'print_layout' => [
                    'title' => 'Employee Insurance Records List',
                    'document_title' => 'Employee Insurance Records List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'employee_insurance_records',
                ],
                'single_print_layout' => [
                    'title' => 'Employee Insurance Record Details',
                    'document_title' => 'Employee Insurance Record Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'employee_insurance_record',
                    'fields' => [
                        ['label' => 'Patient Name', 'key' => 'patient_name'],
                        ['label' => 'Policy Service Type', 'key' => 'policy_service_type'],
                        ['label' => 'Kebele ID', 'key' => 'kebele_id'],
                        ['label' => 'Woreda', 'key' => 'woreda'],
                        ['label' => 'Region', 'key' => 'region'],
                        ['label' => 'Federal ID', 'key' => 'federal_id'],
                        ['label' => 'Ministry Department', 'key' => 'ministry_department'],
                        ['label' => 'Employee ID Number', 'key' => 'employee_id_number'],
                        ['label' => 'Verified', 'key' => 'verified', 'transform' => fn($value) => $value ? 'Yes' : 'No'],
                        ['label' => 'Verified At', 'key' => 'verified_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'Not verified'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => ['patient', 'policy'],
                'default_sort' => ['kebele_id', 'asc'],
                'per_page' => 5,
            ];
        }

        return self::$employeeInsuranceRecordConfig;
    }

    private static $marketingPlatformConfig = null;

    public static function getMarketingPlatformConfig(): array
    {
        if (self::$marketingPlatformConfig === null) {
            self::$marketingPlatformConfig = [
                'searchable_fields' => ['name'],
                'sortable_fields' => ['name', 'api_endpoint', 'is_active', 'created_at'],
                'csv_headers' => ['Name', 'API Endpoint', 'Active', 'Description', 'Created At'],
                'csv_fields' => ['name', 'api_endpoint', 'is_active_text', 'description', 'created_at'],
                'pdf_columns' => [
                    ['key' => 'name', 'label' => 'Name', 'printWidth' => '25%'],
                    ['key' => 'api_endpoint', 'label' => 'API Endpoint', 'printWidth' => '35%'],
                    ['key' => 'is_active_text', 'label' => 'Active', 'printWidth' => '15%'],
                    ['key' => 'description', 'label' => 'Description', 'printWidth' => '25%'],
                ],
                'field_transformations' => [
                    'name' => fn($value) => $value ?: '-',
                    'api_endpoint' => fn($value) => $value ?: '-',
                    'is_active_text' => fn($value) => $value ? 'Yes' : 'No',
                    'description' => fn($value) => $value ? (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) : '-',
                    'created_at' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-',
                ],
                'print_layout' => [
                    'title' => 'Marketing Platforms List',
                    'document_title' => 'Marketing Platforms List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'marketing_platforms',
                ],
                'single_print_layout' => [
                    'title' => 'Marketing Platform Details',
                    'document_title' => 'Marketing Platform Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'marketing_platform',
                    'fields' => [
                        ['label' => 'Name', 'key' => 'name'],
                        ['label' => 'API Endpoint', 'key' => 'api_endpoint'],
                        ['label' => 'Active', 'key' => 'is_active', 'transform' => fn($value) => $value ? 'Yes' : 'No'],
                        ['label' => 'Description', 'key' => 'description'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => [],
                'default_sort' => ['name', 'asc'],
                'per_page' => 5,
                'additional_filters' => [
                    'is_active' => fn($query, $value) => $query->where('is_active', $value),
                ],
            ];
        }

        return self::$marketingPlatformConfig;
    }

    private static $landingPageConfig = null;

    public static function getLandingPageConfig(): array
    {
        if (self::$landingPageConfig === null) {
            self::$landingPageConfig = [
                'searchable_fields' => ['page_title', 'page_url', 'page_code'],
                'sortable_fields' => ['page_title', 'page_url', 'page_code', 'is_active', 'language', 'created_at'],
                'csv_headers' => ['Page Title', 'Page URL', 'Page Code', 'Campaign', 'Active', 'Language', 'Description', 'Created At'],
                'csv_fields' => ['page_title', 'page_url', 'page_code', 'campaign_name', 'is_active_text', 'language', 'description', 'created_at'],
                'pdf_columns' => [
                    ['key' => 'page_title', 'label' => 'Page Title', 'printWidth' => '20%'],
                    ['key' => 'page_url', 'label' => 'Page URL', 'printWidth' => '25%'],
                    ['key' => 'page_code', 'label' => 'Page Code', 'printWidth' => '15%'],
                    ['key' => 'campaign_name', 'label' => 'Campaign', 'printWidth' => '15%'],
                    ['key' => 'is_active_text', 'label' => 'Active', 'printWidth' => '10%'],
                    ['key' => 'language', 'label' => 'Language', 'printWidth' => '15%'],
                ],
                'field_transformations' => [
                    'page_title' => fn($value) => $value ?: '-',
                    'page_url' => fn($value) => $value ?: '-',
                    'page_code' => fn($value) => $value ?: '-',
                    'campaign_name' => fn($value) => $value ?: '-',
                    'is_active_text' => fn($value) => $value ? 'Yes' : 'No',
                    'language' => fn($value) => $value ?: '-',
                    'description' => fn($value) => $value ? (strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value) : '-',
                    'created_at' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-',
                ],
                'print_layout' => [
                    'title' => 'Landing Pages List',
                    'document_title' => 'Landing Pages List',
                    'paper_size' => 'a4',
                    'orientation' => 'landscape',
                    'filename_prefix' => 'landing_pages',
                ],
                'single_print_layout' => [
                    'title' => 'Landing Page Details',
                    'document_title' => 'Landing Page Details',
                    'paper_size' => 'a4',
                    'orientation' => 'portrait',
                    'filename_prefix' => 'landing_page',
                    'fields' => [
                        ['label' => 'Page Title', 'key' => 'page_title'],
                        ['label' => 'Page URL', 'key' => 'page_url'],
                        ['label' => 'Page Code', 'key' => 'page_code'],
                        ['label' => 'Campaign', 'key' => 'campaign_name'],
                        ['label' => 'Active', 'key' => 'is_active', 'transform' => fn($value) => $value ? 'Yes' : 'No'],
                        ['label' => 'Language', 'key' => 'language'],
                        ['label' => 'Description', 'key' => 'description'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'relationships' => ['campaign'],
                'default_sort' => ['page_title', 'asc'],
                'per_page' => 5,
                'additional_filters' => [
                    'campaign_id' => fn($query, $value) => $query->where('campaign_id', $value),
                    'is_active' => fn($query, $value) => $query->where('is_active', $value),
                    'language' => fn($query, $value) => $query->where('language', $value),
                ],
            ];
        }

        return self::$landingPageConfig;
    }
}
