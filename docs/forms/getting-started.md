# Getting Started with Dynamic Forms

This guide will help you create your first dynamic form using Laravel Simple Datatables And Forms.

## Prerequisites

Before you begin, ensure you have:

- Laravel 10.x or higher installed
- Livewire 3.x installed and configured
- Laravel Simple Datatables And Forms package installed

## Creating Your First Form

### Method 1: Using Artisan Command (Recommended)

The fastest way to create a form is using the provided Artisan command:

```bash
# Basic form generation
php artisan make:milenmk-form Admin/CreateUser User

# Generate with auto-generated fields based on model
php artisan make:milenmk-form Admin/CreateUser User --generate

# Generate for editing existing records
php artisan make:milenmk-form Admin/EditUser User --generate
```

This creates:

- A Livewire component at `app/Livewire/Admin/CreateUser.php`
- A Blade view at `resources/views/livewire/admin/create-user.blade.php`
- Auto-generated form fields (when using `--generate` flag)

### Method 2: Manual Creation

#### Step 1: Create a Livewire Component

```php
<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasForm;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\TextareaField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\ToggleField;
use App\Models\User;

class CreateUser extends Component
{
    use HasForm;

    public function mount(): void
    {
        $this->mountForm();
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(User::class)
            ->heading([
                'title' => 'Create New User',
                'description' => 'Add a new user to the system with the required information.',
            ])
            ->columns(2) // Two-column layout
            ->schema([
                InputField::make('name')
                    ->label('Full Name')
                    ->placeholder('Enter the user\'s full name')
                    ->required()
                    ->columnSpan(2), // Span across both columns

                InputField::make('email')->label('Email Address')->email()->placeholder('user@example.com')->required(),

                InputField::make('phone')->label('Phone Number')->tel()->placeholder('+1 (555) 123-4567'),

                SelectField::make('role')
                    ->label('User Role')
                    ->options([
                        'admin' => 'Administrator',
                        'manager' => 'Manager',
                        'user' => 'Regular User',
                    ])
                    ->emptyOption('Select a role')
                    ->required(),

                SelectField::make('department')
                    ->label('Department')
                    ->options([
                        'hr' => 'Human Resources',
                        'it' => 'Information Technology',
                        'sales' => 'Sales',
                        'marketing' => 'Marketing',
                    ])
                    ->emptyOption('Select department'),

                TextareaField::make('bio')
                    ->label('Biography')
                    ->placeholder('Brief description about the user...')
                    ->rows(4)
                    ->columnSpan(2),

                ToggleField::make('is_active')
                    ->label('Active User')
                    ->onLabel('Active')
                    ->offLabel('Inactive')
                    ->default(true),

                ToggleField::make('email_verified')
                    ->label('Email Verified')
                    ->onLabel('Verified')
                    ->offLabel('Unverified')
                    ->default(false),
            ]);
    }

    public function save()
    {
        // Validate the form data
        $this->validate();

        // Create the user
        $user = User::create($this->formData);

        // Flash success message
        session()->flash('message', 'User created successfully!');

        // Redirect or reset form
        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.admin.create-user');
    }
}
```

#### Step 2: Create the Blade View

Create `resources/views/livewire/admin/create-user.blade.php`:

```blade
<div class="mx-auto max-w-4xl">
    <div class="rounded-lg bg-white shadow-lg dark:bg-gray-800">
        <div class="p-6">
            @if (session()->has('message'))
                <div class="mb-4 rounded border border-green-400 bg-green-100 p-4 text-green-700">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit.prevent="save">
                {{ $this->form }}

                <div class="mt-6 flex justify-end space-x-3">
                    <button
                        type="button"
                        onclick="window.history.back()"
                        class="rounded-md bg-gray-300 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
                    >
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

#### Step 3: Add Routes

Add routes to display and handle your form:

```php
// routes/web.php
use App\Livewire\Admin\CreateUser;

