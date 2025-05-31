<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\View\View;
use Livewire\Attributes\Session;
use Livewire\WithPagination;
use Milenmk\LaravelSimpleDatatables\Services\CacheService;
use Milenmk\LaravelSimpleDatatables\Services\SearchService;
use Milenmk\LaravelSimpleDatatables\Services\SecurityService;
use Milenmk\LaravelSimpleDatatables\Table\Table;

/**
 * @see \Milenmk\LaravelSimpleDatatables\Contracts\HasTableInterface
 */
trait HasTable
{
    use WithExport;
    use WithFilters;
    use WithGrouping;
    use WithPagination;
    use WithPerPage;
    use WithSearch;
    use WithSorting;

    #[Session]
    public array|object $visibleColumns;

    public Stringable $componentName;

    public function mount(): void
    {
        $this->componentName = Str::of(class_basename($this))->snake();

        if (empty($this->visibleColumns)) {
            $this->visibleColumns = collect($this->table(new Table)->getColumns())
                ->mapWithKeys(fn ($column) => ["{$this->componentName}.{$column->key}" => $column->visible])
                ->toArray();
        }

        // Initialize filters from session if persistFilterInSession is true
        foreach ($this->table(new Table)->getFilters() as $filter) {
            if ($filter->persistInSession && session()->has("filters.{$filter->name}")) {
                $this->filters[$filter->name] = session("filters.{$filter->name}");
            }
        }

        $this->mountWithGrouping();

        // Initialize filters
        $this->initializeFilters();
    }

    abstract public function table(Table $table): Table;

    public function toggleValue(string $itemId, string $field): void
    {
        // Find the model by ID
        $model = $this->getModel($itemId);

        if ($model) {
            // Get the current value of the field
            $currentValue = (bool) $model->{$field};

            // Toggle the value
            $newValue = ! $currentValue;

            // Update the model's field in the database
            $model->update([$field => $newValue]);

            $this->dispatch('toggleUpdated', $model->id);
        }
    }

    public function getModel(string $itemId): ?Model
    {
        $table = new Table;

        return $this->table($table)->getModelInstance($itemId);
    }

    public function getTableProperty(): View
    {
        // Use dependency injection through app() helper
        $searchService = app(SearchService::class);
        $cacheService = app(CacheService::class);
        $securityService = app(SecurityService::class);

        // Create table instance
        $table = app(Table::class);

        // Start with the base query defined in the component
        $query = $this->table($table)->getQuery();

        // Sanitize search input
        $sanitizedSearch = $securityService->sanitizeInput($this->search);

        // Apply search filter if there's any input using the search service
        if (! empty($sanitizedSearch)) {
            $query->where(function ($query) use ($sanitizedSearch, $table) {
                foreach ($table->getColumns() as $column) {
                    if ($column->searchable) {
                        $fields = $column->searchFields ?: [$column->key];
                        foreach ($fields as $field) {
                            if (str_contains($field, '.')) {
                                // Handle relationship search
                                [$relation, $relatedField] = explode('.', $field, 2);
                                $query->orWhereHas($relation, function ($query) use ($relatedField, $sanitizedSearch) {
                                    $query->where($relatedField, 'LIKE', "%{$sanitizedSearch}%");
                                });
                            } else {
                                // Handle direct field search
                                $query->orWhere($field, 'LIKE', "%{$sanitizedSearch}%");
                            }
                        }
                    }
                }
            });
        }

        // Apply grouping
        if ($this->selectedGroup) {
            $query->groupBy('id', $this->selectedGroup);
        }

        // Apply sorting with validation
        if (! empty($this->sortField)) {
            $allowedFields = collect($table->getColumns())
                ->pluck('key')
                ->toArray();
            if (
                $securityService->validateSortField($this->sortField, $allowedFields) &&
                $securityService->validateSortDirection($this->sortDir)
            ) {
                $query->orderBy($this->sortField, $this->sortDir);
            }
        }

        // Apply filters to query
        $this->applyFiltersToQuery($query);

        // Cache the column configuration
        $columns = $cacheService->remember('columns_' . $this->componentName, function () use ($table) {
            return collect($table->getColumns())
                ->map(function ($column) {
                    $column->visible =
                        $this->visibleColumns["{$this->componentName}.{$column->key}"] ?? $column->visible;

                    return $column;
                })
                ->toArray();
        });

        // Ensure the query is paginated before passing it to the table
        $paginatedResults = $query->paginate($this->perPage);

        $table->setSelectedGroupFromTrait($this->selectedGroup);
        $table->setCollapsedGroupsFromTrait($this->collapsedGroups);
        $table->setShowFilters($this->getShowFilters());
        $table->setTableFilters($this->prepareFilterViews());
        $table->setFiltersValues($this->filters);

        // Set the component
        foreach ($table->getFilters() as $filter) {
            $filter->setComponent($this);
        }

        return $this->table($table)
            ->query($paginatedResults)
            ->schema($columns)
            ->groups($table->getGroups())
            ->render();
    }

    public function toggleColumnVisibility(string $columnKey): void
    {
        $prefixedKey = "{$this->componentName}.{$columnKey}";

        $this->visibleColumns[$prefixedKey] = ! ($this->visibleColumns[$prefixedKey] ?? true);

        // Clear the cache to ensure the updated visibility is reflected
        $cacheService = app(CacheService::class);
        $cacheService->forget('columns_' . $this->componentName);
    }
}
