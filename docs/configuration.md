# Configuration

Laravel Simple Datatables provides a comprehensive configuration system to customize the behavior and appearance of your tables.

## Publishing the Configuration

To publish the configuration file, run:

```bash
php artisan vendor:publish --tag=laravel-simple-datatables-config
```

This will create a `simple-datatables.php` file in your application's `config` directory.

## Pagination Settings

You can customize pagination settings for your tables through the configuration file:

```php
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
```

You can also customize pagination at the component level:

```php
// In your Livewire component
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([
            // Your columns here
        ])
        ->paginate(true) // Enable pagination (default)
        ->perPage(25); // Set items per page
}

// Override per page options
public function perPageOptions(): array
{
    return [5, 15, 30, 50, 100]; // Custom options
}
```

For more details on pagination, see the [Pagination](pagination.md) documentation.

## Configuration Options

### Asset Loading

Control how CSS and JavaScript assets are loaded:

```php
'assets' => [
    // Enable lazy loading of CSS assets
    'lazy_load' => true,
    
    // Enable CSS minification
    'minify_css' => true,
    
    // Defer JavaScript loading
    'defer_js' => true,
],
```

### Search Configuration

Configure search behavior and optimization settings:

```php
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
```

### Caching Configuration

Configure caching behavior for improved performance:

```php
'cache' => [
    // Enable caching for column definitions
    'enable' => true,
    
    // Cache lifetime in seconds (default: 1 hour)
    'lifetime' => 3600,
    
    // Cache key prefix
    'prefix' => 'simple_datatables_',
],
```

### Appearance Configuration

Configure the default appearance of tables:

```php
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
```

### Responsive Configuration

Configure responsive behavior for different screen sizes:

```php
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
```

### Export Configuration

Configure export functionality:

```php
'export' => [
    // Enable export functionality
    'enable' => true,
    
    // Available export formats
    'formats' => ['csv', 'excel', 'pdf'],
    
    // Default export format
    'default_format' => 'csv',
    
    // Maximum rows for export (0 for unlimited)
    'max_rows' => 10000,
    
    // Custom filename prefix (default is 'export')
    'filename_prefix' => 'export',
],
```

### Security Configuration

Configure security settings:

```php
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
```