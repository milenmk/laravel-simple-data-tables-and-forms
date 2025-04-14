# Export Functionality

Laravel Simple Datatables provides built-in export functionality that allows users to export table data.

## Enabling Export

Export functionality is enabled by default. You can control it through the configuration:

```php
// config/simple-datatables.php
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

## Using Export in Your Components

The export functionality is automatically included in components that use the `HasTable` trait. The export button will appear in the table header.

### Current Implementation Status

**Important Note**: While the configuration allows for specifying multiple export formats, the current implementation in the `WithExport` trait only supports CSV export. The `ExportService` class contains placeholder implementations for Excel and PDF exports that actually return CSV data.

If you need Excel or PDF export functionality, you will need to implement these features yourself by extending the `ExportService` class or overriding the `export` method in your Livewire component.

### Customizing Export Formats

You can customize which export formats are available for a specific table by passing them to the table component:

```php
// In your Livewire component
public function render()
{
    return view('livewire.my-table', [
        'exportFormats' => ['csv'], // Currently only CSV is fully implemented
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
    
    // Currently, only CSV export is fully implemented
    return $exportService->toCsv($query, $table);
    
    // If you've implemented your own Excel or PDF export:
    // return match($format) {
    //     'excel' => $exportService->toExcel($query, $table),
    //     'pdf' => $exportService->toPdf($query, $table),
    //     default => $exportService->toCsv($query, $table),
    // };
}
```

## Export Formats

### CSV Export

CSV export is the default and currently the only fully implemented export option. It exports data as a comma-separated values file.

### Excel Export (Placeholder Implementation)

The current implementation of Excel export in the `ExportService` class is a placeholder that returns CSV data with an Excel MIME type. To implement proper Excel export, you would need to extend the service with a library like PhpSpreadsheet or Laravel Excel.

To implement proper Excel export, you would need to add the following package to your project:

```
composer require maatwebsite/excel
```

### PDF Export (Placeholder Implementation)

The current implementation of PDF export in the `ExportService` class is a placeholder that returns CSV data. To implement proper PDF export, you would need to extend the service with a PDF generation library.

To implement proper PDF export, you would need to add the following package to your project:

```
composer require barryvdh/laravel-dompdf
```

## Customizing the Export Button

You can customize the export button by publishing the component view:

```
php artisan vendor:publish --tag=laravel-simple-datatables-views
```

Then edit the `resources/views/vendor/laravel-simple-datatables/components/export.blade.php` file.

## Customizing Export Filenames

You can customize the filename of exported files by implementing the `exportFilename` method in your Livewire component:

```php
public function exportFilename(string $format): string
{
    return 'users-report-' . date('Y-m-d') . '.' . $format;
}
```