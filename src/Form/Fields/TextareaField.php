<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Illuminate\Support\ViewErrorBag;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class TextareaField extends Field
{
    public int $rows = 3;
    public int $cols = 50;
    public ?int $minLength = null;
    public ?int $maxLength = null;
    public bool $autosize = false;

    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function cols(int $cols): static
    {
        $this->cols = $cols;

        return $this;
    }

    public function minLength(int $minLength): static
    {
        $this->minLength = $minLength;

        return $this;
    }

    public function maxLength(int $maxLength): static
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    public function autosize(bool $autosize = true): static
    {
        $this->autosize = $autosize;

        return $this;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function render(): string
    {
        return view('laravel-simple-datatables-and-forms::components.form.fields.textarea', [
            'field' => $this,
            'errors' => session()->get('errors', new ViewErrorBag),
        ])->render();
    }
}
