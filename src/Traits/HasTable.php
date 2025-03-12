<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Traits;

use Illuminate\Contracts\View\View;
use Livewire\WithPagination;
use Milenmk\LaravelSimpleDatatables\Table\Table;

trait HasTable
{
    use WithPagination;
    use WithPerPage;
    use WithSearch;
    use WithSorting;

    public array|object $visibleColumns;

    public function mount(): void
    {
        $this->visibleColumns = session(
            'visible_columns',
            collect($this->table(new Table)->getColumns())
                ->mapWithKeys(fn ($column) => [$column->key => $column->visible])
                ->toArray(),
        );
    }

    abstract public function table(Table $table): Table;

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
                        return $column->visible($this->visibleColumns[$column->key] ?? true);
                    })
                    ->toArray(),
            )
            ->render();
    }

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
        $this->visibleColumns[$columnKey] = ! ($this->visibleColumns[$columnKey] ?? true);
        session(['visible_columns' => $this->visibleColumns]);
    }
}
