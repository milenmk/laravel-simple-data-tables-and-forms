<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Illuminate\Support\ViewErrorBag;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class Repeater extends Field
{
    public array $schema = [];
    public int $minItems = 0;
    public int $maxItems = 10;
    public ?string $relationship = null;
    public bool $collapsible = false;
    public bool $cloneable = true;
    public bool $deletable = true;
    public ?string $addLabel = null;
    public ?string $deleteLabel = null;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->addLabel = 'Add Item';
        $this->deleteLabel = 'Delete';
    }

    public function schema(array $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    public function minItems(int $minItems): static
    {
        $this->minItems = $minItems;

        return $this;
    }

    public function maxItems(int $maxItems): static
    {
        $this->maxItems = $maxItems;

        return $this;
    }

    public function relationship(string $relationship): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function collapsible(bool $collapsible = true): static
    {
        $this->collapsible = $collapsible;

        return $this;
    }

    public function cloneable(bool $cloneable = true): static
    {
        $this->cloneable = $cloneable;

        return $this;
    }

    public function deletable(bool $deletable = true): static
    {
        $this->deletable = $deletable;

        return $this;
    }

    public function addLabel(string $addLabel): static
    {
        $this->addLabel = $addLabel;

        return $this;
    }

    public function deleteLabel(string $deleteLabel): static
    {
        $this->deleteLabel = $deleteLabel;

        return $this;
    }

    public function getValidationRules(): array
    {
        $rules = parent::getValidationRules();

        $rules[] = 'array';

        if ($this->minItems > 0) {
            $rules[] = "min:{$this->minItems}";
        }

        if ($this->maxItems > 0) {
            $rules[] = "max:{$this->maxItems}";
        }

        // Add validation rules for schema fields
        foreach ($this->schema as $field) {
            if (method_exists($field, 'getValidationRules')) {
                $fieldRules = $field->getValidationRules();
                if (! empty($fieldRules)) {
                    $rules["{$this->name}.*.{$field->name}"] = $fieldRules;
                }
            }
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
        return view('laravel-simple-datatables-and-forms::components.form.fields.repeater', [
            'field' => $this,
            'errors' => session()->get('errors', new ViewErrorBag),
        ])->render();
    }
}
