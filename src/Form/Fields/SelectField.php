<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ViewErrorBag;
use InvalidArgumentException;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns\Get;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns\Set;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class SelectField extends Field
{
    public array|Closure $options = [];
    public bool $multiple = false;
    public ?string $emptyOption = null;
    public string|Closure|null $placeholder = null;
    public bool $searchable = false;

    // Relationship properties
    public ?string $relationship = null;
    public ?string $titleAttribute = null;
    public ?Closure $getOptionLabelFromRecordUsing = null;
    public array $searchableColumns = [];
    public ?Closure $modifyQueryUsing = null;

    // Form context
    protected ?string $formModelClass = null;

    public function options(array|Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function placeholder(string|Closure|null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function emptyOption(string $emptyOption): static
    {
        $this->emptyOption = $emptyOption;

        return $this;
    }

    public function searchable(bool|array $searchable = true): static
    {
        if (is_array($searchable)) {
            $this->searchable = true;
            $this->searchableColumns = $searchable;
        } else {
            $this->searchable = $searchable;
        }

        return $this;
    }

    public function relationship(string $relationship, string $titleAttribute): static
    {
        $this->relationship = $relationship;
        $this->titleAttribute = $titleAttribute;

        return $this;
    }

    public function getOptionLabelFromRecordUsing(Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function modifyQueryUsing(Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    public function setFormModelClass(?string $modelClass): static
    {
        $this->formModelClass = $modelClass;

        return $this;
    }

    public function getOptions(): array
    {
        // Handle relationship-based options
        if ($this->relationship && $this->titleAttribute) {
            return $this->getRelationshipOptions();
        }

        // Handle closure-based options (for reactive functionality)
        if ($this->options instanceof Closure) {
            $get = new Get($this->formData);
            $set = new Set($this->formData);
            $result = call_user_func($this->options, $this->record, $get, $set);

            return is_array($result) ? $result : [];
        }

        if (is_callable($this->options)) {
            // SECURITY: Validate callable before execution
            if (count($this->options) === 2) {
                [$class, $method] = $this->options;

                // Only allow specific safe methods and classes
                $allowedMethods = ['all', 'pluck', 'get', 'toArray', 'cases'];
                $allowedClasses = [
                    'Illuminate\Database\Eloquent\Model',
                    'Illuminate\Support\Collection',
                    'App\Models\\', // Allow app models
                ];

                if (! is_string($method) || ! in_array($method, $allowedMethods)) {
                    throw new InvalidArgumentException("Method '{$method}' is not allowed for security reasons.");
                }

                if (is_object($class)) {
                    $className = get_class($class);
                } elseif (is_string($class)) {
                    $className = $class;
                } else {
                    throw new InvalidArgumentException('Invalid callable class type.');
                }

                $isAllowed = false;
                foreach ($allowedClasses as $allowedClass) {
                    if (str_starts_with($className, $allowedClass) || is_subclass_of($className, $allowedClass)) {
                        $isAllowed = true;
                        break;
                    }
                }

                if (! $isAllowed) {
                    throw new InvalidArgumentException("Class '{$className}' is not allowed for security reasons.");
                }
            } elseif (is_string($this->options) && function_exists($this->options)) {
                // Block all function calls for security
                throw new InvalidArgumentException('Function calls are not allowed for security reasons.');
            }

            try {
                $result = call_user_func($this->options);

                return is_array($result) ? $result : [];
            } catch (Throwable $e) {
                Log::error('SelectField options callback failed: ' . $e->getMessage());

                return [];
            }
        }

        // Handle enum options
        if (is_string($this->options) && enum_exists($this->options)) {
            return collect($this->options::cases())
                ->mapWithKeys(fn ($case) => [$case->value => $case->name])
                ->toArray();
        }

        return $this->options;
    }

    public function getValidationRules(): array
    {
        $rules = [];

        // Add basic validation rules from parent
        if ($this->required) {
            $rules[] = 'required';
        }

        // For multiple selects, add array validation
        if ($this->multiple) {
            $rules[] = 'array';
        }

        // Add relationship-specific validation rules
        if ($this->relationship && $this->titleAttribute) {
            $relationshipRules = $this->getRelationshipValidationRules();
            $rules = array_merge($rules, $relationshipRules);
        }

        // Add any custom rules that were set
        if (! empty($this->rules)) {
            $rules = array_merge($rules, $this->validateRules($this->rules));
        }

        return $rules;
    }

    public function getSearchableColumns(): array
    {
        return $this->searchableColumns;
    }

    public function hasCustomSearchColumns(): bool
    {
        return ! empty($this->searchableColumns);
    }

    public function hasCustomLabelCallback(): bool
    {
        return $this->getOptionLabelFromRecordUsing !== null;
    }

    public function hasQueryModification(): bool
    {
        return $this->modifyQueryUsing !== null;
    }

    public function isRelationshipBased(): bool
    {
        return $this->relationship !== null && $this->titleAttribute !== null;
    }

    /**
     * Get the resolved placeholder (including callbacks)
     */
    public function getPlaceholder(): ?string
    {
        if ($this->placeholder instanceof Closure) {
            $get = new Get($this->formData);

            return call_user_func($this->placeholder, $get);
        }

        return $this->placeholder;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    public function render(): string
    {
        return view('laravel-simple-datatables-and-forms::components.form.fields.select', [
            'field' => $this,
            'errors' => session()->get('errors', new ViewErrorBag),
        ])->render();
    }

    protected function getRelationshipOptions(): array
    {
        try {
            // Get the form's model class
            $modelClass = $this->getModelClass();
            if (! $modelClass) {
                return [];
            }

            // Create a model instance to access the relationship
            $modelInstance = new $modelClass;

            // Get the relationship query
            $relationshipQuery = $modelInstance->{$this->relationship}();

            // Apply custom query modifications
            if ($this->modifyQueryUsing) {
                $relationshipQuery = call_user_func($this->modifyQueryUsing, $relationshipQuery);
            }

            // Get the related records
            $records = $relationshipQuery->get();

            // Build options array
            $options = [];
            foreach ($records as $record) {
                $value = $record->getKey();

                // Use custom label callback if provided
                if ($this->getOptionLabelFromRecordUsing) {
                    $label = call_user_func($this->getOptionLabelFromRecordUsing, $record);
                } else {
                    $label = $record->{$this->titleAttribute};
                }

                $options[$value] = $label;
            }

            return $options;
        } catch (Throwable $e) {
            Log::error('SelectField relationship options failed: ' . $e->getMessage());

            return [];
        }
    }

    protected function getModelClass(): ?string
    {
        return $this->formModelClass;
    }

    protected function getRelationshipValidationRules(): array
    {
        $rules = [];

        try {
            $modelClass = $this->getModelClass();
            if (! $modelClass) {
                return $rules;
            }

            // Create a model instance to get the relationship
            $modelInstance = new $modelClass;
            $relationshipQuery = $modelInstance->{$this->relationship}();

            // Get the related model's table name
            $relatedModel = $relationshipQuery->getRelated();
            $tableName = $relatedModel->getTable();
            $keyName = $relatedModel->getKeyName();

            if ($this->multiple) {
                // For multiple selection (many-to-many relationships)
                $rules[] = 'array';
                $rules['*'] = "exists:{$tableName},{$keyName}";
            } else {
                // For single selection (belongs-to relationships)
                $rules[] = "exists:{$tableName},{$keyName}";
            }
        } catch (Throwable $e) {
            Log::error('SelectField relationship validation rules failed: ' . $e->getMessage());
        }

        return $rules;
    }
}
