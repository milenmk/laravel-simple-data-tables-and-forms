<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns\Get;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns\Set;
use ReflectionException;
use ReflectionFunction;

abstract class Field
{
    public string $name;
    public ?string $label = null;
    public mixed $default = null;
    public bool $required = false;
    public string|Closure|null $placeholder = null;
    public ?string $helperText = null;
    public array $rules = [];
    public array $validationMessages = [];
    public bool|Closure $disabled = false;
    public ?string $columnSpan = null;
    public array $attributes = [];
    public array $extraAttributes = [];
    public ?string $prefixIcon = null;
    public ?string $suffixIcon = null;
    public string $type;
    public bool $reactive = false;

    // Reactive functionality
    public ?Closure $afterStateUpdated = null;
    public ?Closure $hidden = null;
    public ?Closure $extraAttributesCallback = null;

    // Form context for reactive functionality
    protected ?Model $record = null;
    protected array $formData = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function placeholder(string|Closure|null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function helperText(string $helperText): static
    {
        $this->helperText = $helperText;

        return $this;
    }

    public function rules(array $rules): static
    {
        // SECURITY: Validate and sanitize rules to prevent injection
        $this->rules = $this->validateRules($rules);

        return $this;
    }

    public function validationMessages(array $messages): static
    {
        $this->validationMessages = $messages;

        return $this;
    }

    public function columnSpan(string $columnSpan): static
    {
        $this->columnSpan = $columnSpan;

        return $this;
    }

    public function prefixIcon(string $icon): static
    {
        $this->prefixIcon = $icon;

        return $this;
    }

    public function suffixIcon(string $icon): static
    {
        $this->suffixIcon = $icon;

        return $this;
    }

    /**
     * Add extra attributes to the field wrapper
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
     * Set callback to execute after field state is updated
     */
    public function afterStateUpdated(Closure $callback): static
    {
        $this->afterStateUpdated = $callback;

        return $this;
    }

    /**
     * Set callback to determine if field should be hidden
     */
    public function hidden(bool|Closure $hidden = true): static
    {
        if ($hidden instanceof Closure) {
            $this->hidden = $hidden;
        } else {
            $this->hidden = fn () => $hidden;
        }

        return $this;
    }

    /**
     * Override the disabled method to support closures
     */
    public function disabled(bool|Closure $disabled = true): static
    {
        if ($disabled instanceof Closure) {
            $this->disabled = $disabled;
        } else {
            $this->disabled = fn () => $disabled;
        }

        return $this;
    }

    /**
     * Set the form context for reactive functionality
     */
    public function setFormContext(?Model $record, array $formData): static
    {
        $this->record = $record;
        $this->formData = $formData;

        return $this;
    }

    public function attributes(array $attributes): static
    {
        // Sanitize and validate attributes for security
        $sanitizedAttributes = [];
        $allowedAttributes = [
            'class',
            'id',
            'style',
            'data-*',
            'aria-*',
            'x-*', // Alpine.js attributes
            'role',
            'title',
            'alt',
            'placeholder',
            'readonly',
            'disabled',
            'required',
            'multiple',
            'min',
            'max',
            'step',
            'minlength',
            'maxlength',
            'pattern',
            'accept',
            'autocomplete',
            'autofocus',
            'form',
            'formaction',
            'formenctype',
            'formmethod',
            'formnovalidate',
            'formtarget',
            'height',
            'width',
            'size',
            'cols',
            'rows',
            'wrap',
        ];

        foreach ($attributes as $key => $value) {
            $key = strtolower($key);

            // Allow data-*, aria-*, and x-* attributes
            if (str_starts_with($key, 'data-') || str_starts_with($key, 'aria-') || str_starts_with($key, 'x-')) {
                $sanitizedAttributes[$key] = htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

                continue;
            }

            // Check if attribute is in allowed list
            if (in_array($key, $allowedAttributes)) {
                // Sanitize the value to prevent XSS
                $sanitizedAttributes[$key] = htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
            }
        }

        $this->attributes = array_merge($this->attributes, $sanitizedAttributes);

        return $this;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function getLabel(): string
    {
        return $this->label ??
            str($this->name)
                ->title()
                ->replace('_', ' ')
                ->toString();
    }

    /**
     * Check if the field should be hidden
     *
     * @throws ReflectionException
     */
    public function isHidden(): bool
    {
        if ($this->hidden instanceof Closure) {
            $get = new Get($this->formData);
            $reflection = new ReflectionFunction($this->hidden);
            $params = $reflection->getNumberOfParameters();

            return match ($params) {
                1 => call_user_func($this->hidden, $get),
                2 => call_user_func($this->hidden, $this->record, $get),
                default => call_user_func($this->hidden, $this->record, $get),
            };
        }

        return false;
    }

    /**
     * Check if the field should be disabled
     *
     * @throws ReflectionException
     */
    public function isDisabled(): bool
    {
        if ($this->disabled instanceof Closure) {
            $get = new Get($this->formData);
            $reflection = new ReflectionFunction($this->disabled);
            $params = $reflection->getNumberOfParameters();

            return match ($params) {
                1 => call_user_func($this->disabled, $get),
                2 => call_user_func($this->disabled, $this->record, $get),
                default => call_user_func($this->disabled, $this->record, $get),
            };
        }

        return (bool) $this->disabled;
    }

    /**
     * Execute the afterStateUpdated callback
     *
     * @throws ReflectionException
     */
    public function executeAfterStateUpdated(mixed $state): void
    {
        if ($this->afterStateUpdated instanceof Closure) {
            $get = new Get($this->formData);
            $set = new Set($this->formData);
            $reflection = new ReflectionFunction($this->afterStateUpdated);
            $params = $reflection->getNumberOfParameters();

            match ($params) {
                1 => call_user_func($this->afterStateUpdated, $state),
                2 => call_user_func($this->afterStateUpdated, $get, $state),
                3 => call_user_func($this->afterStateUpdated, $get, $set, $state),
                4 => call_user_func($this->afterStateUpdated, $this->record, $get, $set, $state),
                default => call_user_func($this->afterStateUpdated, $this->record, $get, $set, $state),
            };
        }
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
            $callbackAttributes = call_user_func($this->extraAttributesCallback, $this->record, $get);

            if (is_array($callbackAttributes)) {
                $attributes = array_merge($attributes, $callbackAttributes);
            }
        }

        return $attributes;
    }

    /**
     * Get the current value of the field from form data
     */
    public function getCurrentValue(): mixed
    {
        $value = $this->formData[$this->name] ?? $this->default;

        // For select fields, ensure proper handling of empty values
        if ($this instanceof SelectField) {
            if ($this->multiple) {
                // For multiple selects, ensure we return an array
                if ($value === null || $value === '') {
                    return [];
                }

                return is_array($value) ? $value : [$value];
            } else {
                // For single selects, return null for empty values
                return $value === '' || $value === null ? null : $value;
            }
        }

        return $value;
    }

    abstract public function render(): string;

    public function reactive(bool $condition = true): static
    {
        $this->reactive = $condition;

        return $this;
    }

    /**
     * Validate and sanitize validation rules for security
     */
    protected function validateRules(array $rules): array
    {
        $sanitizedRules = [];
        $allowedRules = [
            'required',
            'nullable',
            'string',
            'integer',
            'numeric',
            'boolean',
            'array',
            'email',
            'url',
            'date',
            'date_format',
            'min',
            'max',
            'between',
            'size',
            'alpha',
            'alpha_dash',
            'alpha_num',
            'regex',
            'unique',
            'exists',
            'confirmed',
            'in',
            'not_in',
            'mimes',
            'image',
            'dimensions',
            'file',
            'json',
            'ip',
            'ipv4',
            'ipv6',
            'mac_address',
            'uuid',
            'timezone',
        ];

        foreach ($rules as $rule) {
            if (is_string($rule)) {
                // Parse rule with parameters (e.g., "min:5", "unique:users,email")
                $ruleParts = explode(':', $rule, 2);
                $ruleName = $ruleParts[0];

                if (in_array($ruleName, $allowedRules)) {
                    // For rules with parameters, validate the parameters
                    if (isset($ruleParts[1])) {
                        $sanitizedRules[] = $this->sanitizeRuleParameters($ruleName, $ruleParts[1]);
                    } else {
                        $sanitizedRules[] = $rule;
                    }
                } else {
                    Log::warning("Validation rule '{$ruleName}' is not allowed and was removed for security.");
                }
            } elseif (is_object($rule) && method_exists($rule, '__toString')) {
                // Handle rule objects (like Rule::unique())
                $sanitizedRules[] = $rule;
            }
        }

        return $sanitizedRules;
    }

    /**
     * Sanitize rule parameters to prevent injection
     */
    protected function sanitizeRuleParameters(string $ruleName, string $parameters): string
    {
        // Sanitize parameters based on rule type
        switch ($ruleName) {
            case 'unique':
            case 'exists':
                // For database rules, only allow alphanumeric, underscores, and commas
                $sanitizedParams = preg_replace('/[^a-zA-Z0-9_,]/', '', $parameters);

                return "{$ruleName}:{$sanitizedParams}";

            case 'regex':
                // For regex rules, validate the pattern is safe
                if (@preg_match($parameters, '') === false) {
                    Log::warning("Invalid regex pattern removed: {$parameters}");

                    return 'string'; // Fallback to string validation
                }

                return "{$ruleName}:{$parameters}";

            case 'in':
            case 'not_in':
                // For in/not_in rules, sanitize the values
                $values = explode(',', $parameters);
                $sanitizedValues = array_map(function ($value) {
                    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
                }, $values);

                return "{$ruleName}:" . implode(',', $sanitizedValues);

            default:
                // For numeric parameters, ensure they're actually numeric
                if (is_numeric($parameters)) {
                    return "{$ruleName}:{$parameters}";
                }
                // For other parameters, sanitize them
                $sanitizedParams = htmlspecialchars($parameters, ENT_QUOTES, 'UTF-8');

                return "{$ruleName}:{$sanitizedParams}";
        }
    }
}
