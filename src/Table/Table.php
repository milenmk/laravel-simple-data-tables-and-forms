<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as FacadesView;
use Illuminate\View\View;
use Milenmk\LaravelSimpleDatatables\Table\Filters\BaseFilter;
use Milenmk\LaravelSimpleDatatables\Table\Grouping\Group;

class Table
{
    public string|array|null $heading = '';
    public bool $striped = false;

    public bool $showFilters = false;

    protected LengthAwarePaginator|Builder|null $query = null;
    protected array $columns = [];

    protected array $groups = [];

    protected ?string $modelClass = null;

    protected array $filters = [];

    protected array $tableFilters = [];

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
        // Get appearance settings from config
        $config = config('simple-datatables.appearance', []);
        $theme = $config['theme'] ?? 'light';
        $striped = $this->striped ?? $config['striped'] ?? true;
        $hover = $config['hover'] ?? true;
        $borders = $config['borders'] ?? 'all';
        $size = $config['size'] ?? 'md';

        // Get security service
        $securityService = app(\Milenmk\LaravelSimpleDatatables\Services\SecurityService::class);

        // Generate CSRF field if enabled
        $csrfField = $securityService->getCsrfField();

        return FacadesView::make('laravel-simple-datatables::components.table.table', [
            'data' => $this->data(),
            'columns' => $this->columns,
            'heading' => $this->heading,
            'striped' => $striped,
            'hover' => $hover,
            'borders' => $borders,
            'size' => $size,
            'theme' => $theme,
            'groups' => $this->groups,
            'selectedGroup' => $this->getSelectedGroupFromTrait(),
            'collapsedGroups' => $this->getCollapsedGroupsFromTrait(),
            'showFilters' => $this->showFilters,
            'tableFilters' => $this->tableFilters,
            'filters' => $this->getFiltersValues(),
            'csrfField' => $csrfField,
            'exportEnabled' => config('simple-datatables.export.enable', true),
            'exportFormats' => config('simple-datatables.export.formats', ['csv', 'excel', 'pdf']),
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

    /**
     * @throws Exception
     */
    public function filters(array $filters): self
    {
        foreach ($filters as $filter) {
            if (! $filter instanceof BaseFilter) {
                throw new Exception('All filters must be instances of the Filter class.');
            }

            $this->filters[] = $filter;
        }

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setShowFilters(bool $showFilters): self
    {
        $this->showFilters = $showFilters;

        return $this;
    }

    public function setTableFilters(array $tableFilters): self
    {
        $this->tableFilters = $tableFilters;

        return $this;
    }

    public function getFiltersValues(): array
    {
        return $this->filtersValues;
    }

    public function setFiltersValues(array $filtersValues): self
    {
        $this->filtersValues = $filtersValues;

        return $this;
    }
}
