<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Milenmk\LaravelSimpleDatatables\Table\Table;

class SearchService
{
    /**
     * Apply search to the query based on configuration.
     */
    public function applySearch(Builder $query, string $search, Table $table): Builder
    {
        // If search is empty or doesn't meet minimum character requirement, return query as is
        $minChars = Config::get('simple-datatables.search.min_characters', 2);
        if (empty($search) || strlen($search) < $minChars) {
            return $query;
        }

        // Get search mode from config
        $searchMode = Config::get('simple-datatables.search.default_mode', 'like');
        $enableFulltext = Config::get('simple-datatables.search.enable_fulltext', false);

        // Apply search based on mode
        switch ($searchMode) {
            case 'exact':
                return $this->applyExactSearch($query, $search, $table);
            case 'fulltext':
                if ($enableFulltext && $this->canUseFulltext($query, $table)) {
                    return $this->applyFulltextSearch($query, $search, $table);
                }

                // Fallback to like search if fulltext is not available
                return $this->applyLikeSearch($query, $search, $table);
            case 'like':
            default:
                return $this->applyLikeSearch($query, $search, $table);
        }
    }

    /**
     * Apply LIKE search to the query.
     */
    protected function applyLikeSearch(Builder $query, string $search, Table $table): Builder
    {
        return $query->where(function ($q) use ($search, $table) {
            foreach ($table->getColumns() as $column) {
                if ($column->searchable) {
                    $q->orWhere($column->key, 'LIKE', "%{$search}%");
                }
            }
        });
    }

    /**
     * Apply exact match search to the query.
     */
    protected function applyExactSearch(Builder $query, string $search, Table $table): Builder
    {
        return $query->where(function ($q) use ($search, $table) {
            foreach ($table->getColumns() as $column) {
                if ($column->searchable) {
                    $q->orWhere($column->key, '=', $search);
                }
            }
        });
    }

    /**
     * Apply fulltext search to the query if supported by the database.
     */
    protected function applyFulltextSearch(Builder $query, string $search, Table $table): Builder
    {
        $connection = $query->getConnection();
        $driver = $connection->getDriverName();
        $searchableColumns = collect($table->getColumns())
            ->filter(fn ($column) => $column->searchable)
            ->pluck('key')
            ->toArray();

        if (empty($searchableColumns)) {
            return $query;
        }

        // Apply database-specific fulltext search
        switch ($driver) {
            case 'mysql':
                $columns = implode(',', $searchableColumns);

                return $query->whereRaw("MATCH({$columns}) AGAINST(? IN BOOLEAN MODE)", [$search . '*']);

            case 'pgsql':
                return $query->where(function ($q) use ($searchableColumns, $search) {
                    foreach ($searchableColumns as $column) {
                        $q->orWhereRaw("{$column}::text ILIKE ?", ["%{$search}%"]);
                    }
                });

            default:
                // Fallback to LIKE search for unsupported drivers
                return $this->applyLikeSearch($query, $search, $table);
        }
    }

    /**
     * Check if fulltext search can be used for the given query and columns.
     */
    protected function canUseFulltext(Builder $query, Table $table): bool
    {
        $connection = $query->getConnection();
        $driver = $connection->getDriverName();

        // Only MySQL supports FULLTEXT indexes
        if ($driver !== 'mysql') {
            return false;
        }

        // Get the table name from the query
        $from = $query->getQuery()->from;
        if (! $from || ! is_string($from)) {
            return false;
        }

        // Get searchable columns
        $searchableColumns = collect($table->getColumns())
            ->filter(fn ($column) => $column->searchable)
            ->pluck('key')
            ->toArray();

        if (empty($searchableColumns)) {
            return false;
        }

        // Check if there's a FULLTEXT index on any of the searchable columns
        $indexes = DB::select("SHOW INDEX FROM {$from} WHERE Index_type = 'FULLTEXT'");
        $indexedColumns = collect($indexes)->pluck('Column_name')->toArray();

        // Check if any searchable column has a FULLTEXT index
        foreach ($searchableColumns as $column) {
            if (in_array($column, $indexedColumns)) {
                return true;
            }
        }

        return false;
    }
}
