<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Table\Grouping;

class Group
{
    public string $key;
    public string $label;
    public bool $collapsible;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->label = $key;
        $this->collapsible = false;
    }

    public static function make(string $key): self
    {
        return new self($key);
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function collapsible(bool $collapsible = true): self
    {
        $this->collapsible = $collapsible;

        return $this;
    }
}
