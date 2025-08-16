# Form Sections

Organize complex forms into logical sections for better user experience and maintainability. Form sections help break
down large forms into manageable groups of related fields.

## Basic Section Usage

### Creating Sections

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;

public function form(Form $form): Form
{
    return $form->schema([
        Section::make('Personal Information')
            ->description('Basic personal details')
            ->schema([
                InputField::make('first_name')
                    ->label('First Name')
                    ->required(),

                InputField::make('last_name')
                    ->label('Last Name')
                    ->required(),

                InputField::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                InputField::make('phone')
                    ->label('Phone Number')
                    ->tel(),
            ]),

        Section::make('Address Information')
            ->description('Your current address')
            ->schema([
                InputField::make('street_address')
                    ->label('Street Address')
                    ->columnSpan('full'),

                InputField::make('city')
                    ->label('City'),

                SelectField::make('state')
                    ->label('State')
                    ->options($stateOptions),

                InputField::make('postal_code')
                    ->label('Postal Code'),
            ]),

        Section::make('Account Settings')
            ->schema([
                InputField::make('username')
                    ->label('Username')
                    ->required(),

                InputField::make('password')
                    ->label('Password')
                    ->password()
                    ->required(),
            ]),
    ]);
}
```

## Section Configuration

### Section Styling and Layout

```php
Section::make('Profile Settings')
    ->description('Manage your profile information')
    ->icon('user')                          // Icon for section header
    ->collapsible()                         // Make section collapsible
    ->collapsed()                           // Start collapsed
    ->columns(3)                            // Number of columns in section
    ->compact()                             // Reduced padding/spacing
    ->aside()                               // Side-by-side layout with main content
    ->schema([
        // Section fields...
    ]),
```

### Conditional Sections

```php
Section::make('Company Information')
    ->description('Required for business accounts')
    ->visible(fn ($get) => $get('account_type') === 'business')
    ->schema([
        InputField::make('company_name')
            ->label('Company Name')
            ->required(),

        InputField::make('tax_id')
            ->label('Tax ID')
            ->required(),
    ]),
```

## Advanced Section Features

### Collapsible Sections

```php
Section::make('Advanced Options')
    ->description('Optional advanced configuration')
    ->collapsible()
    ->collapsed()                           // Start collapsed
    ->persistCollapsed()                    // Remember collapsed state
    ->schema([
        SelectField::make('timezone')
            ->label('Timezone')
            ->options($timezoneOptions),

        InputField::make('api_key')
            ->label('API Key')
            ->helperText('Optional: For advanced integrations'),
    ]),
```

### Aside Sections

Create side-by-side layouts with aside sections:

```php
public function form(Form $form): Form
{
    return $form
        ->columns(3)
        ->schema([
            // Main content (spans 2 columns)
            Section::make('Main Information')
                ->columnSpan(2)
                ->schema([
                    InputField::make('title')
                        ->label('Title')
                        ->columnSpan('full'),

                    TextareaField::make('content')
                        ->label('Content')
                        ->columnSpan('full')
                        ->rows(10),
                ]),

            // Sidebar (spans 1 column)
            Section::make('Publication Settings')
                ->aside()
                ->columnSpan(1)
                ->schema([
                    SelectField::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                            'archived' => 'Archived',
                        ]),

                    InputField::make('publish_date')
                        ->label('Publish Date')
                        ->date(),

                    ToggleField::make('featured')
                        ->label('Featured Article'),
                ]),
        ]);
}
```

### Nested Sections

Sections can contain other sections for complex forms:

```php
Section::make('User Configuration')
    ->schema([
        Section::make('Basic Settings')
            ->columns(2)
            ->schema([
                InputField::make('display_name')
                    ->label('Display Name'),

                SelectField::make('language')
                    ->label('Language')
                    ->options($languages),
            ]),

        Section::make('Notification Preferences')
            ->columns(1)
            ->schema([
                ToggleField::make('email_notifications')
                    ->label('Email Notifications'),

                ToggleField::make('sms_notifications')
                    ->label('SMS Notifications'),

                Section::make('Email Types')
                    ->visible(fn ($get) => $get('email_notifications'))
                    ->schema([
                        CheckboxField::make('marketing_emails')
                            ->label('Marketing emails'),

                        CheckboxField::make('security_alerts')
                            ->label('Security alerts'),
                    ]),
            ]),
    ]),
