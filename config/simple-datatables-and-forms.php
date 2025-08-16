<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Asset Loading Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how CSS and JavaScript assets are loaded.
    |
    */
    'assets' => [
        // Enable lazy loading of CSS assets
        'lazy_load' => true,

        // Enable CSS minification
        'minify_css' => true,

        // Defer JavaScript loading
        'defer_js' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Configuration
    |--------------------------------------------------------------------------
    |
    | Configure default pagination behavior.
    |
    */
    'pagination' => [
        // Default items per page
        'per_page' => 10,

        // Available pagination options
        'options' => [10, 25, 50, 100],

        // Show pagination summary
        'show_summary' => true,

        // Enable pagination by default
        'enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    |
    | Configure search behavior and optimization settings.
    |
    */
    'search' => [
        // Default search mode: 'like', 'exact', or 'fulltext'
        'default_mode' => 'like',

        // Enable full-text search when available
        'enable_fulltext' => false,

        // Minimum characters required to trigger search
        'min_characters' => 2,

        // Debounce time in milliseconds
        'debounce_time' => 300,
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior for improved performance.
    |
    */
    'cache' => [
        // Enable caching for column definitions
        'enable' => true,

        // Cache lifetime in seconds (default: 1 hour)
        'lifetime' => 3600,

        // Cache key prefix
        'prefix' => 'simple_datatables_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Appearance Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the default appearance of tables.
    |
    */
    'appearance' => [
        // Default theme: 'light', 'dark', or 'auto'
        'theme' => 'light',

        // Default striped rows
        'striped' => true,

        // Default hover effect
        'hover' => true,

        // Default border style: 'all', 'horizontal', 'vertical', 'outer', 'none'
        'borders' => 'all',

        // Default table size: 'sm', 'md', 'lg'
        'size' => 'md',
    ],

    /*
    |--------------------------------------------------------------------------
    | Responsive Configuration
    |--------------------------------------------------------------------------
    |
    | Configure responsive behavior for different screen sizes.
    |
    */
    'responsive' => [
        // Enable responsive tables
        'enable' => true,

        // Breakpoints for responsive behavior
        'breakpoints' => [
            'sm' => 640,
            'md' => 768,
            'lg' => 1024,
            'xl' => 1280,
            '2xl' => 1536,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    |
    | Configure export functionality.
    |
    */
    'export' => [
        // Enable export functionality
        'enable' => true,

        // Available export formats
        'formats' => ['csv', 'excel', 'xlsx', 'xls', 'pdf'],

        // Default export format
        'default_format' => 'csv',

        // Maximum rows for export (0 for unlimited)
        'max_rows' => 10000,

        // Custom filename prefix (default is 'export')
        'filename_prefix' => 'export',
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Configuration
    |--------------------------------------------------------------------------
    |
    | Configure form behavior and appearance.
    |
    */
    'form' => [
        // Default theme: 'light', 'dark', or 'auto'
        'theme' => 'light',

        // Default number of columns in forms
        'columns' => 2,

        // Enable client-side validation
        'client_validation' => true,

        // Enable real-time validation
        'realtime_validation' => false,

        // Default field appearance
        'field_appearance' => [
            'size' => 'md', // 'sm', 'md', 'lg'
            'border_radius' => 'md', // 'sm', 'md', 'lg', 'full'
        ],

        // Form animations
        'animations' => [
            'enable' => true,
            'duration' => 300, // milliseconds
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filters Configuration
    |--------------------------------------------------------------------------
    |
    | Configure filter behavior and appearance.
    |
    */
    'filters' => [
        // Default number of columns for filter layout
        'columns' => 6,

        // Enable responsive filter columns
        'responsive' => true,

        // Responsive breakpoints for filter columns
        'responsive_columns' => [
            'sm' => 1, // 1 column on small screens
            'md' => 2, // 2 columns on medium screens
            'lg' => 4, // 4 columns on large screens
            'xl' => 6, // 6 columns on extra large screens
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Configure security settings.
    |
    */
    'security' => [
        // Enable CSRF protection for all forms
        'csrf_protection' => true,

        // Enable rate limiting for search operations and form submissions
        'rate_limiting' => [
            'enable' => true,
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],

        // Enable input sanitization
        'sanitize_input' => true,

        // File upload security settings
        'max_file_size' => 10240, // KB (10MB)
        'allowed_file_types' => [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'svg',
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'ppt',
            'pptx',
            'txt',
            'csv',
            'zip',
            'rar',
        ],

        // Content Security Policy settings
        'csp' => [
            'enable' => true,
            'script_src' => "'self' 'unsafe-inline'",
            'style_src' => "'self' 'unsafe-inline'",
            'img_src' => "'self' data: https:",
        ],

        // Additional security headers
        'security_headers' => [
            'x_frame_options' => 'DENY',
            'x_content_type_options' => 'nosniff',
            'x_xss_protection' => '1; mode=block',
            'referrer_policy' => 'strict-origin-when-cross-origin',
        ],
    ],
];
