<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Boilerplate Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the boilerplate
    |
     */

    'features' => [
        'authentication' => true,
        'user_management' => true,
        'staff_management' => true,
        'rbac' => true,
        'messaging' => true,
        'notifications' => true,
        'global_search' => true,
        'api_endpoints' => true,
        'export_functionality' => true,
        'mobile_integration' => true,
        'file_uploads' => true,
        'email_verification' => true,
        'password_reset' => true,
        'two_factor_auth' => false,
        'audit_logging' => false,
        'real_time_notifications' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules
    |--------------------------------------------------------------------------
    |
    | Configure which modules are enabled in the application
    |
     */

    'modules' => [
        'users' => true,
        'staff' => true,
        'roles' => true,
        'permissions' => true,
        'messages' => true,
        'notifications' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for performance optimization features
    |
     */

    'performance' => [
        'caching' => [
            'enabled' => true,
            'ttl' => [
                'default' => 600,
                'dropdowns' => 600,
                'search' => 300,
                'api' => 300,
            ],
        ],
        'pagination' => [
            'default_per_page' => 15,
            'max_per_page' => 100,
        ],
        'export' => [
            'chunk_size' => 1000,
            'max_rows' => 10000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for API functionality
    |
     */

    'api' => [
        'version' => 'v1',
        'rate_limiting' => [
            'enabled' => true,
            'requests_per_minute' => 60,
            'auth_requests_per_minute' => 100,
        ],
        'cors' => [
            'allowed_origins' => ['*'],
            'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
            'exposed_headers' => [],
            'max_age' => 0,
            'supports_credentials' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for security features
    |
     */

    'security' => [
        'password' => [
            'min_length' => 8,
            'require_confirmation' => true,
            'require_special_chars' => false,
        ],
        'session' => [
            'lifetime' => 120,
            'expire_on_close' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for notification system
    |
     */

    'notifications' => [
        'database' => true,
        'email' => true,
        'push' => false,
        'polling_interval' => 30, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for export functionality
    |
     */

    'export' => [
        'formats' => ['pdf', 'csv', 'xlsx'],
        'default_format' => 'csv',
        'storage_disk' => 'local',
        'storage_path' => 'exports',
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for global search functionality
    |
     */

    'search' => [
        'models' => [
            'users' => \App\Models\User::class,
            'staff' => \App\Models\Staff::class,
        ],
        'per_page' => 15,
        'min_query_length' => 3,
    ],

];
