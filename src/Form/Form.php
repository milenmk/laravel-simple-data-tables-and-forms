<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns\Get;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\SecurityService;

class Form
{
    public string|array|null $heading = '';
    public int $columns = 2;
    public string $theme = 'light';

    public array $extraAttributes = [];
    public ?Closure $extraAttributesCallback = null;

    protected array $fields = [];
    protected array $sections = [];
    protected ?string $modelClass = null;
    protected ?Model $model = null;
    protected array $formData = [];

    public function schema(array $fields): self
    {
        $this->fields = $fields;
        $this->setModelClassOnFields();

        return $this;
    }

    /**
     * Get all fields.
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function sections(array $sections): self
    {
        $this->sections = collect($sections)
            ->map(function ($section) {
                if ($section instanceof Section) {
                    return $section;
                }

                return is_array($section) ? Section::make(...$section) : Section::make($section);
            })
            ->toArray();

        $this->setModelClassOnFields();

        return $this;
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    public function columns(int $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function heading(string|array $value): self
    {
        $this->heading = $value;

        return $this;
    }

    public function model(string $modelClass): self
    {
        $this->modelClass = $modelClass;
        $this->setModelClassOnFields();

        return $this;
    }

    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function theme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function fillFromModel(?Model $model): self
    {
        if ($model) {
            $this->setModel($model);
            $this->fill($model->toArray());
        }

        return $this;
    }

    public function fill(array $data): self
    {
        $this->formData = array_merge($this->formData, $data);

        return $this;
    }

    public function getFormData(): array
    {
        return $this->formData;
    }

    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->getFields() as $field) {
            if (! empty($field->rules)) {
                $rules["formData.{$field->name}"] = $field->rules;
            }

            if ($field->required) {
                $rules["formData.{$field->name}"][] = 'required';
            }

            // Add field type specific rules
            if (method_exists($field, 'getValidationRules')) {
                $fieldRules = $field->getValidationRules();
                if (! empty($fieldRules)) {
                    $rules["formData.{$field->name}"] = array_merge(
                        $rules["formData.{$field->name}"] ?? [],
                        $fieldRules,
                    );
                }
            }
        }

        return $rules;
    }

    public function render(): View
    {
        // Set form context on all fields for reactive functionality
        $this->setFormContextOnFields();

        // Get appearance settings from config
        $config = config('simple-datatables-and-forms.form', []);
        $theme = $config['theme'] ?? 'light';

        // Generate CSRF field for security
        $securityService = app(SecurityService::class);
        $csrfField = $securityService->getCsrfField();

        return view('laravel-simple-datatables-and-forms::components.form.form', [
            'fields' => $this->fields,
            'sections' => $this->sections,
            'heading' => $this->heading,
            'columns' => $this->columns,
            'theme' => $theme,
            'model' => $this->model,
            'formData' => $this->formData,
            'csrfField' => $csrfField,
            'form' => $this, // Pass the form instance
        ]);
    }

    /**
     * Add extra attributes to the form wrapper
     */
    public function extraAttributes(array|Closure $attributes): static
    {
        if ($attributes instanceof Closure) {
            $this->extraAttributesCallback = $attributes;
        } else {
            $this->extraAttributes = array_merge($this->extraAttributes, $attributes);
        }

        return $this;
    }

    /**
     * Merge extra attributes with existing class attribute
     */
    public function getMergedAttributes(array $baseAttributes = []): array
    {
        $extraAttributes = $this->getExtraAttributes();
        $merged = array_merge($baseAttributes, $extraAttributes);

        // Special handling for class attribute - merge instead of replace
        if (isset($baseAttributes['class']) && isset($extraAttributes['class'])) {
            $merged['class'] = trim($baseAttributes['class'] . ' ' . $extraAttributes['class']);
        }

        return $merged;
    }

    /**
     * Get the resolved extra attributes (including callbacks)
     */
    public function getExtraAttributes(): array
    {
        $attributes = $this->extraAttributes;

        if ($this->extraAttributesCallback) {
            $get = new Get($this->formData);
            $callbackAttributes = call_user_func($this->extraAttributesCallback, $this->model, $get);

            if (is_array($callbackAttributes)) {
                $attributes = array_merge($attributes, $callbackAttributes);
            }
        }

        return $attributes;
    }

    protected function setModelClassOnFields(): void
    {
        if (! $this->modelClass) {
            return;
        }

        // Set model class on direct fields
        foreach ($this->fields as $field) {
            if ($field instanceof SelectField) {
                $field->setFormModelClass($this->modelClass);
            }
        }

        // Set model class on fields within sections
        foreach ($this->sections as $section) {
            foreach ($section->getFields() as $field) {
                if ($field instanceof SelectField) {
                    $field->setFormModelClass($this->modelClass);
                }
            }
        }
    }

    protected function setFormContextOnFields(): void
    {
        // Set form context on direct fields
        foreach ($this->fields as $field) {
            $field->setFormContext($this->model, $this->formData);
        }

        // Set form context on fields within sections
        foreach ($this->sections as $section) {
            foreach ($section->getFields() as $field) {
                $field->setFormContext($this->model, $this->formData);
            }
        }
    }
}
