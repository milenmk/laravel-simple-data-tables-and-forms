<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Illuminate\Support\ViewErrorBag;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class InputField extends Field
{
    public string $type = 'text';
    public ?int $minLength = null;
    public ?int $maxLength = null;
    public ?string $pattern = null;
    public ?int $min = null;
    public ?int $max = null;
    public ?int $step = null;

    public function email(): static
    {
        return $this->type('email');
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function password(): static
    {
        return $this->type('password');
    }

    public function number(): static
    {
        return $this->type('number');
    }

    public function tel(): static
    {
        return $this->type('tel');
    }

    public function url(): static
    {
        return $this->type('url');
    }

    public function date(): static
    {
        return $this->type('date');
    }

    public function time(): static
    {
        return $this->type('time');
    }

    public function datetime(): static
    {
        return $this->type('datetime-local');
    }

    public function file(): static
    {
        return $this->type('file');
    }

    public function search(): static
    {
        return $this->type('search');
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

    public function pattern(string $pattern): static
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function min(int $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function max(int $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function step(int $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getValidationRules(): array
    {
        $rules = parent::getValidationRules();

        // Add type-specific validation rules
        if ($this->type === 'email') {
            $rules[] = 'email';
        }

        if ($this->type === 'number') {
            $rules[] = 'numeric';
        }

        if ($this->type === 'url') {
            $rules[] = 'url';
        }

        if ($this->minLength !== null) {
            $rules[] = "min:{$this->minLength}";
        }

        if ($this->maxLength !== null) {
            $rules[] = "max:{$this->maxLength}";
        }

        if ($this->min !== null) {
            $rules[] = "min:{$this->min}";
        }

        if ($this->max !== null) {
            $rules[] = "max:{$this->max}";
        }

        return $rules;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function render(): string
    {
        return view('laravel-simple-datatables-and-forms::components.form.fields.input', [
            'field' => $this,
            'errors' => session()->get('errors', new ViewErrorBag),
        ])->render();
    }
}
