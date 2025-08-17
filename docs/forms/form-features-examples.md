# Form Features Examples

This document demonstrates the new form features including icons, collapsible sections, and enhanced security.

## 1. Input Fields with Icons

### Prefix Icons

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;

// Using Heroicon
InputField::make('phone')
    ->label('Phone Number')
    ->prefixIcon('heroicon-o-phone')
    ->placeholder('Enter your phone number');

// Using custom SVG file
InputField::make('email')->label('Email Address')->prefixIcon('/images/icons/email.svg')->type('email');

// Using image file
InputField::make('website')->label('Website')->prefixIcon('/images/icons/globe.png')->type('url');
```

### Suffix Icons

```php
InputField::make('password')->label('Password')->type('password')->suffixIcon('heroicon-o-eye-slash');

InputField::make('search')->label('Search')->type('search')->suffixIcon('heroicon-o-magnifying-glass');
```

### Both Prefix and Suffix Icons

```php
InputField::make('amount')
    ->label('Amount')
    ->type('number')
    ->prefixIcon('heroicon-o-currency-dollar')
    ->suffixIcon('heroicon-o-calculator');
```

## 2. Textarea Fields with Icons

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\TextareaField;

TextareaField::make('description')
    ->label('Description')
    ->prefixIcon('heroicon-o-document-text')
    ->rows(4)
    ->placeholder('Enter description...');

TextareaField::make('notes')->label('Notes')->suffixIcon('heroicon-o-pencil-square')->rows(3);
```

## 3. Collapsible Form Sections

### Basic Collapsible Section

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;

public function form(Form $form): Form
{
    return $form
        ->sections([
            Section::make('personal_info')
                ->label('Personal Information')
                ->description('Basic personal details')
                ->icon('heroicon-o-user')
                ->collapsible()
                ->collapsed(false) // Start expanded
                ->schema([
                    InputField::make('first_name')
                        ->label('First Name')
                        ->prefixIcon('heroicon-o-user')
                        ->required(),

                    InputField::make('last_name')
                        ->label('Last Name')
                        ->prefixIcon('heroicon-o-user')
                        ->required(),

                    InputField::make('email')
                        ->label('Email')
                        ->type('email')
                        ->prefixIcon('heroicon-o-envelope')
                        ->required(),
                ]),

            Section::make('contact_info')
                ->label('Contact Information')
                ->description('Phone and address details')
                ->icon('heroicon-o-phone')
                ->collapsible()
                ->collapsed(true) // Start collapsed
                ->schema([
                    InputField::make('phone')
                        ->label('Phone')
                        ->type('tel')
                        ->prefixIcon('heroicon-o-phone'),

                    InputField::make('address')
                        ->label('Address')
                        ->prefixIcon('heroicon-o-map-pin'),

                    InputField::make('city')
                        ->label('City')
                        ->prefixIcon('heroicon-o-building-office'),
                ]),
        ]);
}
```

### Section with Custom Icons

```php
Section::make('job_info')
    ->label('Job Information')
    ->description('Employment details')
    ->icon('/images/icons/briefcase.svg') // Custom SVG
    ->collapsible()
    ->schema([
        InputField::make('company')->label('Company')->prefixIcon('heroicon-o-building-office-2'),

        InputField::make('position')->label('Position')->prefixIcon('heroicon-o-briefcase'),

        InputField::make('salary')->label('Salary')->type('number')->prefixIcon('heroicon-o-currency-dollar'),
    ]);
```

## 4. Icon Types Supported

### Heroicons (Recommended)

```php
// Outline icons
->prefixIcon('heroicon-o-user')
->prefixIcon('heroicon-o-envelope')
->prefixIcon('heroicon-o-phone')

// Solid icons
->prefixIcon('heroicon-s-user')
->prefixIcon('heroicon-s-envelope')
->prefixIcon('heroicon-s-phone')

// Mini icons
->prefixIcon('heroicon-m-user')
->prefixIcon('heroicon-m-envelope')
->prefixIcon('heroicon-m-phone')
```

### Custom SVG Files

```php
->prefixIcon('/images/icons/custom-icon.svg')
->suffixIcon('/assets/icons/search.svg')
```

### Image Files

```php
->prefixIcon('/images/icons/logo.png')
->suffixIcon('/images/icons/arrow.jpg')
```

### HTML/Blade Components

```php
// Using existing icon components
->prefixIcon('<x-custom-icon name="user" />')
```

## 5. Complete Form Example

```php
<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\TextareaField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasForm;

class UserProfileForm extends Component
{
    use HasForm;

    public function mount(): void
    {
        $this->mountForm();
    }

