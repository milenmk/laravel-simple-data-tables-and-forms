<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Table\Actions;

use Closure;

class BaseAction
{
    public string $key;

    public string|array|null $label;

    public mixed $url = null;
    public ?string $textColor = null;
    public ?string $color = null;
    public ?string $buttonBackground = null;
    public ?string $actionView = null;
    public string|bool|Closure|null $icon = null;
    public ?string $actionName = null;

    protected string $view = 'laravel-simple-datatables::components.actions.index';

    public function __construct($key)
    {
        $this->key = $key;
        $this->label = $key;
    }

    public static function make($key): static
    {
        return new static($key);
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function button(): self
    {
        $this->actionView = 'button';

        return $this;
    }

    public function iconButton(): self
    {
        $this->actionView = 'icon';

        return $this;
    }

    public function badge(): self
    {
        $this->actionView = 'badge';

        return $this;
    }

    public function hiddenIcon(): static
    {
        $this->icon = null;

        return $this;
    }

    public function icon(string|bool|Closure|null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function url(mixed $value): self
    {
        $this->url ??= $value; // Only set if null

        return $this;
    }

    public function getUrl($record = null): mixed
    {
        return match (true) {
            is_callable($this->url) && $record !== null => call_user_func($this->url, $record),
            is_callable($this->url) => call_user_func($this->url),
            default => $this->url ?? null,
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
     * @param  string  $buttonBackground  Color value e.g. gray-500, primary
     * @return $this
     */
    public function buttonBackground(string $buttonBackground): self
    {
        $this->buttonBackground = $buttonBackground;

        return $this;
    }

    public function action(string $actionName): self
    {
        $this->actionName = $actionName;

        return $this;
    }

    public function confirm()
    {
        //
    }
}
