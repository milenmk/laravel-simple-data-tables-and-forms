<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Actions;

class Action
{
    public string $name;
    public ?string $label = null;
    public ?string $icon = null;
    public ?string $color = null;
    public ?string $size = null;
    public ?string $url = null;
    public mixed $action = null;
    public array $attributes = [];
    public bool $visible = true;
    public bool $disabled = false;

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

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function size(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function action(string|callable $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function attributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function visible(bool $visible = true): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ?: ucfirst(str_replace('_', ' ', $this->name));
    }
}
