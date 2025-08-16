# Field Types

This document provides comprehensive information about all available form field types in Laravel Simple Datatables And
Forms.

## InputField

The most versatile field type for various input types.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;

InputField::make('name')->label('Full Name')->placeholder('Enter your name')->required();
```

### Input Types

```php
// Text input (default)
InputField::make('name'),

// Email input with validation
InputField::make('email')->email(),

// Password input (hidden)
InputField::make('password')->password(),

// Number input with min/max
InputField::make('age')->number()->min(0)->max(120),

// URL input
InputField::make('website')->url(),

// Telephone input
InputField::make('phone')->tel(),

// Date input
InputField::make('birth_date')->date(),

// Time input
InputField::make('start_time')->time(),

// Datetime input
InputField::make('appointment')->datetime(),

// Search input
InputField::make('search_term')->search(),
```

### Available Methods

```php
InputField::make('field_name')
    ->label('Field Label') // Field label
    ->placeholder('Enter value...') // Placeholder text
    ->default('default_value') // Default value
    ->required() // Mark as required
    ->disabled() // Disable the field
    ->readonly() // Make read-only
    ->maxLength(255) // Maximum character length
    ->minLength(3) // Minimum character length
    ->pattern('[A-Za-z]+') // Regex pattern
    ->step(0.01) // Step for number inputs
    ->min(0) // Minimum value for numbers
    ->max(100) // Maximum value for numbers
    ->helperText('Additional information') // Helper text below field
    ->columnSpan('2') // Column span in grid
    ->attributes(['data-custom' => 'value']); // Custom HTML attributes
```

## SelectField

Dropdown selection field with single or multiple options.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;

SelectField::make('country')
    ->label('Country')
    ->options([
        'us' => 'United States',
        'uk' => 'United Kingdom',
        'ca' => 'Canada',
    ])
    ->required();
```

### Multiple Selection

```php
SelectField::make('skills')
    ->label('Skills')
    ->multiple()
    ->options([
        'php' => 'PHP',
        'javascript' => 'JavaScript',
        'python' => 'Python',
    ]);
```

### Using Enums

```php
enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
}

SelectField::make('status')->options(UserStatus::class);
```

### Available Methods

```php
SelectField::make('field_name')
    ->label('Field Label')
    ->options($optionsArray) // Options array or Enum class
    ->emptyOption('Choose an option...') // Empty option text
    ->multiple() // Allow multiple selections
    ->searchable() // Make options searchable
    ->default('default_value') // Default selection
    ->disabled() // Disable the field
    ->required() // Mark as required
    ->helperText('Choose from the list') // Helper text
    ->columnSpan('2'); // Column span
```

## CheckboxField

Simple checkbox for boolean values or consent.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\CheckboxField;

CheckboxField::make('terms')->label('I agree to the terms and conditions')->required();
```

### Custom Values

```php
CheckboxField::make('newsletter')
    ->label('Subscribe to newsletter')
    ->checkedValue(1) // Value when checked
    ->uncheckedValue(0) // Value when unchecked
    ->default(true); // Default checked state
```

### Available Methods

```php
CheckboxField::make('field_name')
    ->label('Checkbox Label')
    ->checkedValue('yes') // Value when checked (default: 1)
    ->uncheckedValue('no') // Value when unchecked (default: 0)
    ->inline() // Display inline
    ->default(true) // Default checked state
    ->disabled() // Disable checkbox
    ->required() // Mark as required
    ->helperText('Check to confirm'); // Helper text
```

## ToggleField

Modern toggle switch for boolean values.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\ToggleField;

ToggleField::make('is_active')
    ->label('Active Status')
    ->default(true)
```

### Custom Labels and Colors

```php
ToggleField::make('notifications')
    ->label('Email Notifications')
    ->onLabel('Enabled')
    ->offLabel('Disabled')
    ->color('green')
    ->size('lg');
```

### Available Methods

```php
ToggleField::make('field_name')
    ->label('Toggle Label')
    ->onLabel('On') // Label for on state
    ->offLabel('Off') // Label for off state
    ->onValue('enabled') // Value when on (default: 1)
    ->offValue('disabled') // Value when off (default: 0)
    ->color('green') // Color: 'primary', 'green', 'red'
    ->size('lg') // Size: 'sm', 'md', 'lg'
    ->default(true) // Default state
    ->disabled() // Disable toggle
    ->helperText('Toggle to change'); // Helper text
```

## TextareaField

Multi-line text input for longer content.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\TextareaField;

TextareaField::make('description')->label('Description')->placeholder('Enter description...')->rows(5);
```

### Auto-resizing

```php
TextareaField::make('content')
    ->label('Content')
    ->autosize() // Auto-resize based on content
    ->maxLength(1000); // Character limit
```

### Available Methods

```php
TextareaField::make('field_name')
    ->label('Textarea Label')
    ->placeholder('Enter text...') // Placeholder text
    ->rows(4) // Number of rows
    ->cols(50) // Number of columns
    ->maxLength(500) // Maximum character length
    ->minLength(10) // Minimum character length
    ->autosize() // Auto-resize based on content
    ->disabled() // Disable textarea
    ->readonly() // Make read-only
    ->required() // Mark as required
    ->default('Default content') // Default content
    ->helperText('Provide detailed info') // Helper text
    ->columnSpan('full'); // Column span
```

## Common Field Properties

All fields share these common properties and methods:

### Validation

```php
// Basic validation
InputField::make('email')
    ->rules(['required', 'email', 'unique:users'])

// Custom validation messages
InputField::make('name')
    ->rules(['required', 'min:3'])
    ->validationMessages([
        'required' => 'Name is required',
        'min' => 'Name must be at least 3 characters'
    ])
```

### Conditional Display

```php
InputField::make('other_reason')->label('Other Reason')->visible(fn($get) => $get('reason') === 'other');
```

### Column Spanning

```php
// Span specific number of columns
InputField::make('address')->columnSpan('2')

// Span full width
TextareaField::make('notes')->columnSpan('full')
```

### Custom Attributes

```php
InputField::make('username')->attributes([
    'autocomplete' => 'username',
    'class' => 'custom-class',
    'data-validation' => 'true',
    'x-data' => '{ focused: false }',
]);
```

### Helper Text and Hints

```php
InputField::make('password')
    ->password()
    ->helperText('Password must be at least 8 characters long')
    ->hint('Use a mix of letters, numbers, and symbols');
```

## Creating Custom Fields

You can create custom field types by extending the base `Field` class:

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\Field;

class ColorField extends Field
{
    protected string $view = 'custom.fields.color';

    public function render(): string
    {
        return view($this->view, ['field' => $this])->render();
    }

    public function color(string $type = 'full'): static
    {
        $this->inputType = 'color';

        return $this;
    }
}
```

Then use it in your forms:

```php
ColorField::make('brand_color')->label('Brand Color')->default('#3B82F6');
```
