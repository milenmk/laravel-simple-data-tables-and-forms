<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\HtmlString;

class SecurityService
{
    /**
     * Sanitize input string to prevent XSS and SQL injection.
     */
    public function sanitizeInput(string $input): string
    {
        if (! Config::get('simple-datatables.security.sanitize_input', true)) {
            return $input;
        }

        // Remove HTML tags
        $sanitized = strip_tags($input);

        // Convert special characters to HTML entities
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');

        return $sanitized;
    }

    /**
     * Sanitize an array of inputs.
     *
     * @param  array<string, mixed>  $inputs
     * @return array<string, mixed>
     */
    public function sanitizeInputs(array $inputs): array
    {
        if (! Config::get('simple-datatables.security.sanitize_input', true)) {
            return $inputs;
        }

        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                $inputs[$key] = $this->sanitizeInput($value);
            } elseif (is_array($value)) {
                $inputs[$key] = $this->sanitizeInputs($value);
            }
        }

        return $inputs;
    }

    /**
     * Validate that the given sort field is allowed.
     */
    public function validateSortField(string $sortField, array $allowedFields): bool
    {
        return in_array($sortField, $allowedFields);
    }

    /**
     * Validate that the given sort direction is allowed.
     */
    public function validateSortDirection(string $sortDirection): bool
    {
        return in_array(strtoupper($sortDirection), ['ASC', 'DESC']);
    }

    /**
     * Generate CSRF token field for forms.
     */
    public function getCsrfField(): string|HtmlString
    {
        if (! Config::get('simple-datatables.security.csrf_protection', true)) {
            return '';
        }

        return csrf_field();
    }
}
