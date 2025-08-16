# Model Binding

Laravel Simple Datatables And Forms provides powerful model binding features that allow you to seamlessly integrate
forms with Eloquent models for both creating and updating records.

## Basic Model Binding

### Setting Form Model

```php
use App\Models\User;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;

public function form(Form $form): Form
{
    return $form
        ->model(User::class)  // Set the model class
        ->schema([
            InputField::make('name')
                ->label('Full Name')
                ->required(),

            InputField::make('email')
                ->email()
                ->required(),

            SelectField::make('role')
                ->options(['admin' => 'Admin', 'user' => 'User'])
                ->required(),
        ]);
}
```

### Loading Existing Model Data

```php
class EditUser extends Component
{
    use HasForm;

    public User $user;

    public function mount(User $user): void
    {
        $this->mountForm();
        $this->loadFormModel($user);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(User::class)
            ->schema([
                InputField::make('name')->label('Full Name')->required(),

                InputField::make('email')->email()->required(),
            ]);
    }
}
```

## Advanced Model Operations

### Creating New Records

```php
class CreateUser extends Component
{
    use HasForm;

    public function save()
    {
        // Get validation rules from form
        $rules = $this->getValidationRulesFromForm();
        $validated = $this->validate($rules);

        // Create new model instance
        $user = User::create($this->formData);

        // Optional: Set the created model
        $this->loadFormModel($user);

        $this->notification()->success('User created successfully!');

        // Redirect or reset form
        return redirect()->route('users.index');
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(User::class)
            ->schema([
                InputField::make('name')->label('Full Name')->required(),

                InputField::make('email')->email()->required(),

                InputField::make('password')->password()->required(),
            ]);
    }
}
```

### Updating Existing Records

```php
class EditUser extends Component
{
    use HasForm;

    public User $user;

    public function mount(User $user): void
    {
        $this->mountForm();
        $this->loadFormModel($user);
    }

    public function save()
    {
        $rules = $this->getValidationRulesFromForm();
        $validated = $this->validate($rules);

        // Update the existing model
        $this->formModel->update($this->formData);

        $this->notification()->success('User updated successfully!');
    }

    public function form(Form $form): Form
    {
        return $form->model(User::class)->schema([
            InputField::make('name')->label('Full Name')->required(),

            InputField::make('email')
                ->email()
                ->rules(['required', 'email', Rule::unique('users')->ignore($this->formModel?->id)]),
        ]);
    }
}
```

## Relationship Handling

### Belongs To Relationships

```php
public function form(Form $form): Form
{
    return $form
        ->model(Post::class)
        ->schema([
            InputField::make('title')
                ->label('Post Title')
                ->required(),

            SelectField::make('user_id')
                ->label('Author')
                ->options(User::pluck('name', 'id'))
                ->required(),

            SelectField::make('category_id')
                ->label('Category')
                ->options(Category::pluck('name', 'id'))
                ->required(),
        ]);
}
```

### Many-to-Many Relationships

```php
public function form(Form $form): Form
{
    return $form
        ->model(Post::class)
        ->schema([
            InputField::make('title')
                ->label('Post Title')
                ->required(),

            SelectField::make('tags')
                ->label('Tags')
                ->multiple()
                ->options(Tag::pluck('name', 'id'))
                ->relationship('tags'), // Handle pivot table automatically
        ]);
}

// In your save method
public function save()
{
    $validated = $this->validate($this->getValidationRulesFromForm());

    if ($this->formModel) {
        // Update existing post
        $this->formModel->update($this->formData);

        // Sync many-to-many relationships
        if (isset($this->formData['tags'])) {
            $this->formModel->tags()->sync($this->formData['tags']);
        }
    } else {
        // Create new post
        $post = Post::create(Arr::except($this->formData, ['tags']));

        // Attach tags
        if (isset($this->formData['tags'])) {
            $post->tags()->attach($this->formData['tags']);
        }
    }
}
```

### Has Many Relationships (Nested Forms)

```php
public function form(Form $form): Form
{
    return $form
        ->model(User::class)
        ->schema([
            InputField::make('name')
                ->label('User Name')
                ->required(),

            Section::make('Addresses')
                ->schema([
                    Repeater::make('addresses')
                        ->relationship('addresses')
                        ->schema([
                            InputField::make('street')
                                ->label('Street Address')
                                ->required(),

                            InputField::make('city')
                                ->label('City')
                                ->required(),

                            SelectField::make('type')
                                ->label('Address Type')
                                ->options([
                                    'home' => 'Home',
                                    'work' => 'Work',
                                    'other' => 'Other'
                                ]),
                        ])
                        ->minItems(1)
                        ->maxItems(5),
                ]),
        ]);
}
```

