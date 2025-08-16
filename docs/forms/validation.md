# Form Validation

Laravel Simple Datatables And Forms provides comprehensive validation features that integrate seamlessly with Laravel's
validation system.

## Basic Validation

### Field-Level Validation Rules

Add validation rules directly to form fields:

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;

public function form(Form $form): Form
{
    return $form->schema([
        InputField::make('name')
            ->label('Full Name')
            ->rules(['required', 'string', 'min:2', 'max:255'])
            ->required(), // Visual indicator

        InputField::make('email')
            ->label('Email Address')
            ->email()
            ->rules(['required', 'email', 'unique:users,email'])
            ->required(),

        InputField::make('age')
            ->label('Age')
            ->number()
            ->rules(['required', 'integer', 'min:18', 'max:120']),

        SelectField::make('role')
            ->label('Role')
            ->options(['admin' => 'Admin', 'user' => 'User'])
            ->rules(['required', 'in:admin,user'])
            ->required(),
    ]);
}
```

### Automatic Validation Rule Generation

Some field types automatically generate validation rules:

```php
// InputField generates rules based on its configuration
InputField::make('email')
    ->email()           // Adds 'email' rule
    ->required()        // Adds 'required' rule
    ->maxLength(255)    // Adds 'max:255' rule
    ->minLength(3),     // Adds 'min:3' rule

InputField::make('age')
    ->number()          // Adds 'numeric' rule
    ->min(18)           // Adds 'min:18' rule
    ->max(65),          // Adds 'max:65' rule

SelectField::make('status')
    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
    ->rules(['in:active,inactive']), // Auto-generated from options
```

## Custom Validation Messages

### Field-Level Messages

```php
InputField::make('password')
    ->password()
    ->rules(['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'])
    ->validationMessages([
        'required' => 'Password is required.',
        'min' => 'Password must be at least 8 characters.',
        'regex' => 'Password must contain uppercase, lowercase, and numeric characters.'
    ]),
```

### Component-Level Messages

Override validation messages at the component level:

```php
class CreateUserForm extends Component
{
    use HasForm;

    protected function getValidationMessages(): array
    {
        return [
            'formData.email.required' => 'Email address is mandatory.',
            'formData.email.unique' => 'This email is already registered.',
            'formData.name.min' => 'Name must be at least 2 characters long.',
        ];
    }
}
```

## Real-Time Validation

Enable real-time validation for immediate feedback:

```php
// In your component
class CreateUserForm extends Component
{
    use HasForm;

    // Enable real-time validation for specific fields
    protected $listeners = [
        'formData.email' => 'validateEmail',
        'formData.username' => 'validateUsername',
    ];

    public function validateEmail()
    {
        $this->validateOnly('formData.email', [
            'formData.email' => ['required', 'email', 'unique:users,email'],
        ]);
    }

    public function validateUsername()
    {
        $this->validateOnly('formData.username', [
            'formData.username' => ['required', 'string', 'unique:users,username'],
        ]);
    }
}
```

### Wire:Model with Validation

```php
// Enable real-time validation on blur
InputField::make('email')
    ->email()
    ->rules(['required', 'email', 'unique:users'])
    ->attributes(['wire:model.blur' => 'formData.email']),
```

## Advanced Validation

### Conditional Validation Rules

```php
InputField::make('other_reason')
    ->label('Other Reason')
    ->rules(function ($get) {
        return $get('reason') === 'other'
            ? ['required', 'string', 'min:10']
            : [];
    })
    ->visible(fn ($get) => $get('reason') === 'other'),
```

### Cross-Field Validation

```php
InputField::make('password_confirmation')
    ->password()
    ->label('Confirm Password')
    ->rules(['required', 'same:formData.password']),
```

### Custom Validation Rules

```php
use Illuminate\Validation\Rule;

SelectField::make('username')
    ->rules([
        'required',
        'string',
        Rule::unique('users')->ignore($this->formModel?->id),
        new CustomUsernameRule(),
    ]),
```

## Form Submission Validation

### Basic Form Validation

```php
public function save()
{
    // Get validation rules from form fields
    $rules = $this->getValidationRulesFromForm();

    // Validate the form data
    $validatedData = $this->validate($rules);

    // Process the validated data
    if ($this->formModel) {
        $this->formModel->update($this->formData);
    } else {
        User::create($this->formData);
    }

    $this->notification()->success('Saved successfully!');
}
```

### Custom Validation Logic

```php
public function save()
{
    // Custom validation with additional rules
    $customRules = [
        'formData.terms' => 'accepted',
        'formData.age' => ['required', function ($attribute, $value, $fail) {
            if ($value < 21 && $this->formData['alcohol_consent']) {
                $fail('You must be 21 or older to consent to alcohol.');
            }
        }],
    ];

    $rules = array_merge($this->getValidationRulesFromForm(), $customRules);

    $this->validate($rules);

    // Process form...
}
```

## Validation Error Display

### Field-Level Error Display

Errors are automatically displayed below each field:

```blade
{{-- Automatically rendered by the field --}}
<div class="form-field-wrapper">
    <input type="text" name="name" />
    @error('formData.name')
        <p class="field-error">{{ $message }}</p>
    @enderror