```

## Section Headers and Descriptions

### Rich Section Headers

```php
Section::make('Payment Information')
    ->description('Secure payment processing powered by Stripe')
    ->icon('credit-card')
    ->headerActions([
        Action::make('verify')
            ->label('Verify Payment Method')
            ->action('verifyPayment'),
    ])
    ->schema([
        // Payment fields...
    ]),
```

### Custom Section Content

```php
Section::make('Terms and Conditions')
    ->description('Please review and accept our terms')
    ->content(view('forms.sections.terms-content'))
    ->schema([
        CheckboxField::make('accept_terms')
            ->label('I have read and accept the terms and conditions')
            ->required(),
    ]),
```

## Section Validation

### Section-Level Validation

```php
Section::make('Shipping Address')
    ->description('Required for physical products')
    ->visible(fn ($get) => $get('requires_shipping'))
    ->schema([
        InputField::make('shipping_name')
            ->label('Full Name')
            ->rules(function ($get) {
                return $get('requires_shipping') ? ['required'] : [];
            }),

        InputField::make('shipping_address')
            ->label('Address')
            ->rules(function ($get) {
                return $get('requires_shipping') ? ['required'] : [];
            }),
    ])
    ->validate(function ($get) {
        if ($get('requires_shipping')) {
            // Custom section validation logic
            return [
                'shipping_name' => 'required|string',
                'shipping_address' => 'required|string',
            ];
        }
        return [];
    }),
```

## Responsive Sections

### Mobile-Friendly Sections

```php
Section::make('Product Details')
    ->columns([
        'default' => 1,      // 1 column on mobile
        'sm' => 2,           // 2 columns on small screens
        'md' => 3,           // 3 columns on medium screens
        'lg' => 4,           // 4 columns on large screens
    ])
    ->schema([
        InputField::make('name')
            ->label('Product Name')
            ->columnSpan([
                'default' => 'full',
                'md' => 2,
            ]),

        InputField::make('price')
            ->label('Price')
            ->number()
            ->columnSpan(1),

        InputField::make('sku')
            ->label('SKU')
            ->columnSpan(1),
    ]),