## Dynamic Model Selection

### Polymorphic Models

```php
class CommentForm extends Component
{
    use HasForm;

    public string $commentableType;
    public int $commentableId;

    public function mount(string $type, int $id): void
    {
        $this->commentableType = $type;
        $this->commentableId = $id;

        $this->mountForm();
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(Comment::class)
            ->schema([
                HiddenField::make('commentable_type')->default($this->commentableType),

                HiddenField::make('commentable_id')->default($this->commentableId),

                TextareaField::make('content')->label('Comment')->required(),

                InputField::make('author_name')->label('Your Name')->required(),
            ]);
    }

    public function save()
    {
        $validated = $this->validate($this->getValidationRulesFromForm());

        Comment::create($this->formData);

        $this->resetForm();
        $this->notification()->success('Comment added successfully!');
    }
}
```

### Conditional Model Binding

```php
public function form(Form $form): Form
{
    $modelClass = $this->formData['type'] === 'user' ? User::class : Company::class;

    return $form
        ->model($modelClass)
        ->schema([
            SelectField::make('type')
                ->label('Account Type')
                ->options([
                    'user' => 'Personal',
                    'company' => 'Business'
                ])
                ->required()
                ->live(), // Trigger form rebuild on change

            // Personal fields
            Section::make('Personal Information')
                ->visible(fn ($get) => $get('type') === 'user')
                ->schema([
                    InputField::make('first_name')
                        ->label('First Name')
                        ->required(),

                    InputField::make('last_name')
                        ->label('Last Name')
                        ->required(),
                ]),

            // Company fields
            Section::make('Company Information')
                ->visible(fn ($get) => $get('type') === 'company')
                ->schema([
                    InputField::make('company_name')
                        ->label('Company Name')
                        ->required(),

                    InputField::make('tax_id')
                        ->label('Tax ID')
                        ->required(),
                ]),
        ]);
}
```

## Model Validation Integration

### Using Model Rules

```php
class User extends Model
{
    public static function rules(int $id = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'password' => $id ? ['nullable', 'min:8'] : ['required', 'min:8'],
        ];
    }
}

// In your form component
public function save()
{
    $rules = $this->formModel?->exists
        ? User::rules($this->formModel->id)
        : User::rules();

    $this->validate(array_combine(
        array_map(fn($key) => "formData.{$key}", array_keys($rules)),
        array_values($rules)
    ));

    if ($this->formModel) {
        $this->formModel->update($this->formData);
    } else {
        User::create($this->formData);
    }
}
```

### Model Events Integration

```php
class CreateUser extends Component
{
    use HasForm;

    public function save()
    {
        $validated = $this->validate($this->getValidationRulesFromForm());

        DB::transaction(function () {
            $user = User::create($this->formData);

            // Model events will be triggered automatically
            // But you can also trigger custom events
            event(new UserRegistered($user));

            // Send welcome email
            Mail::to($user->email)->send(new WelcomeEmail($user));

            $this->loadFormModel($user);
        });

        $this->notification()->success('User created and welcome email sent!');
    }
}
```

## File Uploads with Models

### Single File Upload

```php
public function form(Form $form): Form
{
    return $form
        ->model(User::class)
        ->schema([
            InputField::make('name')
                ->label('Full Name')
                ->required(),

            FileUploadField::make('avatar')
                ->label('Profile Picture')
                ->image()
                ->maxSize(2048) // 2MB
                ->directory('avatars')
                ->visibility('public'),
        ]);
}

public function save()
{
    $validated = $this->validate($this->getValidationRulesFromForm());

    // Handle file upload
    if (isset($this->formData['avatar']) && $this->formData['avatar'] instanceof UploadedFile) {
        $avatarPath = $this->formData['avatar']->store('avatars', 'public');
        $this->formData['avatar'] = $avatarPath;
    }

    if ($this->formModel) {
        $this->formModel->update($this->formData);
    } else {
        User::create($this->formData);
    }
}
```

### Multiple File Uploads

