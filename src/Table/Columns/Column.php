<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table\Columns;

class Column
{
    public string|array|null $label;
    public bool $sortable = false;
    public bool $searchable = false;
    public bool $visible = true;
    public mixed $description = null;
    public mixed $value = null;
    public ?string $weight = null;
    public ?string $wrap = null;
    public ?string $backgroundColor = null;
    public ?string $textColor = null;
    public ?string $color = null;
    public string $key;
    public string $align = 'left';
    public string $headerAlign = 'left';
    public string $wireModel;
    public array $searchFields = [];

    protected string $view;

    public function __construct($key)
    {
        $this->key = $key;
        $this->label = $key;
        $this->wireModel = $key;
    }

    public static function make($key): static
    {
        return new static($key);
    }

    public function sortable($value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    public function searchable($value = true): static
    {
        if (is_array($value)) {
            $this->searchable = true;
            $this->searchFields = $value;
        } else {
            $this->searchable = $value;
        }

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    /**
     * Set the label for the column. This can be a string, array, or callable.
     */
    public function label(string|array|callable $value): self
    {
        $this->label = $value;

        return $this;
    }

    public function hiddenLabel(): static
    {
        $this->label = null;

        return $this;
    }

    public function value(mixed $value): self
    {
        $this->value ??= $value; // Only set if null

        return $this;
    }

    public function getValue($item): mixed
    {
        return match (true) {
            is_callable($this->value) => call_user_func($this->value, $item),
            isset($this->value) => $this->value,
            default => $this->getNestedValue($item, $this->key),
        };
    }

    /**
     * @param  string  $color  Full value e.g. text-gray-500, text-primary
     * @return $this
     */
    public function color(string $color): self
    {
        $this->textColor = $color;
        $this->color = str_replace('text-', '', $color);

        return $this;
    }

    /**
     * @param  string  $color  Full value e.g. bg-gray-500, bg-danger
     * @return $this
     */
    public function background(string $color): self
    {
        $this->backgroundColor = $color;

        return $this;
    }

    public function visible(bool $value = true): self
    {
        $this->visible = $value;

        return $this;
    }

    public function getVisibility(): bool
    {
        return $this->visible;
    }

    /**
     * Set the weight of the text (e.g., bold, thin, medium).
     */
    public function weight(string $value): self
    {
        $this->weight = $value;

        return $this;
    }

    /**
     * Allow the text to wrap or not.
     */
    public function wrap($value = true): self
    {
        if ($value === true) {
            $this->wrap = 'text-wrap';
        } else {
            $this->wrap = 'text-nowrap';
        }

        return $this;
    }

    /**
     * Set a description for the column.
     */
    public function description(mixed $value): self
    {
        $this->description ??= $value; // Only set if null

        return $this;
    }

    public function getDescription($item): mixed
    {
        return match (true) {
            is_callable($this->description) => call_user_func($this->description, $item),
            isset($this->description) => $this->description,
            default => $item->{$this->key} ?? null,
        };
    }

    public function align($value = 'left'): self
    {
        $this->align = $value;

        return $this;
    }

    public function headerAlign($value = 'left'): self
    {
        $this->headerAlign = $value;

        return $this;
    }

    /**
     * Specify a custom wire model for field.
     */
    public function model(mixed $value): static
    {
        $this->wireModel = $value;

        return $this;
    }

    protected function getNestedValue($item, string $key)
    {
        $keys = explode('.', $key);
        foreach ($keys as $keyPart) {
            if (is_object($item) && isset($item->{$keyPart})) {
                $item = $item->{$keyPart};
            } else {
                return null;
            }
        }

        return $item;
    }
}