Route::get('/admin/users/create', CreateUser::class)->name('admin.users.create');
```

## Understanding Form Structure

### Basic Form Configuration

```php
public function form(Form $form): Form
{
    return $form
        ->model(User::class)             // Associated model
        ->heading([                      // Form title and description
            'title' => 'Create User',
            'description' => 'Add a new user to the system.',
        ])
        ->columns(2)                     // Number of columns
        ->schema([                       // Field definitions
            // Fields go here
        ]);
}
```

### Form Layout Options

```php
public function form(Form $form): Form
{
    return $form
        ->model(User::class)
        ->columns(3)                     // 3-column layout
        ->columnSpanFull()               // Make all fields span full width by default
        ->compact()                      // Reduce spacing
        ->schema([
            InputField::make('name')
                ->columnSpan(2),         // This field spans 2 columns

            InputField::make('email')
                ->columnSpan(1),         // This field spans 1 column

            TextareaField::make('bio')
                ->columnSpanFull(),      // This field spans all columns
        ]);
}
```

## Common Field Types

### Input Fields

```php
// Text input
InputField::make('name')
    ->label('Full Name')
    ->placeholder('Enter name')
    ->required(),

// Email input with validation
InputField::make('email')
    ->email()
    ->required(),

// Password input
InputField::make('password')
    ->password()
    ->minLength(8)
    ->required(),

// Number input with constraints
InputField::make('age')
    ->number()
    ->min(18)
    ->max(100),

// Date input
InputField::make('birth_date')
    ->date()
    ->before('today'),

// URL input
InputField::make('website')
    ->url()
    ->placeholder('https://example.com'),
```

### Select Fields

```php
// Basic select
SelectField::make('country')
    ->options([
        'us' => 'United States',
        'uk' => 'United Kingdom',
        'ca' => 'Canada',
    ])
    ->emptyOption('Select country')
    ->required(),

// Multiple select
SelectField::make('skills')
    ->multiple()
    ->options([
        'php' => 'PHP',
        'javascript' => 'JavaScript',
        'python' => 'Python',
    ]),

// Using Enum
SelectField::make('status')
    ->options(UserStatus::class), // Enum class
```

### Other Field Types

```php
// Textarea
TextareaField::make('description')
    ->rows(5)
    ->maxLength(1000)
    ->autosize(),

// Checkbox
CheckboxField::make('terms')
    ->label('I agree to the terms and conditions')
    ->required(),

// Toggle switch
ToggleField::make('is_active')
    ->label('Active')
    ->onLabel('Enabled')
    ->offLabel('Disabled')
    ->default(true),
```

## Form Validation

### Field-Level Validation

```php
InputField::make('email')
    ->email()
    ->rules(['required', 'email', 'unique:users,email'])
    ->required(), // Visual indicator

InputField::make('age')
    ->number()
    ->rules(['required', 'integer', 'min:18', 'max:120']),
```

### Custom Validation Messages

```php
public function getValidationMessages(): array
{
    return [
        'formData.email.required' => 'The email address is required.',
        'formData.email.unique' => 'This email address is already taken.',
        'formData.age.min' => 'You must be at least 18 years old.',
    ];
}
```

### Real-time Validation

```php
public function form(Form $form): Form
{
    return $form
        ->model(User::class)
        ->realtimeValidation() // Enable real-time validation
        ->schema([
            // fields...
        ]);
}
```

## Working with Models

### Creating Records

```php
public function save()
{
    $this->validate();

    $user = User::create($this->formData);

    session()->flash('message', 'User created successfully!');
    return redirect()->route('users.index');
}
```

### Editing Existing Records

```php
public User $user;

public function mount(User $user): void
{
    $this->user = $user;
    $this->mountForm();
    $this->loadFormModel($user);
}

public function save()
{
    $this->validate();

    $this->user->update($this->formData);

    session()->flash('message', 'User updated successfully!');
}
```

## Form Sections

Organize your form with sections:

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Components\Section;

public function form(Form $form): Form
{
    return $form
        ->model(User::class)
        ->schema([
            Section::make('Personal Information')
                ->description('Basic user details')
                ->schema([
                    InputField::make('name')->required(),
                    InputField::make('email')->email()->required(),
                    InputField::make('phone')->tel(),
                ]),

            Section::make('Account Settings')
                ->description('User permissions and preferences')
                ->schema([
                    SelectField::make('role')->required(),
                    ToggleField::make('is_active')->default(true),
                    ToggleField::make('email_verified'),
                ]),
        ]);
}
```

## Styling and Customization

### Field Icons

