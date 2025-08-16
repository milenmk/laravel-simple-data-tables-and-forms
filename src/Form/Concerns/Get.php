<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns;

class Get
{
    protected array $formData;

    public function __construct(array $formData = [])
    {
        $this->formData = $formData;
    }

    /**
     * Get the value of a field by name
     */
    public function __invoke(string $fieldName): mixed
    {
        return $this->formData[$fieldName] ?? null;
    }

    /**
     * Get the value of a field by name (alternative method)
     */
    public function get(string $fieldName): mixed
    {
        return $this->formData[$fieldName] ?? null;
    }

    /**
     * Set the form data context
     */
    public function setFormData(array $formData): void
    {
        $this->formData = $formData;
    }

    /**
     * Get all form data
     */
    public function all(): array
    {
        return $this->formData;
    }

    /**
     * Check if a field has a value
     */
    public function has(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->formData);
    }

    /**
     * Check if a field is empty
     */
    public function isEmpty(string $fieldName): bool
    {
        $value = $this->get($fieldName);

        return empty($value);
    }

    /**
     * Check if a field is not empty
     */
    public function isNotEmpty(string $fieldName): bool
    {
        return ! $this->isEmpty($fieldName);
    }
}