    public function form(Form $form): Form
    {
        return $form
            ->heading([
                'title' => 'User Profile',
                'description' => 'Manage your personal information and preferences',
            ])
            ->sections([
                Section::make('basic_info')
                    ->label('Basic Information')
                    ->description('Your personal details')
                    ->icon('heroicon-o-user-circle')
                    ->collapsible()
                    ->collapsed(false)
                    ->columns(2)
                    ->schema([
                        InputField::make('first_name')
                            ->label('First Name')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->rules(['required', 'string', 'max:50']),

                        InputField::make('last_name')
                            ->label('Last Name')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->rules(['required', 'string', 'max:50']),

                        InputField::make('email')
                            ->label('Email Address')
                            ->type('email')
                            ->prefixIcon('heroicon-o-envelope')
                            ->suffixIcon('heroicon-o-at-symbol')
                            ->required()
                            ->rules(['required', 'email', 'unique:users,email']),

                        InputField::make('phone')
                            ->label('Phone Number')
                            ->type('tel')
                            ->prefixIcon('heroicon-o-phone')
                            ->placeholder('+1 (555) 123-4567')
                            ->rules(['nullable', 'string', 'max:20']),
                    ]),

                Section::make('address_info')
                    ->label('Address Information')
                    ->description('Your location details')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->collapsed(true)
                    ->columns(1)
                    ->schema([
                        InputField::make('street_address')
                            ->label('Street Address')
                            ->prefixIcon('heroicon-o-home')
                            ->placeholder('123 Main Street'),

                        InputField::make('city')
                            ->label('City')
                            ->prefixIcon('heroicon-o-building-office')
                            ->columnSpan('1'),

                        InputField::make('postal_code')
                            ->label('Postal Code')
                            ->prefixIcon('heroicon-o-map')
                            ->columnSpan('1'),
                    ]),

                Section::make('preferences')
                    ->label('Preferences')
                    ->description('Your account preferences')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->collapsed(true)
                    ->schema([
                        SelectField::make('timezone')
                            ->label('Timezone')
                            ->options([
                                'UTC' => 'UTC',
                                'America/New_York' => 'Eastern Time',
                                'America/Chicago' => 'Central Time',
                                'America/Denver' => 'Mountain Time',
                                'America/Los_Angeles' => 'Pacific Time',
                            ])
                            ->required(),

                        TextareaField::make('bio')
                            ->label('Biography')
                            ->prefixIcon('heroicon-o-document-text')
                            ->rows(4)
                            ->placeholder('Tell us about yourself...')
                            ->helperText('Maximum 500 characters')
                            ->rules(['nullable', 'string', 'max:500']),
                    ]),
            ]);
    }

    public function save()
    {
        // Apply rate limiting and security checks
        $this->validate($this->getValidationRulesFromForm());

        // Get sanitized and filtered form data
        $formData = $this->getFillableFormData();

        // Save the data
        auth()->user()->update($formData);

        session()->flash('message', 'Profile updated successfully!');
    }

    public function render()
    {
        return view('livewire.forms.user-profile-form');
    }
}
```

## 6. Security Features

All form inputs are automatically:

- **Sanitized** for XSS protection
- **Validated** against allowed field types
- **Rate Limited** to prevent abuse
- **CSRF Protected** automatically
- **Mass Assignment Protected** via fillable filtering

### Custom Security Configuration

```php
// config/simple-datatables-and-forms.php
'security' => [
    'csrf_protection' => true,
    'sanitize_input' => true,
    'rate_limiting' => [
        'enable' => true,
        'max_attempts' => 60,
        'decay_minutes' => 1,
    ],
    'allowed_file_types' => [
        'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'
    ],
    'max_file_size' => 10240, // KB
],
```

## 7. Styling Customization

The form components use CSS classes that can be customized:

```css
/* Icon positioning */
.form-field .relative .absolute {
    /* Customize icon positioning */
}

/* Collapsible sections */
.form-section.collapsible {
    /* Custom collapsible styling */
}

/* Input with icons */
.form-input.ps-10 {
    /* Input with prefix icon */
}

.form-input.pe-10 {
    /* Input with suffix icon */
}
```

## 8. Form Reactivity

A sample code fo a reactive form is like this:

```php
SelectField::make('type')
    ->label(__('Type'))
    ->emptyOption(__('Select Type'))
    ->reactive(),
SelectField::make('parent_id')
    ->label(__('Parent'))
    ->options(Model::whereNull('parent_id')->pluck('name', 'id')->toArray())
    ->emptyOption(__('Select Parent'))
    ->hidden(fn (Get $get) => $get('type') === 'heading')
    ->disabled(fn (Get $get) => $get('type') === 'heading'),
InputField::make('route')
    ->label(__('Route'))
    ->hidden(fn (Get $get) => $get('type') === 'heading'),
```

By adding `reactive()` to the first select, we make it available for other fields. Then in each field that depends on
the value of the first one, we use `fn(Get $get)` and `$get` will contain all values from the form.

As for the input field, if we do not add `reactive()` to it, it <b>always</b> remains hidden.

Why? Because of timing and default values:

- InputField by default might render before Livewire has set the initial state of type.
- When the closure runs, `$get('type')` is still null, so the condition `$get('type') === 'heading'` might evaluate as
  true
  in
  some frameworks (depending on truthiness checks), or more likely the field is evaluated too early and never updated.
- `SelectField::make('parent_id')` works without adding `reactive()`, because it’s designed for reactive option
  handling, whereas InputField may not re-evaluate its
  `hidden()` callback dynamically unless its own value or the parent state changes.
