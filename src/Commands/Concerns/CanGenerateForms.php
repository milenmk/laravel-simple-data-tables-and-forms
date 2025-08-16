<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Commands\Concerns;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait CanGenerateForms
{
    protected function getResourceFormFields(string $model): string
    {
        if (! class_exists($model)) {
            return '';
        }

        $modelInstance = app($model);
        $table = $modelInstance->getTable();

        if (! Schema::hasTable($table)) {
            return '';
        }

        $columns = Schema::getColumnListing($table);
        $fields = [];

        foreach ($columns as $column) {
            // Skip system columns
            if (
                in_array($column, [
                    'id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'email_verified_at',
                    'remember_token',
                ])
            ) {
                continue;
            }

            $columnType = Schema::getColumnType($table, $column);
            $fieldType = $this->mapColumnTypeToFormField($columnType, $column);

            $field = $this->generateFormFieldCode($column, $fieldType);
            $fields[] = $field;
        }

        return implode(",\n", $fields);
    }

    protected function mapColumnTypeToFormField(string $columnType, string $columnName): string
    {
        // Handle boolean fields
        if (
            in_array($columnType, ['boolean', 'tinyint']) &&
            Str::startsWith($columnName, ['is_', 'has_', 'can_', 'should_'])
        ) {
            return 'ToggleField';
        }

        // Handle specific column names
        if (Str::endsWith($columnName, '_password') || $columnName === 'password') {
            return 'InputField::password';
        }

        if (Str::endsWith($columnName, '_email') || $columnName === 'email') {
            return 'InputField::email';
        }

        if (Str::contains($columnName, 'url')) {
            return 'InputField::url';
        }

        if (Str::contains($columnName, 'phone') || Str::contains($columnName, 'tel')) {
            return 'InputField::tel';
        }

        if (
            in_array($columnName, ['date', 'birth_date', 'start_date', 'end_date']) ||
            Str::endsWith($columnName, '_date')
        ) {
            return 'DateTimeField::date';
        }

        if (in_array($columnName, ['time', 'start_time', 'end_time']) || Str::endsWith($columnName, '_time')) {
            return 'DateTimeField::time';
        }

        if (Str::contains($columnName, ['datetime', 'timestamp'])) {
            return 'DateTimeField::datetime';
        }

        // Handle column types
        return match ($columnType) {
            'text', 'longtext', 'mediumtext', 'json' => 'TextareaField',
            'boolean', 'tinyint' => 'CheckboxField',
            'integer', 'bigint', 'smallint', 'mediumint', 'decimal', 'float', 'double' => 'InputField::number',
            'enum' => 'SelectField',
            'date' => 'DateTimeField::date',
            'time' => 'DateTimeField::time',
            'datetime', 'timestamp' => 'DateTimeField::datetime',
            default => 'InputField',
        };
    }

    protected function generateFormFieldCode(string $column, string $fieldType): string
    {
        $label = Str::title(Str::replace('_', ' ', $column));

        if (Str::contains($fieldType, '::')) {
            [$baseType, $method] = explode('::', $fieldType);

            return "{$baseType}::make('{$column}')\n->label(__('{$label}'))\n->{$method}()";
        }

        $code = "{$fieldType}::make('{$column}')\n->label(__('{$label}'))";

        // Add specific configurations based on field type
        if ($fieldType === 'SelectField') {
            $code .= "\n->options([])"; // User will need to fill this
        }

        if ($fieldType === 'TextareaField') {
            $code .= "\n->rows(3)";
        }

        return $code;
    }
}
