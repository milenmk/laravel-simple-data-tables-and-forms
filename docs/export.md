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
    'formats' => ['csv', 'excel', 'xlsx', 'xls', 'pdf'],
    
    // Default export format
    'default_format' => 'csv',
    
    // Maximum rows for export (0 for unlimited)
    'max_rows' => 10000,
    
    // Custom filename prefix (default is 'export')
    'filename_prefix' => 'export',
],
```

## Required Packages for Export Formats

The package supports multiple export formats, but some require additional packages:

### For Excel Export (XLS/XLSX)

```bash
composer require phpoffice/phpspreadsheet
```

### For PDF Export

```bash
composer require barryvdh/laravel-dompdf
```

If these packages are not installed, the export will gracefully fall back to CSV format with the appropriate file extension.

## Using Export in Your Components

The export functionality is automatically included in components that use the `HasTable` trait. The export button will appear in the table header.

### Customizing Export Formats

You can customize which export formats are available for a specific table by passing them to the table component:

```php
// In your Livewire component
public function render()
{
    return view('livewire.my-table', [
        'exportFormats' => ['csv', 'xlsx', 'pdf'], // Specify available formats
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
    
    // Choose the appropriate export method based on format
    return match($format) {
        'excel' => $exportService->toExcel($query, $table),
        'xlsx' => $exportService->toXlsx($query, $table),
        'xls' => $exportService->toXls($query, $table),
        'pdf' => $exportService->toPdf($query, $table),
        default => $exportService->toCsv($query, $table),
    };
}
```

## Export Formats

### CSV Export

CSV export is the default and most lightweight option. It exports data as a comma-separated values file. This format doesn't require any additional packages.

### Excel Export (XLS and XLSX)

The package supports both older XLS format and newer XLSX format for Excel exports:

- **XLS**: The older Excel format, compatible with Excel 97-2003
- **XLSX**: The modern Excel format based on Open XML, compatible with Excel 2007 and later

Both formats require the PhpSpreadsheet package:

```bash
composer require phpoffice/phpspreadsheet
```

If PhpSpreadsheet is not installed, the export will fall back to CSV format with an Excel extension.

### PDF Export

PDF export creates a portable document format file that maintains consistent formatting across different devices and platforms. This format is ideal for reports that need to be printed or shared formally.

PDF export requires the Laravel DomPDF package:

```bash
composer require barryvdh/laravel-dompdf
```

If DomPDF is not installed, the export will fall back to CSV format with a PDF extension.

## Customizing the Export Button

You can customize the export button by publishing the component view:

```bash
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

## Customizing PDF Template

The package includes a default PDF template at `resources/views/exports/pdf.blade.php`. You can publish and customize this template:

```bash
php artisan vendor:publish --tag=laravel-simple-datatables-views
```

Then edit the `resources/views/vendor/laravel-simple-datatables/exports/pdf.blade.php` file to match your design requirements.

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