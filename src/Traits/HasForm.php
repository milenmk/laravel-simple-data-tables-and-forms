<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\CheckboxField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\ToggleField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\SecurityService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @see \Milenmk\LaravelSimpleDatatablesAndForms\Contracts\HasFormInterface
 */
trait HasForm
{
    public Stringable $componentName;
    public array $formData = [];
    public ?Model $formModel = null;

    public function mountForm(): void
    {
        $this->componentName = Str::of(class_basename($this))->snake();

        // Always initialize form data with default values from form fields first
        $this->initializeFormData();

        // Then merge with model data if exists, but only for form fields
        if ($this->formModel) {
            $this->fillFormFromModel();
        }
    }

    abstract public function form(Form $form): Form;

    public function getFormProperty(): View
    {
        // Create form instance
        $form = app(Form::class);

        // Set the model if available
        if ($this->formModel) {
            $form->fillFromModel($this->formModel);
        }

        // Fill with current form data
        if (! empty($this->formData)) {
            $form->fill($this->formData);
        }

        return $this->form($form)->render();
    }

    public function loadFormModel(?Model $model): void
    {
        $this->formModel = $model;

        $this->initializeFormData();

        if ($model) {
            $this->fillFormFromModel();
        }
    }

    public function fillForm(array $data): void
    {
        // Sanitize input data before filling form
        $securityService = app(SecurityService::class);
        $sanitizedData = $securityService->sanitizeInputs($data);

        // Only allow data for fields that exist in the form schema
        $form = new Form;
        $form = $this->form($form);
        $allowedFields = collect($form->getFields())
            ->pluck('name')
            ->toArray();

        $filteredData = array_intersect_key($sanitizedData, array_flip($allowedFields));

        $this->formData = array_merge($this->formData, $filteredData);
    }

    public function getFormData(): array
    {
        return $this->formData;
    }

    public function setFormData(array $data): void
    {
        // Sanitize and validate data before setting
        $securityService = app(SecurityService::class);
        $sanitizedData = $securityService->sanitizeInputs($data);

        // Only allow data for fields that exist in the form schema
        $form = new Form;
        $form = $this->form($form);
        $allowedFields = collect($form->getFields())
            ->pluck('name')
            ->toArray();

        $this->formData = array_intersect_key($sanitizedData, array_flip($allowedFields));
    }

    public function resetForm(): void
    {
        $form = new Form;
        $form = $this->form($form);

        // Reset to default values
        $this->formData = [];

        // Apply default values from fields
        foreach ($form->getFields() as $field) {
            $this->formData[$field->name] = $field->default ?? null;
        }

        $this->formModel = null;
    }

    public function getFormInstance(): Form
    {
        $form = new Form;

        // Set the model if available
        if ($this->formModel) {
            $form->fillFromModel($this->formModel);
        }

        // Fill with current form data
        if (! empty($this->formData)) {
            $form->fill($this->formData);
        }

        return $this->form($form);
    }

    public function refreshForm(): void
    {
        $this->initializeFormData();
    }

    /**
     * Get recommended listeners for form components
     * Components can merge this with their own listeners if needed
     */
    public function getFormListeners(): array
    {
        return [
            'refreshForm' => 'refreshForm',
        ];
    }

    public function updatedFormData(): void
    {
        // This method ensures Livewire tracks changes to formData
        // It will be called whenever any formData property changes

        // Sanitize the updated form data for security
        $securityService = app(SecurityService::class);
        $this->formData = $securityService->sanitizeInputs($this->formData);

        // Validate that only allowed fields are present
        $form = new Form;
        $form = $this->form($form);
        $allowedFields = collect($form->getFields())
            ->pluck('name')
            ->toArray();

        // Remove any fields that aren't defined in the form schema
        $this->formData = array_intersect_key($this->formData, array_flip($allowedFields));
    }

    public function debugFormData(): array
    {
        $form = new Form;
        $form = $this->form($form);

        $debug = [
            'formData' => $this->formData,
            'fieldNames' => [],
            'fieldDefaults' => [],
        ];

        foreach ($form->getFields() as $field) {
            $debug['fieldNames'][] = $field->name;
            $debug['fieldDefaults'][$field->name] = $field->default ?? null;
        }

        return $debug;
    }

