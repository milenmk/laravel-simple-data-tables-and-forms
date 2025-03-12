<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class Table
{
    public string|array|null $heading = '';
    public bool $striped = false;

    protected LengthAwarePaginator|Builder|null $query = null;
    protected array $columns = [];

    public function query(Builder|LengthAwarePaginator $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get the current query.
     */
    public function getQuery(): Builder|LengthAwarePaginator|null
    {
        return $this->query;
    }

    public function schema(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get all columns.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function render(): View
    {
        return view('components.table.table', [
            'data' => $this->data(),
            'columns' => $this->columns,
            'heading' => $this->heading,
            'striped' => $this->striped,
        ]);
    }

    public function data(): Builder|LengthAwarePaginator
    {
        return $this->query;
    }

    public function heading(string|array $value): self
    {
        $this->heading = $value;

        return $this;
    }

    public function striped($value = true): static
    {
        $this->striped = $value;

        return $this;
    }
}
