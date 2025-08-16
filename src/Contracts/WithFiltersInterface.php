<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface WithFiltersInterface
{
    /**
     * Toggle filters visibility.
     */
    public function toggleFilters(): void;

    /**
     * Reset all filters.
     */
    public function resetFilters(): void;

    /**
     * Apply filters to the query.
     */
    public function applyFiltersToQuery(Builder $query): void;

    /**
     * Initialize filters.
     */
    public function initializeFilters(): void;
}
