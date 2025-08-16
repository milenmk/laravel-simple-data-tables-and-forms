<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters;

class FiltersGroup
{
    protected string $name;
    protected array $filters = [];
    protected ?string $label = null;
    protected bool $hideLabel = false;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->label = $name; // Default label is the name
    }

    public static function make(string $name = ''): static
    {
        return new static($name);
    }

    /**
     * Set the schema (filters) for this group
     */
    public function schema(array $filters): static
    {
        $this->filters = $filters;

        // Automatically set the group name on all filters
        foreach ($this->filters as $filter) {
            if (method_exists($filter, 'group')) {
                $filter->group($this->name);
            }
        }

        return $this;
    }

    /**
     * Set a custom label for the group
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Hide the group label
     */
    public function hideGroupLabel(): static
    {
        $this->hideLabel = true;

        return $this;
    }

    /**
     * Show the group label (default behavior)
     */
    public function showGroupLabel(): static
    {
        $this->hideLabel = false;

        return $this;
    }

    /**
     * Get the group name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the group label
     */
    public function getLabel(): ?string
    {
        return $this->hideLabel ? null : $this->label;
    }

    /**
     * Check if the group label should be hidden
     */
    public function isLabelHidden(): bool
    {
        return $this->hideLabel;
    }

    /**
     * Get all filters in this group
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Add a filter to this group
     */
    public function addFilter(BaseFilter $filter): static
    {
        $this->filters[] = $filter;

        // Set the group name on the filter
        if (method_exists($filter, 'group')) {
            $filter->group($this->name);
        }

        return $this;
    }
}