```php
InputField::make('email')
    ->prefixIcon('heroicon-o-envelope')
    ->suffixIcon('heroicon-o-at-symbol'),

InputField::make('phone')
    ->prefixIcon('heroicon-o-phone'),
```

### Field Help Text

```php
InputField::make('password')
    ->password()
    ->helperText('Password must be at least 8 characters long')
    ->minLength(8),
```

### Conditional Fields

```php
SelectField::make('user_type')
    ->options(['individual' => 'Individual', 'business' => 'Business'])
    ->reactive(), // Make field reactive

InputField::make('company_name')
    ->label('Company Name')
    ->visible(fn() => $this->formData['user_type'] === 'business'),
```

## Next Steps

Now that you have a basic form working, explore these advanced features:

1. **[Field Types](field-types.md)** - Complete reference for all available field types
2. **[Form Validation](validation.md)** - Advanced validation techniques
3. **[Form Sections](sections.md)** - Organize complex forms with sections
4. **[Model Binding](model-binding.md)** - Advanced model integration
5. **[Form Examples](form-features-examples.md)** - Practical examples and advanced features

## Troubleshooting

### Common Issues

**Form not displaying:**

- Ensure you've called `$this->mountForm()` in your `mount()` method
- Check that you've included the form assets in your layout

**Validation not working:**

- Verify that you're calling `$this->validate()` in your save method
- Check that validation rules are properly defined

**Data not saving:**

- Ensure your model has the correct `$fillable` properties
- Check that form field names match your model attributes

**Styling issues:**

- Ensure Tailwind CSS is properly configured
- Check that the package views are included in your Tailwind content paths

