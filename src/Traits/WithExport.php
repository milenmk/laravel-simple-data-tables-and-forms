<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Illuminate\Support\Facades\Config;
use Milenmk\LaravelSimpleDatatables\Services\ExportService;
use Milenmk\LaravelSimpleDatatables\Services\SearchService;
use Milenmk\LaravelSimpleDatatables\Table\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @see \Milenmk\LaravelSimpleDatatables\Contracts\WithExportInterface
 */
trait WithExport
{
    /**
     * Export table data to the specified format.
     */
    public function export(string $format = 'csv'): ?StreamedResponse
    {
        // Check if export is enabled in config
        if (! Config::get('simple-datatables.export.enable', true)) {
            return null;
        }

        // Get available export formats
        $availableFormats = Config::get('simple-datatables.export.formats', ['csv', 'excel', 'pdf']);

        // Validate format
        if (! in_array($format, $availableFormats)) {
            $format = Config::get('simple-datatables.export.default_format', 'csv');
        }

        // Get export service
        $exportService = app(ExportService::class);

        // Get table instance
        $table = app(Table::class);
        $this->table($table);

        // Get query
        $query = $table->getQuery();

        // Apply filters
        if (method_exists($this, 'applyFiltersToQuery') && ! empty($this->filters)) {
            $this->applyFiltersToQuery($query);
        }

        // Apply search
        if (! empty($this->search)) {
            $searchService = app(SearchService::class);
            $query = $searchService->applySearch($query, $this->search, $table);
        }

        // Apply sorting
        if (! empty($this->sortField)) {
            $query->orderBy($this->sortField, $this->sortDir);
        }

        // Choose the appropriate export method based on format
        return match ($format) {
            'excel' => $exportService->toExcel($query, $table),
            'xlsx' => $exportService->toXlsx($query, $table),
            'xls' => $exportService->toXls($query, $table),
            'pdf' => $exportService->toPdf($query, $table),
            default => $exportService->toCsv($query, $table),
        };
    }

    /**
     * Get custom filename for export.
     * Override this method in your component to customize the export filename.
     */
    public function exportFilename(string $format): string
    {
        $timestamp = date('Y-m-d_H-i-s');
        $prefix = Config::get('simple-datatables.export.filename_prefix', 'export');
        $componentName = class_basename($this);

        return "{$prefix}_{$componentName}_{$timestamp}.{$format}";
    }
}
