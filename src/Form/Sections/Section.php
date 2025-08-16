<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections;

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
}
