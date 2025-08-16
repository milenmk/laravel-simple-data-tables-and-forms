<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns;

use Closure;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\IconColumn\IconColumnSize;

class IconColumn extends Column
{
    protected string $view = 'laravel-simple-datatables-and-forms::components.table.columns.icon-column';

    protected bool|Closure|null $isBoolean = null;

    protected string|bool|Closure|null $icon = null;

    protected IconColumnSize|string|Closure|null $size = null;
    protected string|array|Closure|null $falseColor = null;
    protected string|Closure|null $falseIcon = null;
    protected string|array|Closure|null $trueColor = null;
    protected string|Closure|null $trueIcon = null;

    public function icon(string|bool|Closure|null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(mixed $state): ?string
    {
        if (! $this->isBoolean) {
            return null;
        }

        if ($state === null) {
            return null;
        }

        return $state ? $this->getTrueIcon() : $this->getFalseIcon();
    }

    public function getTrueIcon(): string
    {
        return $this->trueIcon ?? 'heroicon-o-check-circle';
    }

    public function getFalseIcon(): string
    {
        return $this->falseIcon ?? 'heroicon-o-x-circle';
    }

    public function false(string|Closure|null $icon = null, string|array|Closure|null $color = null): static
    {
        $this->falseIcon($icon);
        $this->falseColor($color);

        return $this;
    }

    public function falseIcon(string|Closure|null $icon): static
    {
        $this->boolean();
        $this->falseIcon = $icon;

        return $this;
    }

    public function boolean(bool|Closure $condition = true): static
    {
        $this->isBoolean = $condition;

        return $this;
    }

    public function falseColor(string|array|Closure|null $color): static
    {
        $this->boolean();
        $this->falseColor = $color;

        return $this;
    }

    public function true(string|Closure|null $icon = null, string|array|Closure|null $color = null): static
    {
        $this->trueIcon($icon);
        $this->trueColor($color);

        return $this;
    }

    public function trueIcon(string|Closure|null $icon): static
    {
        $this->boolean();
        $this->trueIcon = $icon;

        return $this;
    }

    public function trueColor(string|array|Closure|null $color): static
    {
        $this->boolean();
        $this->trueColor = $color;

        return $this;
    }

    public function size(IconColumnSize|string|Closure|null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getColor(mixed $state): string|array|null
    {
        if (! $this->isBoolean) {
            return null;
        }

        if ($state === null) {
            return null;
        }

        return $state ? $this->getTrueColor() : $this->getFalseColor();
    }

    public function getTrueColor(): string|array
    {
        return $this->trueColor ?? 'success';
    }

    public function getFalseColor(): string|array
    {
        return $this->falseColor ?? 'danger';
    }
}