```php
public function form(Form $form): Form
{
    return $form
        ->model(Post::class)
        ->schema([
            InputField::make('title')
                ->label('Post Title')
                ->required(),

            FileUploadField::make('attachments')
                ->label('Attachments')
                ->multiple()
                ->maxFiles(5)
                ->acceptedFileTypes(['pdf', 'doc', 'docx'])
                ->directory('post-attachments'),
        ]);
}

public function save()
{
    $validated = $this->validate($this->getValidationRulesFromForm());

    if ($this->formModel) {
        $post = $this->formModel;
        $post->update(Arr::except($this->formData, ['attachments']));
    } else {
        $post = Post::create(Arr::except($this->formData, ['attachments']));
    }

    // Handle file attachments
    if (isset($this->formData['attachments'])) {
        foreach ($this->formData['attachments'] as $file) {
            $path = $file->store('post-attachments', 'public');

            $post->attachments()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }
}
```

## Mass Assignment Protection

### Using Fillable

```php
// In your User model
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];
}

// In your form component
public function save()
{
    $validated = $this->validate($this->getValidationRulesFromForm());

    // Only fillable attributes will be mass assigned
    User::create($this->formData);
}
```

### Manual Assignment for Sensitive Fields

```php
public function save()
{
    $validated = $this->validate($this->getValidationRulesFromForm());

    $user = new User();

    // Mass assign safe fields
    $user->fill(Arr::except($this->formData, ['password', 'role']));

    // Manually handle sensitive fields
    if (!empty($this->formData['password'])) {
        $user->password = Hash::make($this->formData['password']);
    }

    // Handle role with permission check
    if (auth()->user()->can('assign-roles')) {
        $user->role = $this->formData['role'];
    }

    $user->save();
}
```

## Complex Model Interactions

### Wizard-Style Multi-Model Forms

```php
class RegistrationWizard extends Component
{
    use HasForm;

    public int $step = 1;
    public User $user;
    public Profile $profile;

    public function form(Form $form): Form
    {
        return match ($this->step) {
            1 => $this->userForm($form),
            2 => $this->profileForm($form),
            3 => $this->preferencesForm($form),
            default => $form,
        };
    }

    private function userForm(Form $form): Form
    {
        return $form
            ->model(User::class)
            ->schema([
                InputField::make('name')->label('Full Name')->required(),

                InputField::make('email')->email()->required(),

                InputField::make('password')->password()->required(),
            ]);
    }

    private function profileForm(Form $form): Form
    {
        return $form
            ->model(Profile::class)
            ->schema([
                InputField::make('phone')->label('Phone Number')->tel(),

                TextareaField::make('bio')->label('Biography')->maxLength(500),

                InputField::make('birth_date')->label('Date of Birth')->date(),
            ]);
    }

    public function nextStep()
    {
        // Validate current step
        $rules = $this->getValidationRulesFromForm();
        $this->validate($rules);

        // Save current step data
        match ($this->step) {
            1 => $this->saveUser(),
            2 => $this->saveProfile(),
        };

        $this->step++;
    }

    private function saveUser()
    {
        $this->user = User::create($this->formData);
        $this->resetForm();
    }

    private function saveProfile()
    {
        $this->user->profile()->create($this->formData);
        $this->resetForm();
    }
}
```

## Performance Optimization

### Lazy Loading Relationships

```php
public function form(Form $form): Form
{
    return $form
        ->model(Post::class)
        ->schema([
            SelectField::make('category_id')
                ->label('Category')
                ->options(function () {
                    // Lazy load options only when needed
                    return Category::active()->pluck('name', 'id');
                })
                ->searchable(),

            SelectField::make('tags')
                ->label('Tags')
                ->multiple()
                ->options(function () {
                    return Tag::popular()->pluck('name', 'id');
                }),
        ]);
}
```

### Caching Form Options

```php
use Illuminate\Support\Facades\Cache;

public function form(Form $form): Form
{
    return $form
        ->model(Product::class)
        ->schema([
            SelectField::make('category_id')
                ->label('Category')
                ->options(function () {
                    return Cache::remember('categories-options', 3600, function () {
                        return Category::active()->pluck('name', 'id');
                    });
                }),
        ]);
}
```

## See Also

- [Field Types](field-types.md) - Available form field types and their options
- [Form Validation](validation.md) - Validation rules and techniques
- [Form Sections](sections.md) - Organizing forms into sections
