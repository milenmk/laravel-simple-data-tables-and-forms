<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Concerns;

class Set
{
    protected array $formData;
    protected array $pendingUpdates = [];

    public function __construct(array &$formData = [])
    {
        $this->formData = &$formData;
    }

    /**
     * Set the value of a field by name
     */
    public function __invoke(string $fieldName, mixed $value): void
    {
        $this->set($fieldName, $value);
    }

    /**
     * Set the value of a field by name (alternative method)
     */
    public function set(string $fieldName, mixed $value): void
    {
        $this->formData[$fieldName] = $value;
        $this->pendingUpdates[$fieldName] = $value;
    }

    /**
     * Set multiple field values at once
     */
    public function fill(array $data): void
    {
        foreach ($data as $fieldName => $value) {
            $this->set($fieldName, $value);
        }
    }

    /**
     * Clear a field value
     */
    public function clear(string $fieldName): void
    {
        $this->set($fieldName, null);
    }

    /**
     * Remove a field entirely
     */
    public function forget(string $fieldName): void
    {
        unset($this->formData[$fieldName]);
        $this->pendingUpdates[$fieldName] = null;
    }

    /**
     * Get pending updates (for reactive functionality)
     */
    public function getPendingUpdates(): array
    {
        return $this->pendingUpdates;
    }

    /**
     * Clear pending updates
     */
    public function clearPendingUpdates(): void
    {
        $this->pendingUpdates = [];
    }

    /**
     * Set the form data reference
     */
    public function setFormDataReference(array &$formData): void
    {
        $this->formData = &$formData;
    }
}
