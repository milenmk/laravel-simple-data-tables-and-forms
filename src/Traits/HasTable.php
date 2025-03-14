<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\View\View;
use Livewire\Attributes\Session;
use Livewire\WithPagination;
use Milenmk\LaravelSimpleDatatables\Table\Table;

trait HasTable
{
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
            $this->visibleColumns = collect($this->columns)
                ->mapWithKeys(fn ($column) => ["{$this->componentName}.{$column->key}" => $column->visible])
                ->toArray();
        }
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

        if (! empty($this->sortField)) {
            $query->orderBy($this->sortField, $this->sortDir);
        }

        // Ensure the query is paginated before passing it to the table
        $paginatedResults = $query->paginate($this->perPage);

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
