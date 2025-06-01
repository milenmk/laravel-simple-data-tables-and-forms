<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Milenmk\LaravelSimpleDatatables\Table\Table;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Check if required package is available for the given format.
     */
    public function isPackageAvailable(string $format): bool
    {
        return match ($format) {
            'xls', 'xlsx', 'excel' => class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet'),
            'pdf' => class_exists('\Barryvdh\DomPDF\Facade\Pdf'),
            'csv' => true, // CSV doesn't require external packages
            default => true,
        };
    }

    /**
     * Get the required package name for the given format.
     */
    public function getRequiredPackage(string $format): ?string
    {
        return match ($format) {
            'xls', 'xlsx', 'excel' => 'phpoffice/phpspreadsheet',
            'pdf' => 'barryvdh/laravel-dompdf',
            default => null,
        };
    }

    /**
     * Export table data to Excel (generic method that chooses the appropriate format).
     *
     * Requires PhpSpreadsheet package.
     */
    public function toExcel(Builder $query, Table $table, string $format = 'xlsx'): StreamedResponse
    {
        return match ($format) {
            'xls' => $this->toXls($query, $table),
            default => $this->toXlsx($query, $table),
        };
    }

    /**
     * Export table data to Excel (XLS format).
     *
     * Requires PhpSpreadsheet package.
     */
    public function toXls(Builder $query, Table $table): StreamedResponse
    {
        if (! class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return $this->fallbackToCSV('xls');
        }

        $columns = collect($table->getColumns())
            ->filter(fn ($column) => $column->visible)
            ->values();

        $headers = $columns->pluck('label')->toArray();
        $keys = $columns->pluck('key')->toArray();

        $filename = $this->getExportFilename('xls');

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        foreach ($headers as $colIndex => $header) {
            $sheet->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
        }

        // Add data
        $maxRows = Config::get('simple-datatables.export.max_rows', 10000);
        $rowIndex = 2; // Start from row 2 (after headers)
        $count = 0;

        $query->chunk(100, function (Collection $chunk) use ($sheet, $keys, &$rowIndex, &$count, $maxRows) {
            foreach ($chunk as $row) {
                if ($maxRows > 0 && $count >= $maxRows) {
                    break;
                }

                foreach ($keys as $colIndex => $key) {
                    $value = data_get($row, $key, '');
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex, $this->formatValue($value));
                }

                $rowIndex++;
                $count++;
            }
        });

        // Create writer and prepare response
        $writer = new Xls($spreadsheet);

        return Response::stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ],
        );
    }

    /**
     * Export table data to CSV.
     */
    public function toCsv(Builder $query, Table $table): StreamedResponse
    {
        $columns = collect($table->getColumns())
            ->filter(fn ($column) => $column->visible)
            ->values();

        $headers = $columns->pluck('label')->toArray();
        $keys = $columns->pluck('key')->toArray();

        $filename = $this->getExportFilename('csv');

        return Response::stream(
            function () use ($query, $headers, $keys) {
                $handle = fopen('php://output', 'w');

                // Add headers
                fputcsv($handle, $headers);

                // Add data in chunks to avoid memory issues
                $maxRows = Config::get('simple-datatables.export.max_rows', 10000);
                $count = 0;

                $query->chunk(100, function (Collection $chunk) use ($handle, $keys, &$count, $maxRows) {
                    foreach ($chunk as $row) {
                        if ($maxRows > 0 && $count >= $maxRows) {
                            break;
                        }

                        $data = [];
                        foreach ($keys as $key) {
                            $value = data_get($row, $key, '');
                            $data[] = $this->formatValue($value);
                        }

                        fputcsv($handle, $data);
                        $count++;
                    }
                });

                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ],
        );
    }

    /**
     * Export table data to Excel (XLSX format).
     *
     * Requires PhpSpreadsheet package.
     */
    public function toXlsx(Builder $query, Table $table): StreamedResponse
    {
        if (! class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return $this->fallbackToCSV('xlsx');
        }

        $columns = collect($table->getColumns())
            ->filter(fn ($column) => $column->visible)
            ->values();

        $headers = $columns->pluck('label')->toArray();
        $keys = $columns->pluck('key')->toArray();

        $filename = $this->getExportFilename('xlsx');

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        foreach ($headers as $colIndex => $header) {
            $sheet->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
        }

        // Add data
        $maxRows = Config::get('simple-datatables.export.max_rows', 10000);
        $rowIndex = 2; // Start from row 2 (after headers)
        $count = 0;

        $query->chunk(100, function (Collection $chunk) use ($sheet, $keys, &$rowIndex, &$count, $maxRows) {
            foreach ($chunk as $row) {
                if ($maxRows > 0 && $count >= $maxRows) {
                    break;
                }

                foreach ($keys as $colIndex => $key) {
                    $value = data_get($row, $key, '');
                    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex, $this->formatValue($value));
                }

                $rowIndex++;
                $count++;
            }
        });

        // Create writer and prepare response
        $writer = new Xlsx($spreadsheet);

        return Response::stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ],
        );
    }

    /**
     * Export table data to PDF.
     *
     * Requires DomPDF package.
     */
    public function toPdf(Builder $query, Table $table): StreamedResponse
    {
        if (! class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            return $this->fallbackToCSV('pdf');
        }

        $columns = collect($table->getColumns())
            ->filter(fn ($column) => $column->visible)
            ->values();

        $headers = $columns->pluck('label')->toArray();
        $keys = $columns->pluck('key')->toArray();

        $filename = $this->getExportFilename('pdf');

        // Prepare data for PDF
        $data = [];
        $maxRows = Config::get('simple-datatables.export.max_rows', 10000);
        $count = 0;

        $query->chunk(100, function (Collection $chunk) use (&$data, $keys, &$count, $maxRows) {
            foreach ($chunk as $row) {
                if ($maxRows > 0 && $count >= $maxRows) {
                    break;
                }

                $rowData = [];
                foreach ($keys as $key) {
                    $value = data_get($row, $key, '');
                    $rowData[] = $this->formatValue($value);
                }

                $data[] = $rowData;
                $count++;
            }
        });

        // Generate PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('laravel-simple-datatables::exports.pdf', [
            'headers' => $headers,
            'data' => $data,
            'title' => 'Data Export',
        ]);

        return $pdf->download($filename);
    }

    /**
     * Fallback to CSV export when required packages are not installed.
     */
    protected function fallbackToCSV(string $extension): StreamedResponse
    {
        $response = $this->toCsv(app(Builder::class), app(Table::class));

        // Change the filename extension
        $headers = $response->headers->all();
        if (isset($headers['content-disposition'][0])) {
            $contentDisposition = $headers['content-disposition'][0];
            $newContentDisposition = str_replace('.csv', '.' . $extension, $contentDisposition);
            $response->headers->set('Content-Disposition', $newContentDisposition);
        }

        return $response;
    }

    /**
     * Generate export filename.
     */
    protected function getExportFilename(string $extension): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $prefix = Config::get('simple-datatables.export.filename_prefix', 'export');

        return "{$prefix}_{$timestamp}.{$extension}";
    }

    /**
     * Format a value for export.
     */
    protected function formatValue(mixed $value): string
    {
        // Handle Enum values
        if (is_object($value) && enum_exists(get_class($value))) {
            if (method_exists($value, 'value')) {
                return $value->value;
            } elseif (property_exists($value, 'name')) {
                return $value->name;
            } else {
                return (string) $value;
            }
        }

        // Handle other object types that need conversion
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        } elseif (is_object($value)) {
            return json_encode($value) ?: '';
        } elseif (is_array($value)) {
            return json_encode($value) ?: '';
        }

        return (string) $value;
    }
}
