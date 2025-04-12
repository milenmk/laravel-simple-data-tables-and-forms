# Export Functionality

Laravel Simple Datatables provides built-in export functionality that allows users to export table data in various formats.

## Enabling Export

Export functionality is enabled by default. You can control it through the configuration:

```php
// config/simple-datatables.php
'export' => [
    // Enable export functionality
    'enable' => true,
    
    // Available export formats
    'formats' => ['csv'],
    
    // Default export format
    'default_format' => 'csv',
    
    // Maximum rows for export (0 for unlimited)
    'max_rows' => 10000,
],
```

## Using Export in Your Components

The export functionality is automatically included in components that use the `HasTable` trait. The export button will appear in the table header.

### Customizing Export Formats

You can customize which export formats are available for a specific table by passing them to the table component:

```php
// In your Livewire component
public function render()
{
    return view('livewire.my-table', [
        'exportFormats' => ['csv', 'excel'], // Only allow CSV and Excel exports
    ]);
}
```

### Implementing Custom Export Logic

If you need custom export logic, you can override the `export` method in your Livewire component:

```php
public function export(string $format = 'csv')
{
    // Your custom export logic here
    
    // Example: Add additional columns or transform data
    $table = app(\Milenmk\LaravelSimpleDatatables\Table\Table::class);
    $this->table($table);
    
    $query = $table->getQuery();
    
    // Apply your custom transformations
    
    // Then use the export service
    $exportService = app(\Milenmk\LaravelSimpleDatatables\Services\ExportService::class);
    
    return $exportService->toCsv($query, $table);
}
```

## Export Formats

### CSV Export

CSV export is the default and most lightweight option. It exports data as a comma-separated values file.

## Customizing the Export Button

You can customize the export button by publishing the component view:

```
php artisan vendor:publish --tag=laravel-simple-datatables-views
```

Then edit the `resources/views/vendor/laravel-simple-datatables/components/export.blade.php` file.