<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Milenmk\LaravelSimpleDatatables\Table\Filters\SelectFilter;
use Milenmk\LaravelSimpleDatatables\Table\Table;

/**
 * @see \Milenmk\LaravelSimpleDatatables\Contracts\WithFiltersInterface
 */
trait WithFilters
{
    public bool $showFilters = false;

    public array $tableFilterViews = [];

    public array $filters = [];

    public int $appliedFiltersCount = 0;

    public function initializeFilters(): void
    {
        $this->prepareFilterViews();
    }

    public function getShowFilters(): bool
    {
        return $this->showFilters;
    }

    public function setShowFilters(bool $show): void
    {
        $this->showFilters = $show;
    }

    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;
    }

    public function getTableFilterViews(): array
    {
        return $this->tableFilterViews;
    }

    public function resetFilters(): void
    {
        foreach ($this->table(new Table)->getFilters() as $filter) {
            unset($this->filters[$filter->name]);
            if ($filter->persistInSession) {
                session()->forget("filters.{$filter->name}");
            }
        }

        $this->appliedFiltersCount = 0;

        $this->resetPage();
    }

    public function getAppliedFiltersCount(): int
    {
        return $this->appliedFiltersCount;
    }

    public function removeFilter(string $filterName, string|int $filterValue): void
    {
        if (is_array($this->filters[$filterName])) {
            // Remove the specific value from the array
            $key = array_search($filterValue, $this->filters[$filterName]);
            if ($key !== false) {
                unset($this->filters[$filterName][$key]);
                $this->filters[$filterName] = array_values($this->filters[$filterName]); // Reset array keys
                if (empty($this->filters[$filterName])) {
                    unset($this->filters[$filterName]);
                }
            }
        } else {
            // Unset the entire filter if it's not an array
            unset($this->filters[$filterName]);
        }

        $this->resetPage(); // Reset pagination
    }

    protected function prepareFilterViews(): array
    {
        $table = new Table;
        $filters = $this->table($table)->getFilters();
        $modelClass = $this->table($table)->getModelClass();

        foreach ($filters as $filter) {
            $filter->modelClass = $modelClass;
        }

        return collect($filters)->map(function ($filter) {
            $filterData = [
                'name' => $filter->name,
                'view' => $filter->render(),
                'filterLabel' => $filter->label,
            ];

            if ($filter instanceof SelectFilter) {
                $filterData['filterOptions'] = $filter->getOptions();
            }

            return $filterData;
        })->toArray();
    }

    protected function applyFiltersToQuery($query): void
    {
        $this->appliedFiltersCount = 0;

        foreach ($this->table(new Table)->getFilters() as $filter) {
            if (isset($this->filters[$filter->name]) && ! empty($this->filters[$filter->name])) {
                call_user_func([$filter, 'apply'], $query, $this->filters[$filter->name]);
                if ($filter->persistInSession) {
                    session(["filters.{$filter->name}" => $this->filters[$filter->name]]);
                }
                $this->appliedFiltersCount++;
            }
        }
    }
}
