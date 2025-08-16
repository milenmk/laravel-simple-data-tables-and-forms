<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Illuminate\Support\ViewErrorBag;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class DateTimeField extends Field
{
    public string $type = 'datetime-local';
    public ?string $min = null;
    public ?string $max = null;
    public ?int $step = null;
    public string $format = 'Y-m-d\TH:i';
    public bool $withSeconds = false;

    public function date(): static
    {
        $this->type = 'date';
        $this->format = 'Y-m-d';

        return $this;
    }

    public function time(): static
    {
        $this->type = 'time';
        $this->format = 'H:i';

        return $this;
    }

    public function datetime(): static
    {
        $this->type = 'datetime-local';
        $this->format = 'Y-m-d\TH:i';

        return $this;
    }

    public function withSeconds(bool $withSeconds = true): static
    {
        $this->withSeconds = $withSeconds;

        if ($withSeconds) {
            if ($this->type === 'time') {
                $this->format = 'H:i:s';
            } elseif ($this->type === 'datetime-local') {
                $this->format = 'Y-m-d\TH:i:s';
            }
        }

        return $this;
    }

    public function min(string $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function max(string $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function step(int $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getValidationRules(): array
    {
        $rules = [];

        if ($this->type === 'date') {
            $rules[] = 'date';
        } elseif ($this->type === 'time') {
            $rules[] = 'date_format:H:i' . ($this->withSeconds ? ':s' : '');
        } elseif ($this->type === 'datetime-local') {
            $rules[] = 'date';
        }

        if ($this->min !== null) {
            if ($this->type === 'date' || $this->type === 'datetime-local') {
                $rules[] = "after_or_equal:{$this->min}";
            }
        }

        if ($this->max !== null) {
            if ($this->type === 'date' || $this->type === 'datetime-local') {
                $rules[] = "before_or_equal:{$this->max}";
            }
        }

        return $rules;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    public function render(): string
    {
        return view('laravel-simple-datatables-and-forms::components.form.fields.datetime', [
            'field' => $this,
            'errors' => session()->get('errors', new ViewErrorBag),
        ])->render();
    }
}
