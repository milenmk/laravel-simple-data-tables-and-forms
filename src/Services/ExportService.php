<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Milenmk\LaravelSimpleDatatables\Table\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
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
                            $value = $row->{$key} ?? '';

                            // Handle Enum values
                            if (is_object($value) && enum_exists(get_class($value))) {
                                if (method_exists($value, 'value')) {
                                    $value = $value->value;
                                } elseif (property_exists($value, 'name')) {
                                    $value = $value->name;
                                } else {
                                    $value = (string) $value;
                                }
                            }

                            // Handle other object types that need conversion
                            if (is_object($value) && method_exists($value, '__toString')) {
                                $value = (string) $value;
                            } elseif (is_object($value)) {
                                $value = json_encode($value);
                            } elseif (is_array($value)) {
                                $value = json_encode($value);
                            }

                            $data[] = $value;
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
            ]
        );
    }

    /**
     * Export table data to Excel.
     *
     * Note: This is a basic implementation. For a more robust solution,
     * consider using a package like PhpSpreadsheet or Laravel Excel.
     */
    public function toExcel(Builder $query, Table $table): StreamedResponse
    {
        // For now, this is just a CSV with an Excel extension
        // In a real implementation, you would use a proper Excel library
        $filename = $this->getExportFilename('xls'); // Using .xls instead of .xlsx for better compatibility

        $columns = collect($table->getColumns())
            ->filter(fn ($column) => $column->visible)
            ->values();

        $headers = $columns->pluck('label')->toArray();
        $keys = $columns->pluck('key')->toArray();

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
                            $value = $row->{$key} ?? '';

                            // Handle Enum values
                            if (is_object($value) && enum_exists(get_class($value))) {
                                if (method_exists($value, 'value')) {
                                    $value = $value->value;
                                } elseif (property_exists($value, 'name')) {
                                    $value = $value->name;
                                } else {
                                    $value = (string) $value;
                                }
                            }

                            // Handle other object types that need conversion
                            if (is_object($value) && method_exists($value, '__toString')) {
                                $value = (string) $value;
                            } elseif (is_object($value)) {
                                $value = json_encode($value);
                            } elseif (is_array($value)) {
                                $value = json_encode($value);
                            }

                            $data[] = $value;
                        }

                        fputcsv($handle, $data);
                        $count++;
                    }
                });

                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'application/vnd.ms-excel', // Correct MIME type for .xls
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]
        );
    }

    /**
     * Export table data to PDF.
     *
     * Note: For now, we'll export as CSV with a .pdf extension.
     * For a real implementation, you would need to use a PDF generation library.
     */
    public function toPdf(Builder $query, Table $table): StreamedResponse
    {
        // For now, we'll just return a CSV with a .pdf extension
        $filename = $this->getExportFilename('csv');

        $columns = collect($table->getColumns())
            ->filter(fn ($column) => $column->visible)
            ->values();

        $headers = $columns->pluck('label')->toArray();
        $keys = $columns->pluck('key')->toArray();

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
                            $value = $row->{$key} ?? '';

                            // Handle Enum values
                            if (is_object($value) && enum_exists(get_class($value))) {
                                if (method_exists($value, 'value')) {
                                    $value = $value->value;
                                } elseif (property_exists($value, 'name')) {
                                    $value = $value->name;
                                } else {
                                    $value = (string) $value;
                                }
                            }

                            // Handle other object types that need conversion
                            if (is_object($value) && method_exists($value, '__toString')) {
                                $value = (string) $value;
                            } elseif (is_object($value)) {
                                $value = json_encode($value);
                            } elseif (is_array($value)) {
                                $value = json_encode($value);
                            }

                            $data[] = $value;
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
            ]
        );
    }

    /**
     * Generate export filename.
     */
    protected function getExportFilename(string $extension): string
    {
        $timestamp = date('Y-m-d_H-i-s');

        return "export_{$timestamp}.{$extension}";
    }
}
