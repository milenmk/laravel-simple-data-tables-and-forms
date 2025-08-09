<?php

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

        // Enable rate limiting for search operations
        'rate_limiting' => [
            'enable' => true,
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],

        // Enable input sanitization
        'sanitize_input' => true,
    ],
];