For more troubleshooting help, see our [GitHub Issues](https://github.com/milenmk/laravel-simple-datatables-and-forms/issues) page.

## Quick Reference & Common Patterns

### Installation & Setup Commands

```bash
# Install package
composer require milenmk/laravel-simple-datatables-and-forms

# Publish assets
php artisan simple-datatables-and-forms:publish-assets

# Publish configuration (optional)
php artisan vendor:publish --tag=laravel-simple-datatables-and-forms-config

# Generate form component
php artisan make:milenmk-form CreateUser User --generate

# Generate nested form component
php artisan make:milenmk-form Admin/CreateUser User --generate
```

### Essential Component Structure

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasForm;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;

class CreateUser extends Component
{
    use HasForm;

    public function mount(): void
    {
        $this->mountForm();
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(User::class)
            ->schema([InputField::make('name')->required(), InputField::make('email')->email()->required()]);
    }

    public function save()
    {
        $this->validate();
        User::create($this->formData);
        $this->notification()->success('User created successfully!');
    }
}
```

### Common Field Types Cheat Sheet

```php
// Input Fields
InputField::make('name')
    ->label('Full Name')
    ->placeholder('Enter your name')
    ->required()
    ->maxLength(255),

InputField::make('email')
    ->email()
    ->required()
    ->placeholder('user@example.com'),

InputField::make('password')
    ->password()
    ->minLength(8)
    ->required(),

InputField::make('age')
    ->numeric()
    ->min(18)
    ->max(100),

InputField::make('website')
    ->url()
    ->placeholder('https://example.com'),

InputField::make('birth_date')
    ->date()
    ->required(),

// Select Fields
SelectField::make('role')
    ->options(['admin' => 'Admin', 'user' => 'User'])
    ->emptyOption('Select role')
    ->required(),

SelectField::make('permissions')
    ->options(['read' => 'Read', 'write' => 'Write', 'delete' => 'Delete'])
    ->multiple()
    ->required(),

SelectField::make('user_id')
    ->relationship('user', 'name')
    ->searchable()
    ->required(),

// Textarea Fields
TextareaField::make('bio')
    ->label('Biography')
    ->rows(4)
    ->maxLength(500)
    ->placeholder('Tell us about yourself...'),

TextareaField::make('description')
    ->autoResize()
    ->minRows(3)
    ->maxRows(10),

// Toggle & Checkbox Fields
ToggleField::make('is_active')
    ->label('Active')
    ->default(true)
    ->onLabel('Active')
    ->offLabel('Inactive'),

CheckboxField::make('terms')
    ->label('I agree to the terms and conditions')
    ->required(),

CheckboxField::make('interests')
    ->options([
        'sports' => 'Sports',
        'music' => 'Music',
        'travel' => 'Travel',
    ])
    ->multiple(),
```

### Form Layout & Styling

```php
// Column layouts
->columns(2) // Two-column form

// Field spanning multiple columns
InputField::make('description')
    ->columnSpan(2), // Spans 2 columns

// Field spanning full width
InputField::make('notes')
    ->columnSpanFull(), // Spans all columns

// Responsive columns
->columns([
    'sm' => 1,
    'md' => 2,
    'lg' => 3,
])

// Field with custom styling
InputField::make('amount')
    ->prefix('$')
    ->suffix('USD')
    ->placeholder('0.00'),

// Field with help text
InputField::make('username')
    ->helperText('Must be unique and contain only letters and numbers')
    ->required(),
```

### Form Sections

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Components\Section;

->schema([
    Section::make('Personal Information')
        ->description('Basic personal details')
        ->schema([
            InputField::make('first_name')->required(),
            InputField::make('last_name')->required(),
            InputField::make('email')->email()->required(),
        ])
        ->columns(2),

    Section::make('Account Settings')
        ->schema([
            SelectField::make('role')->required(),
            ToggleField::make('is_active')->default(true),
        ]),
])
```

### Validation Quick Reference

```php
// Basic validation rules
InputField::make('name')->required(),
InputField::make('email')->email()->required(),
InputField::make('age')->numeric()->min(18)->max(100),
InputField::make('username')->minLength(3)->maxLength(20),
InputField::make('website')->url(),
InputField::make('birth_date')->date()->before('today'),

// Custom validation in component
protected function rules(): array
{
    return [
        'formData.username' => ['required', 'unique:users,username'],
        'formData.email' => ['required', 'email', 'unique:users,email'],
        'formData.password' => ['required', 'min:8', 'confirmed'],
    ];
}

// Custom validation messages
protected function messages(): array
{
    return [
        'formData.username.unique' => 'This username is already taken.',
        'formData.email.unique' => 'This email is already registered.',
    ];
}

// Real-time validation
InputField::make('email')
    ->email()
    ->required()
    ->live(), // Validates on input change

// Debounced validation
InputField::make('username')
    ->required()
    ->live(onBlur: true), // Validates on blur
```

### Model Binding & Data Handling

```php
// Create form
public function form(Form $form): Form
{
    return $form
        ->model(User::class)
        ->schema([...]);
}

// Edit form
public function mount(User $user): void
{
    $this->mountForm($user);
}

public function form(Form $form): Form
{
    return $form
        ->model($this->record ?? User::class)
        ->schema([...]);
}

// Save with relationships
public function save()
{
    $this->validate();

    $user = User::create($this->formData);

    // Sync many-to-many relationships
    if (isset($this->formData['roles'])) {
        $user->roles()->sync($this->formData['roles']);
    }

    $this->notification()->success('User created successfully!');
}
```

### Conditional Logic

```php
// Show field based on another field's value
InputField::make('company')
    ->visible(fn() => $this->formData['user_type'] === 'business'),

// Show field based on multiple conditions
InputField::make('tax_id')
    ->visible(fn() =>
        $this->formData['user_type'] === 'business' &&
        $this->formData['country'] === 'US'
    ),

// Dynamic select options
SelectField::make('city')
    ->options(fn() => $this->getCitiesForCountry($this->formData['country'] ?? null))
    ->searchable(),
```

### Common Troubleshooting Solutions

```php
// Fix: Form not saving
public function save()
{
    $this->validate(); // Don't forget this!
    User::create($this->formData);
}

// Fix: Form not displaying
public function mount(): void
{
    $this->mountForm(); // Don't forget this!
}

// Fix: Validation not working
protected function rules(): array
{
    return [
        'formData.email' => ['required', 'email'], // Use formData prefix
    ];
}

// Fix: Field not reactive
SelectField::make('country')
    ->live() // Make field reactive for conditional logic
    ->options($countries),
```

### Form Notifications

```php
// Success notification
$this->notification()->success('Form saved successfully!');

// Error notification
$this->notification()->error('Please fix the errors below.');

// Warning notification
$this->notification()->warning('Some fields need attention.');

// Info notification
$this->notification()->info('Form auto-saved.');
```
