<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table\Filters;

use Closure;
use Illuminate\View\View;
use Livewire\Component;

abstract class BaseFilter
{
    public string $name;
    public string $label;
    public bool $persistInSession = false;
    public ?Closure $query = null;

    public ?string $modelClass = null;

    protected ?Component $component = null;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->label = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string|array|callable $value): self
    {
        $this->label = $value;

        return $this;
    }

    public function persistInSession(bool $persist = true): static
    {
        $this->persistInSession = $persist;

        return $this;
    }

    public function setComponent(Component $component): void
    {
        $this->component = $component;
        $this->value = $component->filters[$this->name] ?? [];
    }

    abstract public function apply($query, $value): void;

    abstract public function render(): View;
}