</div>
```

### Form-Level Error Summary

Display all errors at the top of the form:

```blade
<form wire:submit.prevent="save">
    {{-- Error summary --}}
    @if ($errors->any())
        <div class="error-summary">
            <h3>Please correct the following errors:</h3>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ $this->form }}

    <button type="submit">Save</button>
</form>
```

## Client-Side Validation

### HTML5 Validation

Form fields automatically include HTML5 validation attributes:

```php
InputField::make('email')
    ->email()           // Adds type="email"
    ->required()        // Adds required attribute
    ->maxLength(255),   // Adds maxlength="255"

InputField::make('age')
    ->number()          // Adds type="number"
    ->min(18)           // Adds min="18"
    ->max(65),          // Adds max="65"
```

### Alpine.js Integration

For custom client-side validation:

```php
InputField::make('username')
    ->attributes([
        'x-data' => '{ valid: true }',
        'x-on:blur' => 'valid = $el.value.length >= 3',
        'x-bind:class' => '!valid ? "border-red-500" : "border-gray-300"'
    ])
    ->helperText('Username must be at least 3 characters'),
```

## Configuration

Configure validation behavior in your component:

```php
class UserForm extends Component
{
    use HasForm;

    // Customize validation behavior
    protected bool $validateOnBlur = true;
    protected bool $showValidationErrors = true;
    protected string $errorMessageStyle = 'inline'; // 'inline', 'summary', 'both'

    public function mount(): void
    {
        $this->mountForm();

        // Enable real-time validation globally
        if ($this->validateOnBlur) {
            $this->enableRealtimeValidation();
        }
    }

    protected function enableRealtimeValidation(): void
    {
        // Add listeners for all form fields
        $form = $this->getFormInstance();

        foreach ($form->getFields() as $field) {
            $this->listeners["formData.{$field->name}"] = 'validate' . str($field->name)->studly();
        }
    }
}
```

## Validation Examples

### User Registration Form

```php
public function form(Form $form): Form
{
    return $form
        ->heading([
            'title' => 'Create Account',
            'description' => 'All fields are required.'
        ])
        ->schema([
            InputField::make('name')
                ->label('Full Name')
                ->rules(['required', 'string', 'min:2', 'max:255'])
                ->placeholder('John Doe')
                ->required(),

            InputField::make('email')
                ->label('Email Address')
                ->email()
                ->rules(['required', 'email', 'unique:users,email'])
                ->placeholder('john@example.com')
                ->required(),

            InputField::make('username')
                ->label('Username')
                ->rules([
                    'required',
                    'string',
                    'min:3',
                    'max:20',
                    'alpha_dash',
                    'unique:users,username'
                ])
                ->validationMessages([
                    'alpha_dash' => 'Username can only contain letters, numbers, dashes and underscores.',
                    'unique' => 'This username is already taken.'
                ])
                ->helperText('3-20 characters, letters, numbers, dashes and underscores only')
                ->required(),

            InputField::make('password')
                ->label('Password')
                ->password()
                ->rules([
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
                ])
                ->validationMessages([
                    'regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character.'
                ])
                ->helperText('Minimum 8 characters with mixed case, numbers and symbols')
                ->required(),

            InputField::make('password_confirmation')
                ->label('Confirm Password')
                ->password()
                ->rules(['required', 'same:formData.password'])
                ->required(),

            CheckboxField::make('terms')
                ->label('I agree to the Terms of Service and Privacy Policy')
                ->rules(['accepted'])
                ->required(),
        ]);
}
```

### Profile Update Form with Conditional Validation

```php
public function form(Form $form): Form
{
    return $form->schema([
        InputField::make('current_password')
            ->label('Current Password')
            ->password()
            ->rules(function () {
                return $this->isChangingPassword()
                    ? ['required', 'current_password']
                    : [];
            })
            ->visible(fn () => $this->isChangingPassword()),

        InputField::make('new_password')
            ->label('New Password')
            ->password()
            ->rules(function () {
                return $this->isChangingPassword()
                    ? ['required', 'string', 'min:8', 'different:current_password']
                    : [];
            })
            ->visible(fn () => $this->isChangingPassword()),

        ToggleField::make('change_password')
            ->label('Change Password')
            ->live(),
    ]);
}

protected function isChangingPassword(): bool
{
    return $this->formData['change_password'] ?? false;
}
```

## See Also

- [Field Types](field-types.md) - Available form field types and their options
- [Form Sections](sections.md) - Organizing forms into sections
- [Model Binding](model-binding.md) - Advanced model integration techniques
