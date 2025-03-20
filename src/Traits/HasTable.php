<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\View\View;
use Livewire\Attributes\Session;
use Livewire\WithPagination;
use Milenmk\LaravelSimpleDatatables\Table\Table;

trait HasTable
{
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
        $model = $this->table($table)->getModelInstance($itemId);

        return $model;
    }

    public function getTableProperty(): View
    {
        $table = new Table;

        // Start with the base query defined in the component
        $query = $this->table($table)->getQuery();

        // Apply search filter if there's any input
        if (! empty($this->search)) {
            $query->where(function ($q) use ($table) {
                foreach ($table->getColumns() as $column) {
                    if ($column->searchable) {
                        $q->orWhere($column->key, 'LIKE', "%{$this->search}%");
                    }
                }
            });
        }

        // Apply grouping
        if ($this->selectedGroup) {
            $query->groupBy('id', $this->selectedGroup);
        }

        // Apply sorting
        if (! empty($this->sortField)) {
            $query->orderBy($this->sortField, $this->sortDir);
        }

        // Apply filters to query
        $this->applyFiltersToQuery($query);

        // Ensure the query is paginated before passing it to the table
        $paginatedResults = $query->paginate($this->perPage);

        $table->setSelectedGroupFromTrait($this->selectedGroup);
        $table->setCollapsedGroupsFromTrait($this->collapsedGroups);
        $table->setShowFilters($this->getShowFilters());
        $table->setTableFilters($this->prepareFilterViews());
        $table->setFiltersValues($this->filters);

        //Set the component
        foreach ($table->getFilters() as $filter) {
            $filter->setComponent($this);
        }

        return $this->table($table)
            ->query($paginatedResults)
            ->schema(
                collect($table->getColumns())
                    ->map(function ($column) {
                        $column->visible =
                            $this->visibleColumns["{$this->componentName}.{$column->key}"] ?? $column->visible;

                        return $column;
                    })
                    ->toArray(),
            )
            ->groups($table->getGroups())
            ->render();
    }

    abstract public function table(Table $table): Table;

    public function setSortBy($column): void
    {
        if ($this->sortField === $column) {
            $this->sortDir = $this->sortDir === 'ASC' ? 'DESC' : 'ASC';

            return;
        }

        $this->sortField = $column;
        $this->sortDir = 'ASC';
    }

    public function toggleColumnVisibility(string $columnKey): void
    {
        $prefixedKey = "{$this->componentName}.{$columnKey}";

        $this->visibleColumns[$prefixedKey] = ! ($this->visibleColumns[$prefixedKey] ?? true);
    }
}
