<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Illuminate\Support\ViewErrorBag;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class ToggleField extends Field
{
    public mixed $onValue = 1;
    public mixed $offValue = 0;
    public ?string $onLabel = null;
    public ?string $offLabel = null;
    public string $size = 'md';
    public string $color = 'primary';

    public function onValue(mixed $value): static
    {
        $this->onValue = $value;

        return $this;
    }

    public function offValue(mixed $value): static
    {
        $this->offValue = $value;

        return $this;
    }

    public function onLabel(string $label): static
    {
        $this->onLabel = $label;

        return $this;
    }

    public function offLabel(string $label): static
    {
        $this->offLabel = $label;

        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function small(): static
    {
        return $this->size('sm');
    }

    public function size(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function large(): static
    {
        return $this->size('lg');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function render(): string
    {
        return view('laravel-simple-datatables-and-forms::components.form.fields.toggle', [
            'field' => $this,
            'errors' => session()->get('errors', new ViewErrorBag),
        ])->render();
    }
}
