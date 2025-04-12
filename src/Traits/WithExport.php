<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Illuminate\Support\Facades\Config;
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
        $exportService = app(\Milenmk\LaravelSimpleDatatables\Services\ExportService::class);

        // Get table instance
        $table = app(\Milenmk\LaravelSimpleDatatables\Table\Table::class);
        $this->table($table);

        // Get query
        $query = $table->getQuery();

        // Apply filters
        if (method_exists($this, 'applyFiltersToQuery') && ! empty($this->filters)) {
            $this->applyFiltersToQuery($query);
        }

        // Apply search
        if (! empty($this->search)) {
            $searchService = app(\Milenmk\LaravelSimpleDatatables\Services\SearchService::class);
            $query = $searchService->applySearch($query, $this->search, $table);
        }

        // Apply sorting
        if (! empty($this->sortField)) {
            $query->orderBy($this->sortField, $this->sortDir);
        }

        // Only support CSV export for now
        return $exportService->toCsv($query, $table);
    }
}
