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
            'searchable_fields' => ['name', 'contact_person', 'contact_email', 'contact_phone', 'address'],
            'sortable_fields' => ['name', 'contact_person', 'contact_email', 'contact_phone', 'created_at'],
            'default_sort' => 'created_at',
            'select_fields' => ['id', 'name', 'contact_person', 'contact_email', 'contact_phone', 'address', 'tin_number', 'trade_license_number', 'created_at'],

            'csv' => [
                'headers' => ['Name', 'Contact Person', 'Contact Email', 'Contact Phone', 'Address', 'TIN Number', 'Trade License Number'],
                'fields' => ['name', 'contact_person', 'contact_email', 'contact_phone', 'address', 'tin_number', 'trade_license_number'],
                'filename_prefix' => 'insurance_companies',
            ],

            'pdf' => [
                'title' => 'Insurance Companies List',
                'document_title' => 'Insurance Companies List',
                'filename_prefix' => 'insurance_companies',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'name' => 'name',
                    'contact_person' => ['field' => 'contact_person', 'default' => '-'],
                    'contact_email' => ['field' => 'contact_email', 'default' => '-'],
                    'contact_phone' => ['field' => 'contact_phone', 'default' => '-'],
                    'address' => ['field' => 'address', 'default' => '-'],
                    'tin_number' => ['field' => 'tin_number', 'default' => '-'],
                    'trade_license_number' => ['field' => 'trade_license_number', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'contact_email', 'label' => 'Contact Email'],
                    ['key' => 'contact_phone', 'label' => 'Contact Phone'],
                    ['key' => 'address', 'label' => 'Address'],
                    ['key' => 'tin_number', 'label' => 'TIN Number'],
                    ['key' => 'trade_license_number', 'label' => 'Trade License Number'],
                ],
            ],

            'current_page' => [
                'title' => 'Insurance Companies (Current View)',
                'document_title' => 'Insurance Companies (Current View)',
                'filename_prefix' => 'insurance_companies_current',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'name' => 'name',
                    'contact_person' => ['field' => 'contact_person', 'default' => '-'],
                    'contact_email' => ['field' => 'contact_email', 'default' => '-'],
                    'contact_phone' => ['field' => 'contact_phone', 'default' => '-'],
                    'address' => ['field' => 'address', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'contact_email', 'label' => 'Contact Email'],
                    ['key' => 'contact_phone', 'label' => 'Contact Phone'],
                    ['key' => 'address', 'label' => 'Address'],
                ],
            ],

            'all_records' => [
                'title' => 'Insurance Companies List',
                'document_title' => 'Insurance Companies List',
                'filename_prefix' => 'insurance_companies',
                'orientation' => 'landscape',
                'include_index' => false,
                'default_sort' => 'name',
                'fields' => [
                    'name' => 'name',
                    'contact_person' => ['field' => 'contact_person', 'default' => '-'],
                    'contact_email' => ['field' => 'contact_email', 'default' => '-'],
                    'contact_phone' => ['field' => 'contact_phone', 'default' => '-'],
                    'address' => ['field' => 'address', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'contact_person', 'label' => 'Contact Person'],
                    ['key' => 'contact_email', 'label' => 'Contact Email'],
                    ['key' => 'contact_phone', 'label' => 'Contact Phone'],
                    ['key' => 'address', 'label' => 'Address'],
                ],
            ],

            'single_record' => [
                'filename_prefix' => 'insurance_company',
                'fields' => [
                    'Name' => 'name',
                    'Contact Person' => ['field' => 'contact_person', 'default' => '-'],
                    'Contact Email' => ['field' => 'contact_email', 'default' => '-'],
                    'Contact Phone' => ['field' => 'contact_phone', 'default' => '-'],
                    'Address' => ['field' => 'address', 'default' => '-'],
                    'TIN Number' => ['field' => 'tin_number', 'default' => '-'],
                    'Trade License Number' => ['field' => 'trade_license_number', 'default' => '-'],
                ],
            ],
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
            
            'current_page' => [
                'title' => 'Marketing Tasks (Current View)',
                'document_title' => 'Marketing Tasks (Current View)',
                'filename' => 'marketing_tasks_current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'task_name' => 'task_name',
                    'expected_results' => ['field' => 'expected_results', 'default' => '-'],
                    'priority' => 'priority',
                    'status' => 'status',
                    'due_date' => ['field' => 'due_date', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'task_name', 'label' => 'Task Name'],
                    ['key' => 'expected_results', 'label' => 'Expected Results'],
                    ['key' => 'priority', 'label' => 'Priority'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'due_date', 'label' => 'Due Date'],
                ]
            ],
            
            'all_records' => [
                'title' => 'All Marketing Tasks',
                'document_title' => 'All Marketing Tasks',
                'filename' => 'marketing_tasks_all.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'task_name',
                'fields' => [
                    'task_name' => 'task_name',
                    'expected_results' => ['field' => 'expected_results', 'default' => '-'],
                    'priority' => 'priority',
                    'status' => 'status',
                    'due_date' => ['field' => 'due_date', 'default' => '-'],
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'task_name', 'label' => 'Task Name'],
                    ['key' => 'expected_results', 'label' => 'Expected Results'],
                    ['key' => 'priority', 'label' => 'Priority'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'due_date', 'label' => 'Due Date'],
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Task Name' => 'task_name',
                    'Description' => ['field' => 'description', 'default' => '-'],
                    'Expected Results' => ['field' => 'expected_results', 'default' => '-'],
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
                    ['key' => 'trade_license_number', 'label' => 'Trade License Number', 'printWidth' => '15%'],
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

    

    



    private static $eligibilityCriteriaConfig = null;

    public static function getEligibilityCriteriaConfig(): array
    {
        if (self::$eligibilityCriteriaConfig === null) {
            // Updated base fields to match DB + UI
            $legacy = [
                'searchable_fields' => ['criteria_title', 'operator', 'value'],
                'sortable_fields' => ['criteria_title', 'operator', 'value', 'created_at'],
                'csv_headers' => ['Event', 'Criteria Title', 'Operator', 'Value'],
                'csv_fields' => [
                    ['field' => 'event.title', 'default' => '-'],
                    'criteria_title',
                    'operator',
                    'value'
                ],
                'pdf_columns' => [
                    ['key' => 'event_title', 'label' => 'Event', 'printWidth' => '30%'],
                    ['key' => 'criteria_title', 'label' => 'Criteria Title', 'printWidth' => '30%'],
                    ['key' => 'operator', 'label' => 'Operator', 'printWidth' => '20%'],
                    ['key' => 'value', 'label' => 'Value', 'printWidth' => '20%'],
                ],
                'field_transformations' => [
                    'event_title' => fn($value) => $value ?: '-',
                    'criteria_title' => fn($value) => $value ?: '-',
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
                        ['label' => 'Event', 'key' => 'event_title'],
                        ['label' => 'Criteria Title', 'key' => 'criteria_title'],
                        ['label' => 'Operator', 'key' => 'operator'],
                        ['label' => 'Value', 'key' => 'value'],
                        ['label' => 'Created At', 'key' => 'created_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                        ['label' => 'Updated At', 'key' => 'updated_at', 'transform' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : 'N/A'],
                    ],
                ],
                'with_relations' => ['event'],
                'default_sort' => ['created_at', 'desc'],
                'per_page' => 5,
            ];

            // New structure compatible with ExportableTrait
            self::$eligibilityCriteriaConfig = array_merge($legacy, [
                'filename_prefix' => 'eligibility_criteria',
                'csv' => [
                    'headers' => $legacy['csv_headers'],
                    'fields' => $legacy['csv_fields'],
                    'filename_prefix' => 'eligibility_criteria',
                ],
                'current_page' => [
                    'view' => 'pdf-layout',
                    'title' => $legacy['print_layout']['title'],
                    'document_title' => $legacy['print_layout']['document_title'],
                    'filename_prefix' => 'eligibility_criteria-current',
                    'orientation' => $legacy['print_layout']['orientation'] ?? 'landscape',
                    'include_index' => true,
                    'columns' => $legacy['pdf_columns'],
                ],
                'single_record' => [
                    'view' => 'pdf-layout',
                    'title' => $legacy['single_print_layout']['title'],
                    'document_title' => $legacy['single_print_layout']['document_title'],
                    'filename_prefix' => 'eligibility_criteria',
                    // For single record, columns acts as fields list in the universal single template
                    'columns' => array_map(function ($f) {
                        return ['key' => $f['key'], 'label' => $f['label'] ?? ucfirst(str_replace('_', ' ', $f['key']))];
                    }, $legacy['single_print_layout']['fields']),
                ],
            ]);
        }

        return self::$eligibilityCriteriaConfig;
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

                // CSV export configuration (standardized)
                'csv' => [
                    'headers' => ['Patient Name', 'Policy', 'Verified'],
                    'fields' => [
                        'patient_name',
                        'policy_service_type',
                        'verified_text',
                    ],
                    // Exclude records without a verified_at timestamp
                    'query_callback' => function ($query) {
                        $query->whereNotNull('verified_at');
                    },
                    'filename_prefix' => 'employee_insurance_records',
                ],

                // Print current page configuration
                'current_page' => [
                    'view' => 'pdf-layout',
                    'document_title' => 'Employee Insurance Records (Current View)',
                    'filename_prefix' => 'employee_insurance_records_current',
                    'orientation' => 'landscape',
                    'include_index' => true,
                    'columns' => [
                        ['key' => 'index', 'label' => '#'],
                        ['key' => 'patient_name', 'label' => 'Patient', 'printWidth' => '15%'],
                        ['key' => 'kebele_id', 'label' => 'Kebele ID', 'printWidth' => '12%'],
                        ['key' => 'woreda', 'label' => 'Woreda', 'printWidth' => '12%'],
                        ['key' => 'region', 'label' => 'Region', 'printWidth' => '12%'],
                        ['key' => 'federal_id', 'label' => 'Federal ID', 'printWidth' => '12%'],
                        ['key' => 'ministry_department', 'label' => 'Ministry/Dept', 'printWidth' => '15%'],
                        ['key' => 'employee_id_number', 'label' => 'Employee ID', 'printWidth' => '12%'],
                        ['key' => 'verified_text', 'label' => 'Verified', 'printWidth' => '10%'],
                    ],
                ],

                // Print all records configuration
                'all_records' => [
                    'view' => 'pdf-layout',
                    'document_title' => 'All Employee Insurance Records',
                    'filename_prefix' => 'employee_insurance_records_all',
                    'orientation' => 'landscape',
                    'include_index' => true,
                    'columns' => [
                        ['key' => 'index', 'label' => '#'],
                        ['key' => 'patient_name', 'label' => 'Patient', 'printWidth' => '15%'],
                        ['key' => 'kebele_id', 'label' => 'Kebele ID', 'printWidth' => '12%'],
                        ['key' => 'woreda', 'label' => 'Woreda', 'printWidth' => '12%'],
                        ['key' => 'region', 'label' => 'Region', 'printWidth' => '12%'],
                        ['key' => 'federal_id', 'label' => 'Federal ID', 'printWidth' => '12%'],
                        ['key' => 'ministry_department', 'label' => 'Ministry/Dept', 'printWidth' => '15%'],
                        ['key' => 'employee_id_number', 'label' => 'Employee ID', 'printWidth' => '12%'],
                        ['key' => 'verified_text', 'label' => 'Verified', 'printWidth' => '10%'],
                    ],
                ],

                // Single record print configuration
                'single_record' => [
                    'view' => 'pdf-layout',
                    'document_title' => 'Employee Insurance Record Details',
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

                // Field transformations used across exports/prints
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

                'relationships' => ['patient', 'policy'],
                'default_sort' => ['kebele_id', 'asc'],
                'per_page' => 5,
            ];
        }

        return self::$employeeInsuranceRecordConfig;
    }

    public static function getInventoryTransactionConfig(): array
    {
        return [
            'searchable_fields' => ['transaction_type', 'item.name'],
            'sortable_fields' => ['transaction_type', 'quantity', 'created_at'],
            'default_sort' => 'created_at',
            'with_relations' => ['item', 'performedBy'],
            
            'csv' => [
                'headers' => ['Item', 'Transaction Type', 'Quantity', 'Performed By', 'Date'],
                'fields' => [
                    ['field' => 'item.name', 'default' => '-'],
                    'transaction_type',
                    'quantity',
                    ['field' => 'performedBy.first_name', 'default' => '-'],
                    'created_at',
                ],
                'filename' => 'inventory_transactions.csv'
            ],
            
            'pdf' => [
                'title' => 'Inventory Transactions List',
                'document_title' => 'Inventory Transactions List',
                'filename' => 'inventory_transactions.pdf',
                'orientation' => 'landscape',
                'include_index' => false,
                'fields' => [
                    'item_name' => ['field' => 'item.name', 'default' => '-'],
                    'transaction_type' => 'transaction_type',
                    'quantity' => 'quantity',
                    'performed_by' => ['field' => 'performedBy.first_name', 'default' => '-'],
                    'created_at' => 'created_at',
                ],
                'columns' => [
                    ['key' => 'item_name', 'label' => 'Item'],
                    ['key' => 'transaction_type', 'label' => 'Transaction Type'],
                    ['key' => 'quantity', 'label' => 'Quantity'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'created_at', 'label' => 'Date'],
                ]
            ],
            
            'print_current' => [
                'title' => 'Inventory Transactions (Current View)',
                'document_title' => 'Inventory Transactions (Current View)',
                'filename' => 'inventory_transactions_current.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'fields' => [
                    'item_name' => ['field' => 'item.name', 'default' => '-'],
                    'transaction_type' => 'transaction_type',
                    'quantity' => 'quantity',
                    'performed_by' => ['field' => 'performedBy.first_name', 'default' => '-'],
                    'created_at' => 'created_at',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'item_name', 'label' => 'Item'],
                    ['key' => 'transaction_type', 'label' => 'Transaction Type'],
                    ['key' => 'quantity', 'label' => 'Quantity'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'created_at', 'label' => 'Date'],
                ]
            ],
            
            'print_all' => [
                'view' => 'pdf-layout',
                'title' => 'All Inventory Transactions',
                'document_title' => 'All Inventory Transactions',
                'filename' => 'inventory_transactions_all.pdf',
                'orientation' => 'landscape',
                'include_index' => true,
                'default_sort' => 'created_at',
                'fields' => [
                    'item_name' => ['field' => 'item.name', 'default' => '-'],
                    'transaction_type' => 'transaction_type',
                    'quantity' => 'quantity',
                    'performed_by' => ['field' => 'performedBy.first_name', 'default' => '-'],
                    'created_at' => 'created_at',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'item_name', 'label' => 'Item'],
                    ['key' => 'transaction_type', 'label' => 'Transaction Type'],
                    ['key' => 'quantity', 'label' => 'Quantity'],
                    ['key' => 'performed_by', 'label' => 'Performed By'],
                    ['key' => 'created_at', 'label' => 'Date'],
                ]
            ],
            
            'single_record' => [
                'fields' => [
                    'Item' => ['field' => 'item.name', 'default' => '-'],
                    'Transaction Type' => 'transaction_type',
                    'Quantity' => 'quantity',
                    'Performed By' => ['field' => 'performedBy.first_name', 'default' => '-'],
                    'Date' => 'created_at',
                ]
            ]
        ];
    }

    
}
