# Forms

The Laravel Simple Datatables And Forms package includes comprehensive form functionality, similar to Filament Forms,
allowing you
to easily create dynamic, configurable forms in your Livewire components.

## Quick Start

### 1. Use the HasForm Trait

Add the `HasForm` trait to your Livewire component:

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasForm;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;

class CreateUser extends Component
{
    use HasForm;

    public function mount(): void
    {
        $this->mountForm();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            // Your form fields here
        ]);
    }

    public function render()
    {
        return view('livewire.create-user');
    }
}
```

### 2. Add Form to Your View

In your Blade template:

```blade
<form wire:submit.prevent="save">
    {{ $this->form }}

    <button type="submit">Save</button>
</form>
```

## Available Form Fields

### InputField

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;

InputField::make('name')
    ->label('Full Name')
    ->placeholder('Enter your name')
    ->required()
    ->maxLength(255),

// Different input types
InputField::make('email')->email(),
InputField::make('password')->password(),
InputField::make('age')->number()->min(0)->max(120),
InputField::make('website')->url(),
InputField::make('phone')->tel(),
InputField::make('birth_date')->date(),
InputField::make('start_time')->time(),
InputField::make('appointment')->datetime(),
```

### SelectField

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;

SelectField::make('country')
    ->label('Country')
    ->options([
        'us' => 'United States',
        'uk' => 'United Kingdom',
        'ca' => 'Canada',
    ])
    ->emptyOption('Select a country')
    ->required(),

// Multiple selection
SelectField::make('skills')
    ->multiple()
    ->options($skillsArray),

// Using Enum
SelectField::make('status')
    ->options(UserStatus::class), // Enum class
```

### CheckboxField

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\CheckboxField;

CheckboxField::make('terms')
    ->label('I agree to the terms and conditions')
    ->required(),

CheckboxField::make('newsletter')
    ->label('Subscribe to newsletter')
    ->checkedValue(1)
    ->uncheckedValue(0)
    ->inline(),
```

### ToggleField

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\ToggleField;

ToggleField::make('is_active')
    ->label('Active')
    ->onLabel('Enabled')
    ->offLabel('Disabled')
    ->color('green')
    ->size('lg'),
```

### TextareaField

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\TextareaField;

TextareaField::make('description')
    ->label('Description')
    ->rows(5)
    ->maxLength(1000)
    ->autosize()
    ->placeholder('Enter description...'),
```

## Form Configuration

### Setting Form Columns

```php
public function form(Form $form): Form
{
    return $form
        ->columns(3) // 3 columns layout
        ->schema([
            // fields...
        ]);
}
```

### Form Heading

```php
public function form(Form $form): Form
{
    return $form
        ->heading([
            'title' => 'User Profile',
            'description' => 'Manage your profile information.',
        ])
        ->schema([
            // fields...
        ]);
}
```

## Working with Models

### Loading Model Data

```php
public function mount(User $user = null): void
{
    $this->mountForm();

    if ($user->exists) {
        $this->loadFormModel($user);
    }
}
```

### Saving Data

```php
public function save(): void
{
    $validatedData = $this->validate($this->getValidationRulesFromForm());

    if ($this->formModel) {
        // Update existing model
        $this->formModel->update($this->formData);
    } else {
        // Create new model
        User::create($this->formData);
    }

    session()->flash('message', 'Saved successfully!');
}
```

## Generating Forms with Artisan

Generate a new form component:

```bash
php artisan make:milenmk-form Admin/CreateUser create User

# With auto-generated fields
php artisan make:milenmk-form Admin/CreateUser create User --generate
```

This creates:

- A Livewire component with the HasForm trait
- A Blade view template
- Auto-generated form fields (when using --generate)

## Configuration

The form behavior can be configured in `config/simple-datatables-and-forms.php`:

```php
'form' => [
    'theme' => 'light', // 'light', 'dark', 'auto'
    'columns' => 2,     // Default form columns
    'client_validation' => true,
    'realtime_validation' => false,
    // ... other options
],
```

## Styling

The package includes default Tailwind CSS styles. You can customize the appearance by:

1. Publishing the views: `php artisan vendor:publish --tag=laravel-simple-datatables-and-forms-views`
2. Customizing the CSS classes in the published view files
3. Adding custom CSS to your application

## See Also

- [Field Types](field-types.md) - Detailed documentation for all field types
- [Form Validation](validation.md) - Validation rules and techniques
- [Form Sections](sections.md) - Organizing forms with sections
- [Model Binding](model-binding.md) - Advanced model integration
