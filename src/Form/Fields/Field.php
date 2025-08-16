<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Illuminate\Support\Facades\Log;

abstract class Field
{
    public string $name;
    public ?string $label = null;
    public mixed $default = null;
    public bool $required = false;
    public ?string $placeholder = null;
    public ?string $helperText = null;
    public array $rules = [];
    public array $validationMessages = [];
    public bool $disabled = false;
    public ?string $columnSpan = null;
    public array $attributes = [];
    public ?string $prefixIcon = null;
    public ?string $suffixIcon = null;
    public string $type;

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

    public function placeholder(string $placeholder): static
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

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

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

            // Allow data-* and aria-* attributes
            if (str_starts_with($key, 'data-') || str_starts_with($key, 'aria-')) {
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

    abstract public function render(): string;

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
