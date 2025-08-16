# Data Export

Laravel Simple Datatables And Forms provides comprehensive export functionality that allows users to export table data
in multiple
formats including CSV, Excel, and PDF.

## Quick Start

Export functionality is automatically enabled when you use the `HasTable` trait. Users will see an export button in the
table header that allows them to download data in their preferred format.

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasTable;

class UserList extends Component
{
    use HasTable; // Export functionality is automatically available

    public function table(Table $table): Table
    {
        return $table->query(User::query())->schema([
            TextColumn::make('name')->exportable(),
            TextColumn::make('email')->exportable(),
            // Other columns...
        ]);
    }
}
```

## Configuration

### Global Export Settings

Configure export behavior in `config/simple-datatables-and-forms.php`:

```php
'export' => [
    // Enable export functionality globally
    'enable' => true,

    // Available export formats
    'formats' => ['csv', 'excel', 'xlsx', 'xls', 'pdf'],

    // Default export format
    'default_format' => 'csv',

    // Maximum rows for export (0 for unlimited)
    'max_rows' => 10000,

    // Custom filename prefix
    'filename_prefix' => 'export',

    // Include timestamps in filenames
    'include_timestamp' => true,

    // Date format for timestamps
    'timestamp_format' => 'Y-m-d_H-i-s',
],
```

### Per-Table Export Configuration

You can customize export settings for individual tables:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([/* columns */])
        ->export([
            'formats' => ['csv', 'excel'], // Only allow CSV and Excel
            'filename' => 'users_export',  // Custom filename
            'max_rows' => 5000,           // Limit to 5000 rows
        ]);
}
```

## Required Dependencies

### For Excel Export (XLS/XLSX)

```bash
composer require phpoffice/phpspreadsheet
```

### For PDF Export

```bash
composer require barryvdh/laravel-dompdf
```

**Note:** If these packages are not installed, the export will gracefully fall back to CSV format while maintaining the
requested file extension.

## Column Export Control

### Making Columns Exportable

By default, all visible columns are included in exports. You can control this behavior:

```php
TextColumn::make('name')
    ->exportable(),        // Explicitly mark as exportable

TextColumn::make('email')
    ->exportable(false),   // Exclude from exports

TextColumn::make('internal_notes')
    ->exportOnly(),        // Only show in exports, not in table

TextColumn::make('formatted_date')
    ->hidden()             // Hidden columns are excluded from exports
    ->exportable(),        // Unless explicitly marked as exportable
```

### Custom Export Values

Provide different values for export vs. display:

```php
TextColumn::make('status')
    ->value(fn($row) => $row->status->getLabel()) // Display value
    ->exportValue(fn($row) => $row->status->value), // Export value

TextColumn::make('created_at')
    ->format(fn($value) => $value->diffForHumans()) // Display: "2 days ago"
    ->exportValue(fn($row) => $row->created_at->format('Y-m-d H:i:s')), // Export: "2024-01-15 14:30:00"
```

## Export Formats

### CSV Export

CSV is the default format and requires no additional dependencies:

```php
// Automatically available - no configuration needed
```

### Excel Export

Supports both XLS and XLSX formats:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([/* columns */])
        ->export([
            'formats' => ['xlsx', 'xls'],
            'excel' => [
                'sheet_name' => 'Users',
                'include_headers' => true,
                'auto_size_columns' => true,
            ],
        ]);
}
```

### PDF Export

Generate PDF exports with custom styling:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([/* columns */])
        ->export([
            'formats' => ['pdf'],
            'pdf' => [
                'orientation' => 'landscape', // or 'portrait'
                'paper_size' => 'A4',
                'title' => 'User Report',
                'include_logo' => true,
                'logo_path' => public_path('images/logo.png'),
            ],
        ]);
}
```

## Advanced Export Features

### Custom Export Queries

Modify the query used for exports:

```php
public function getExportQuery()
{
    return User::query()
        ->with(['profile', 'roles']) // Include relationships for export
        ->where('status', 'active')  // Only export active users
        ->orderBy('created_at', 'desc');
}
```

### Export Preprocessing

Process data before export:

```php
public function preprocessExportData($data)
{
    return $data->map(function ($row) {
        // Add calculated fields
        $row->full_address = $row->address . ', ' . $row->city . ', ' . $row->country;

        // Format sensitive data
        $row->phone = $this->formatPhoneNumber($row->phone);

        return $row;
    });
}
```

### Custom Export Headers

Define custom headers for exports:

```php
public function getExportHeaders(): array
{
    return [
        'name' => 'Full Name',
        'email' => 'Email Address',
        'created_at' => 'Registration Date',
        'status' => 'Account Status',
    ];
}
```

## Export Events

Listen to export events for logging or additional processing:

```php
// In your component
public function exportStarted($format, $filename)
{
    Log::info("Export started: {$format} format, filename: {$filename}");
}

public function exportCompleted($format, $filename, $rowCount)
{
    Log::info("Export completed: {$rowCount} rows exported to {$filename}");
}

public function exportFailed($format, $error)
{
    Log::error("Export failed: {$format} format, error: {$error}");
}
```

