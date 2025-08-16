<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Table;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;
use Milenmk\LaravelSimpleDatatablesAndForms\Exceptions\InvalidFilterTypeException;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\SecurityService;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\BaseFilter;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\FiltersGroup;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Grouping\Group;

class Table
{
    public string|array|null $heading = '';
    public ?bool $striped = null;

    public ?bool $showFilters = null;

    public array $extraAttributes = [];
    public ?Closure $extraAttributesCallback = null;

    protected LengthAwarePaginator|Builder|null $query = null;
    protected array $columns = [];

    protected array $groups = [];

    protected ?string $modelClass = null;

    protected array $filters = [];

    protected array $filtersGroups = [];

    protected array $tableFilters = [];

    protected ?string $selectedGroup = null;
    protected array $collapsedGroups = [];
    protected array $filtersValues = [];

    protected ?int $filterColumns = null;
    protected ?bool $filterResponsive = null;
    protected ?array $filterResponsiveColumns = null;

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

    public function render(): ViewFacade|View
    {
        // Get appearance settings from config
        $config = config('simple-datatables-and-forms.appearance', []);
        $theme = $config['theme'] ?? 'light';
        $striped = $this->striped ?? ($config['striped'] ?? true);
        $hover = $config['hover'] ?? true;
        $borders = $config['borders'] ?? 'all';
        $size = $config['size'] ?? 'md';

        // Get security service
        $securityService = app(SecurityService::class);

        // Generate CSRF field if enabled
        $csrfField = $securityService->getCsrfField();

        return ViewFacade::make('laravel-simple-datatables-and-forms::components.table.table', [
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
            'exportEnabled' => config('simple-datatables-and-forms.export.enable', true),
            'exportFormats' => config('simple-datatables-and-forms.export.formats', ['csv', 'excel', 'pdf']),
            'filterColumns' => $this->getFilterColumns(),
            'filterResponsive' => $this->getFilterResponsive(),
            'filterResponsiveColumns' => $this->getFilterResponsiveColumns(),
            'table' => $this,
        ]);
    }

    public function data(): Builder|LengthAwarePaginator
    {
        return $this->query;
    }

    public function getSelectedGroupFromTrait(): ?string
    {
        return $this->selectedGroup;
    }

    public function getCollapsedGroupsFromTrait(): array
    {
        return $this->collapsedGroups;
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

    public function getFilterColumns(): int
    {
        return $this->filterColumns ?? config('simple-datatables-and-forms.filters.columns', 6);
    }

    public function getFilterResponsive(): bool
    {
        return $this->filterResponsive ?? config('simple-datatables-and-forms.filters.responsive', true);
    }

    public function getFilterResponsiveColumns(): array
    {
        return $this->filterResponsiveColumns ??
            config('simple-datatables-and-forms.filters.responsive_columns', [
                'sm' => 1,
                'md' => 2,
                'lg' => 4,
                'xl' => 6,
            ]);
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
        $this->groups = collect($groups)
            ->map(function ($group) {
                if ($group instanceof Group) {
                    return $group;
                }

                return is_array($group) ? Group::make(...$group) : Group::make($group);
            })
            ->toArray();

        return $this;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function setSelectedGroupFromTrait(?string $selectedGroup): self
    {
        $this->selectedGroup = $selectedGroup;

        return $this;
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

    public function getModelInstance(string $itemId): ?Model
    {
        $modelClass = $this->getModelClass();

        if (! $modelClass || ! class_exists($modelClass)) {
            return null;
        }

        return call_user_func([$modelClass, 'find'], $itemId);
    }

    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }

    /**
     * @throws InvalidFilterTypeException
     */
    public function filters(array $filters): self
    {
        foreach ($filters as $filter) {
            if ($filter instanceof FiltersGroup) {
                // Store the FiltersGroup for later reference
                $this->filtersGroups[$filter->getName()] = $filter;

                // Handle FiltersGroup - add all its filters to the main filters array
                foreach ($filter->getFilters() as $groupFilter) {
                    if (! $groupFilter instanceof BaseFilter) {
                        throw InvalidFilterTypeException::notInstanceOfBaseFilter($groupFilter);
                    }
                    $this->filters[] = $groupFilter;
                }
            } elseif ($filter instanceof BaseFilter) {
                // Handle individual filter
                $this->filters[] = $filter;
            } else {
                throw InvalidFilterTypeException::notInstanceOfBaseFilter($filter);
            }
        }

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getFiltersGroups(): array
    {
        return $this->filtersGroups;
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

    public function filterColumns(int $columns): self
    {
        $this->filterColumns = $columns;

        return $this;
    }

    public function filterResponsive(bool $responsive = true): self
    {
        $this->filterResponsive = $responsive;

        return $this;
    }

    public function filterResponsiveColumns(array $responsiveColumns): self
    {
        $this->filterResponsiveColumns = $responsiveColumns;

        return $this;
    }

    /**
     * Add extra attributes to the form wrapper
     */
    public function extraAttributes(array|Closure $attributes): static
    {
        if ($attributes instanceof Closure) {
            $this->extraAttributesCallback = $attributes;
        } else {
            $this->extraAttributes = array_merge($this->extraAttributes, $attributes);
        }

        return $this;
    }

    /**
     * Merge extra attributes with existing class attribute
     */
    public function getMergedAttributes(array $baseAttributes = []): array
    {
        $extraAttributes = $this->getExtraAttributes();
        $merged = array_merge($baseAttributes, $extraAttributes);

        // Special handling for class attribute - merge instead of replace
        if (isset($baseAttributes['class']) && isset($extraAttributes['class'])) {
            $merged['class'] = trim($baseAttributes['class'] . ' ' . $extraAttributes['class']);
        }

        return $merged;
    }

    /**
     * Get the resolved extra attributes (including callbacks)
     */
    public function getExtraAttributes(): array
    {
        $attributes = $this->extraAttributes;

        if ($this->extraAttributesCallback) {
            $callbackAttributes = call_user_func($this->extraAttributesCallback);

            if (is_array($callbackAttributes)) {
                $attributes = array_merge($attributes, $callbackAttributes);
            }
        }

        return $attributes;
    }
}
