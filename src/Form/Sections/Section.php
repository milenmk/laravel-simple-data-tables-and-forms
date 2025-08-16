<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections;

use Closure;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns\Get;

class Section
{
    public string $name;
    public ?string $label = null;
    public ?string $description = null;
    public array $fields = [];
    public array $schema = [];
    public int|array $columns = 2;
    public bool $collapsible = false;
    public bool $collapsed = false;
    public bool $persistCollapsed = false;
    public ?string $icon = null;
    public array $headerActions = [];
    public bool $compact = false;
    public bool $aside = false;
    public int|string $columnSpan = 'auto';
    public bool $visible = true;
    public ?string $view = null;
    public mixed $content = null;
    public array $extraAttributes = [];
    public ?Closure $extraAttributesCallback = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function fields(array $fields): static
    {
        $this->fields = $fields;
        $this->schema = $fields; // Keep backward compatibility

        return $this;
    }

    public function columns(int $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function collapsible(bool $collapsible = true): static
    {
        $this->collapsible = $collapsible;

        return $this;
    }

    public function collapsed(bool $collapsed = true): static
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function schema(array $schema): static
    {
        $this->schema = $schema;
        $this->fields = $schema; // Keep backward compatibility

        return $this;
    }

    /**
     * Get all fields from this section
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Set form context on all fields in this section
     */
    public function setFormContext($record, array $formData): static
    {
        foreach ($this->fields as $field) {
            if (method_exists($field, 'setFormContext')) {
                $field->setFormContext($record, $formData);
            }
        }

        return $this;
    }

    public function headerActions(array $actions): static
    {
        $this->headerActions = $actions;

        return $this;
    }

    public function compact(bool $compact = true): static
    {
        $this->compact = $compact;

        return $this;
    }

    public function aside(bool $aside = true): static
    {
        $this->aside = $aside;

        return $this;
    }

    public function columnSpan(int|string $columnSpan): static
    {
        $this->columnSpan = $columnSpan;

        return $this;
    }

    public function persistCollapsed(bool $persistCollapsed = true): static
    {
        $this->persistCollapsed = $persistCollapsed;

        return $this;
    }

    public function visible(bool|callable $visible = true): static
    {
        $this->visible = is_callable($visible) ? $visible() : $visible;

        return $this;
    }

    public function view(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function content(mixed $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ??
            str($this->name)
                ->title()
                ->replace('_', ' ')
                ->toString();
    }

    /**
     * Add extra attributes to the section wrapper
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
            $get = new Get($this->formData);
            $callbackAttributes = call_user_func($this->extraAttributesCallback, $this->record, $get);

            if (is_array($callbackAttributes)) {
                $attributes = array_merge($attributes, $callbackAttributes);
            }
        }

        return $attributes;
    }
}