## Security Considerations

### Access Control

Implement proper authorization for exports:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([/* columns */])
        ->export([
            'authorize' => fn() => auth()->user()->can('export-users'),
        ]);
}
```

### Data Sanitization

Sanitize sensitive data in exports:

```php
TextColumn::make('ssn')
    ->exportValue(fn($row) => '***-**-' . substr($row->ssn, -4)),

TextColumn::make('credit_card')
    ->exportValue(fn($row) => '**** **** **** ' . substr($row->credit_card, -4)),
```

## Performance Optimization

### Large Dataset Handling

For large datasets, consider chunking:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([/* columns */])
        ->export([
            'chunk_size' => 1000,    // Process 1000 rows at a time
            'memory_limit' => '512M', // Increase memory limit
            'timeout' => 300,        // 5 minute timeout
        ]);
}
```

### Background Exports

For very large exports, consider using queued jobs:

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([/* columns */])
        ->export([
            'queue' => true,                    // Use queue for large exports
            'queue_threshold' => 10000,         // Queue if more than 10k rows
            'notification_email' => auth()->user()->email,
        ]);
}
```

## Troubleshooting

### Common Issues

**Memory limit exceeded:**

```php
// Increase memory limit in export configuration
'export' => [
    'memory_limit' => '1G',
    'chunk_size' => 500,
],
```

**Timeout errors:**

```php
// Increase timeout for large exports
'export' => [
    'timeout' => 600, // 10 minutes
],
```

**Missing dependencies:**

```bash
# Install required packages
composer require phpoffice/phpspreadsheet barryvdh/laravel-dompdf
```

**Permission errors:**

```php
// Ensure proper file permissions
'export' => [
    'temp_path' => storage_path('app/exports'),
],
```

### Debug Mode

Enable debug mode for troubleshooting:

```php
'export' => [
    'debug' => true, // Enable detailed error logging
],
```

## Examples

### Basic Export Setup

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([
            TextColumn::make('name')->exportable(),
            TextColumn::make('email')->exportable(),
            TextColumn::make('created_at')
                ->format(fn($value) => $value->diffForHumans())
                ->exportValue(fn($row) => $row->created_at->format('Y-m-d')),
        ]);
}
```

### Advanced Export Configuration

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([/* columns */])
        ->export([
            'formats' => ['csv', 'xlsx', 'pdf'],
            'filename' => 'users_' . date('Y-m-d'),
            'authorize' => fn() => auth()->user()->can('export-users'),
            'excel' => [
                'sheet_name' => 'Active Users',
                'auto_size_columns' => true,
            ],
            'pdf' => [
                'orientation' => 'landscape',
                'title' => 'User Directory',
            ],
        ]);
}
```

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
    $table = app(\Milenmk\LaravelSimpleDatatablesAndForms\Table\Table::class);
    $this->table($table);

    $query = $table->getQuery();

    // Apply your custom transformations

    // Then use the export service
    $exportService = app(\Milenmk\LaravelSimpleDatatablesAndForms\Services\ExportService::class);

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

CSV export is the default and most lightweight option. It exports data as a comma-separated values file. This format
doesn't require any additional packages.

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

PDF export creates a portable document format file that maintains consistent formatting across different devices and
platforms. This format is ideal for reports that need to be printed or shared formally.

PDF export requires the Laravel DomPDF package:

```bash
composer require barryvdh/laravel-dompdf
```

If DomPDF is not installed, the export will fall back to CSV format with a PDF extension.

## Customizing the Export Button

You can customize the export button by publishing the component view:

```bash
php artisan vendor:publish --tag=laravel-simple-datatables-and-forms-views
```

Then edit the `resources/views/vendor/laravel-simple-datatables-and-forms/components/export.blade.php` file.

## Customizing Export Filenames

You can customize the filename of exported files by implementing the `exportFilename` method in your Livewire component:

```php
public function exportFilename(string $format): string
{
    return 'users-report-' . date('Y-m-d') . '.' . $format;
}
```

## Customizing PDF Template

The package includes a default PDF template at `resources/views/exports/pdf.blade.php`. You can publish and customize
this template:

```bash
php artisan vendor:publish --tag=laravel-simple-datatables-and-forms-views
```

Then edit the `resources/views/vendor/laravel-simple-datatables-and-forms/exports/pdf.blade.php` file to match your
design requirements.

## Customizing the Export Button

You can customize the export button by publishing the component view:

```
php artisan vendor:publish --tag=laravel-simple-datatables-and-forms-views
```

Then edit the `resources/views/vendor/laravel-simple-datatables-and-forms/components/export.blade.php` file.

## Customizing Export Filenames

You can customize the filename of exported files by implementing the `exportFilename` method in your Livewire component:

```php
public function exportFilename(string $format): string
{
    return 'users-report-' . date('Y-m-d') . '.' . $format;
}
```