    /**
     * Get form fields data filtered by model's fillable attributes
     * This method provides additional security by ensuring only fillable model attributes are returned
     */
    public function getFillableFormData(): array
    {
        if (! $this->formModel) {
            return $this->getFormFieldsData();
        }

        $formFieldsData = $this->getFormFieldsData();
        $fillable = $this->formModel->getFillable();

        // If no fillable attributes are defined, return all form fields
        // This maintains backward compatibility but logs a warning
        if (empty($fillable)) {
            Log::warning(
                'Model ' .
                    get_class($this->formModel) .
                    ' has no fillable attributes defined. This may be a security risk.',
            );

            return $formFieldsData;
        }

        // Get form field names to ensure we only return data for actual form fields
        $form = new Form;
        $form = $this->form($form);
        $formFieldNames = collect($form->getFields())
            ->pluck('name')
            ->toArray();

        // Triple filter: form fields + fillable attributes + actual form data
        $allowedFields = array_intersect($formFieldNames, $fillable);

        return array_intersect_key($formFieldsData, array_flip($allowedFields));
    }

    /**
     * Get only the form fields data, filtering out non-form fields
     */
    public function getFormFieldsData(): array
    {
        $form = new Form;
        $form = $this->form($form);

        $formFieldsData = [];

        foreach ($form->getFields() as $field) {
            if (array_key_exists($field->name, $this->formData)) {
                $formFieldsData[$field->name] = $this->formData[$field->name];
            } else {
                // Handle missing boolean fields (checkboxes/toggles that weren't checked)
                if ($field instanceof CheckboxField) {
                    $formFieldsData[$field->name] = $field->uncheckedValue;
                } elseif ($field instanceof ToggleField) {
                    $formFieldsData[$field->name] = $field->offValue;
                } else {
                    // For other field types, use their default value if set
                    $formFieldsData[$field->name] = $field->default;
                }
            }
        }

        return $formFieldsData;
    }

    protected function initializeFormData(): void
    {
        $form = new Form;
        $form = $this->form($form);

        foreach ($form->getFields() as $field) {
            if (! array_key_exists($field->name, $this->formData)) {
                $this->formData[$field->name] = $field->default ?? null;
            }
        }
    }

    protected function fillFormFromModel(): void
    {
        if (! $this->formModel) {
            return;
        }

        $form = new Form;
        $form = $this->form($form);
        $modelData = $this->formModel->toArray();

        // Only fill form fields that exist in the form schema
        foreach ($form->getFields() as $field) {
            if (array_key_exists($field->name, $modelData)) {
                $this->formData[$field->name] = $modelData[$field->name];
            }
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    protected function validateFormData(array $rules): array
    {
        // SECURITY: Apply rate limiting before validation
        $this->applyRateLimit();

        return $this->validate($rules);
    }

    /**
     * Apply rate limiting to prevent form abuse
     *
     * @throws InvalidArgumentException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function applyRateLimit(): void
    {
        if (! config('simple-datatables-and-forms.security.rate_limiting.enable', true)) {
            return;
        }

        $maxAttempts = config('simple-datatables-and-forms.security.rate_limiting.max_attempts', 60);
        $decayMinutes = config('simple-datatables-and-forms.security.rate_limiting.decay_minutes', 1);

        $key = 'form_submission:' . request()->ip() . ':' . static::class;

        if (app('cache')->has($key)) {
            $attempts = app('cache')->get($key, 0);

            if ($attempts >= $maxAttempts) {
                throw new ThrottleRequestsException('Too many form submissions. Please try again later.');
            }

            app('cache')->put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        } else {
            app('cache')->put($key, 1, now()->addMinutes($decayMinutes));
        }
    }

    protected function getValidationRulesFromForm(): array
    {
        $form = new Form;
        $form = $this->form($form);
        $rules = [];

        foreach ($form->getFields() as $field) {
            $fieldName = "formData.{$field->name}";

            // Always add at least a basic rule to ensure the field is included in validation
            $fieldRules = [];

            if (! empty($field->rules)) {
                $fieldRules = array_merge($fieldRules, $field->rules);
            }

            if ($field->required) {
                $fieldRules[] = 'required';
            } else {
                // Add nullable rule for non-required fields to ensure they're included
                $fieldRules[] = 'nullable';
            }

            // Add field type specific rules
            if (method_exists($field, 'getValidationRules')) {
                $typeRules = $field->getValidationRules();
                if (! empty($typeRules)) {
                    $fieldRules = array_merge($fieldRules, $typeRules);
                }
            }

            $rules[$fieldName] = $fieldRules;
        }

        return $rules;
    }
}
