<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Illuminate\Support\ViewErrorBag;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class CheckboxField extends Field
{
    public mixed $checkedValue = 1;
    public mixed $uncheckedValue = 0;
    public bool $inline = false;

    public function checkedValue(mixed $value): static
    {
        $this->checkedValue = $value;

        return $this;
    }

    public function uncheckedValue(mixed $value): static
    {
        $this->uncheckedValue = $value;

        return $this;
    }

    public function inline(bool $inline = true): static
    {
        $this->inline = $inline;

        return $this;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function render(): string
    {
        return view('laravel-simple-datatables-and-forms::components.form.fields.checkbox', [
            'field' => $this,
            'errors' => session()->get('errors', new ViewErrorBag),
        ])->render();
    }
}
