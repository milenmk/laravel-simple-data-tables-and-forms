<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as FacadesView;
use Illuminate\View\View;
use Milenmk\LaravelSimpleDatatables\Table\Grouping\Group;

class Table
{
    public string|array|null $heading = '';
    public bool $striped = false;

    protected LengthAwarePaginator|Builder|null $query = null;
    protected array $columns = [];

    protected array $groups = [];

    protected ?string $modelClass = null;

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
        return FacadesView::make('laravel-simple-datatables::components.table.table', [
            'data' => $this->data(),
            'columns' => $this->columns,
            'heading' => $this->heading,
            'striped' => $this->striped,
            'groups' => $this->groups,
            'selectedGroup' => $this->getSelectedGroupFromTrait(),
            'collapsedGroups' => $this->getCollapsedGroupsFromTrait(),
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

    public function groups(array $groups): self
    {
        $this->groups = collect($groups)->map(function ($group) {
            if ($group instanceof Group) {
                return $group;
            }

            return is_array($group) ? Group::make(...$group) : Group::make($group);
        })->toArray();

        return $this;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function getSelectedGroupFromTrait(): ?string
    {
        return $this->selectedGroup;
    }

    public function setSelectedGroupFromTrait(?string $selectedGroup): self
    {
        $this->selectedGroup = $selectedGroup;

        return $this;
    }

    public function getCollapsedGroupsFromTrait(): array
    {
        return $this->collapsedGroups;
    }

    public function setCollapsedGroupsFromTrait(array $collapsedGroups): self
    {
        $this->collapsedGroups = $collapsedGroups;

        return $this;
    }

    public function model(string $modelClass): self
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }

    public function getModelInstance(string $itemId): ?Model
    {
        $modelClass = $this->getModelClass();

        if (! $modelClass || ! class_exists($modelClass)) {
            return null;
        }

        return call_user_func([$modelClass, 'find'], $itemId);
    }
}