```

## Section Examples

### User Profile Form with Sections

```php
public function form(Form $form): Form
{
    return $form
        ->heading([
            'title' => 'Edit Profile',
            'description' => 'Update your profile information and settings.',
        ])
        ->schema([
            Section::make('Profile Information')
                ->description('Your public profile information')
                ->icon('user')
                ->columns(2)
                ->schema([
                    InputField::make('first_name')
                        ->label('First Name')
                        ->required(),

                    InputField::make('last_name')
                        ->label('Last Name')
                        ->required(),

                    InputField::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->columnSpan('full'),

                    TextareaField::make('bio')
                        ->label('Bio')
                        ->maxLength(500)
                        ->columnSpan('full'),
                ]),

            Section::make('Account Settings')
                ->description('Manage your account preferences')
                ->icon('cog')
                ->collapsible()
                ->schema([
                    SelectField::make('timezone')
                        ->label('Timezone')
                        ->options($timezoneOptions),

                    SelectField::make('language')
                        ->label('Language')
                        ->options([
                            'en' => 'English',
                            'es' => 'Spanish',
                            'fr' => 'French',
                        ]),

                    ToggleField::make('email_notifications')
                        ->label('Email Notifications'),

                    ToggleField::make('marketing_emails')
                        ->label('Marketing Emails')
                        ->visible(fn ($get) => $get('email_notifications')),
                ]),

            Section::make('Security')
                ->description('Update your password and security settings')
                ->icon('shield')
                ->collapsible()
                ->collapsed()
                ->schema([
                    InputField::make('current_password')
                        ->label('Current Password')
                        ->password(),

                    InputField::make('new_password')
                        ->label('New Password')
                        ->password()
                        ->rules(['nullable', 'min:8', 'confirmed']),

                    InputField::make('new_password_confirmation')
                        ->label('Confirm New Password')
                        ->password(),
                ]),
        ]);
}
```

### E-commerce Product Form

```php
public function form(Form $form): Form
{
    return $form
        ->columns(3)
        ->schema([
            // Main product information
            Section::make('Product Details')
                ->columnSpan(2)
                ->schema([
                    InputField::make('name')
                        ->label('Product Name')
                        ->required()
                        ->columnSpan('full'),

                    TextareaField::make('description')
                        ->label('Description')
                        ->required()
                        ->columnSpan('full'),

                    InputField::make('sku')
                        ->label('SKU')
                        ->required(),

                    SelectField::make('category_id')
                        ->label('Category')
                        ->options($categories)
                        ->required(),

                    InputField::make('price')
                        ->label('Price')
                        ->number()
                        ->step(0.01)
                        ->required(),

                    InputField::make('weight')
                        ->label('Weight (kg)')
                        ->number()
                        ->step(0.01),
                ]),

            // Sidebar settings
            Section::make('Settings')
                ->aside()
                ->columnSpan(1)
                ->schema([
                    SelectField::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'draft' => 'Draft',
                        ])
                        ->default('draft'),

                    ToggleField::make('featured')
                        ->label('Featured Product'),

                    ToggleField::make('track_inventory')
                        ->label('Track Inventory'),

                    InputField::make('stock_quantity')
                        ->label('Stock Quantity')
                        ->number()
                        ->visible(fn ($get) => $get('track_inventory')),
                ]),

            // SEO Section
            Section::make('SEO Settings')
                ->columnSpan('full')
                ->collapsible()
                ->collapsed()
                ->schema([
                    InputField::make('meta_title')
                        ->label('Meta Title')
                        ->maxLength(60)
                        ->helperText('Recommended: 50-60 characters'),

                    TextareaField::make('meta_description')
                        ->label('Meta Description')
                        ->maxLength(160)
                        ->helperText('Recommended: 150-160 characters'),

                    InputField::make('slug')
                        ->label('URL Slug')
                        ->helperText('Auto-generated if left blank'),
                ]),
        ]);
}
```

### Multi-Step Form with Sections

```php
class MultiStepForm extends Component
{
    use HasForm;

    public int $currentStep = 1;
    public int $totalSteps = 3;

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Step 1: Personal Information')
                ->visible(fn() => $this->currentStep === 1)
                ->schema([
                    InputField::make('first_name')->label('First Name')->required(),

                    InputField::make('last_name')->label('Last Name')->required(),

                    InputField::make('email')->label('Email')->email()->required(),
                ]),

            Section::make('Step 2: Company Information')
                ->visible(fn() => $this->currentStep === 2)
                ->schema([
                    InputField::make('company_name')->label('Company Name')->required(),

                    SelectField::make('industry')->label('Industry')->options($industries)->required(),
                ]),

            Section::make('Step 3: Review & Confirm')->visible(fn() => $this->currentStep === 3)->schema([
                // Review content
            ]),
        ]);
    }

    public function nextStep()
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }
}
```

## Styling and Customization

### Custom Section Styles

You can customize section appearance by publishing the views and modifying the CSS classes:

```bash
php artisan vendor:publish --tag=laravel-simple-datatables-and-forms-views
```

Then modify the section template in
`resources/views/vendor/laravel-simple-datatables-and-forms/components/form/section.blade.php`.

### Custom Section Templates

Create custom section templates for specific use cases:

```php
Section::make('Special Section')
    ->view('custom.form-sections.special')
    ->schema([
        // Fields...
    ]),
```

## See Also

- [Field Types](field-types.md) - Available form field types and their options
- [Form Validation](validation.md) - Validation rules and techniques
- [Model Binding](model-binding.md) - Advanced model integration techniques
