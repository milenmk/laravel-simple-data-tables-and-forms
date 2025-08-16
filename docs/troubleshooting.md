# Troubleshooting Guide

This guide covers common issues you might encounter when using Laravel Simple Datatables And Forms and their solutions.

## Installation Issues

### Package Not Found

**Problem**: Composer cannot find the package.

```bash
Could not find package milenmk/laravel-simple-datatables-and-forms
```

**Solution**:

```bash
# Ensure you're using the correct package name
composer require milenmk/laravel-simple-datatables-and-forms

# If still having issues, clear composer cache
composer clear-cache
composer install
```

### Version Conflicts

**Problem**: Composer reports version conflicts with Laravel or Livewire.

**Solution**:

```bash
# Check your Laravel and Livewire versions
composer show laravel/framework
composer show livewire/livewire

# Update to compatible versions
composer update laravel/framework livewire/livewire

# Or specify compatible versions in composer.json
"laravel/framework": "^10.0|^11.0|^12.0",
"livewire/livewire": "^3.0"
```

## Asset Issues

### Styles Not Loading

**Problem**: Table appears unstyled or broken.

**Symptoms**:

- Table has no styling
- Buttons appear as plain text
- Layout is broken

**Solutions**:

1. **Check Asset Directives**:

    ```blade
    {{-- In your layout file --}}
    <head>
        @SimpleDatatablesStyle
    </head>
    <body>
        {{-- Your content --}}
        @SimpleDatatablesScript
    </body>
    ```

2. **Publish Assets**:

    ```bash
    php artisan simple-datatables-and-forms:publish-assets
    ```

3. **Clear Cache**:

    ```bash
    php artisan view:clear
    php artisan cache:clear
    ```

4. **Check Tailwind Configuration**:
    ```js
    // tailwind.config.js
    module.exports = {
        content: [
            './vendor/milenmk/laravel-simple-datatables-and-forms/resources/views/**/*.blade.php',
            // ... your other paths
        ],
    };
    ```

### JavaScript Not Working

**Problem**: Interactive features (search, filters, actions) don't work.

**Solutions**:

1. **Check Script Placement**:

    ```blade
    {{-- Place before closing </body> tag --}}
    @SimpleDatatablesScript
    ```

2. **Check for JavaScript Errors**:
    - Open browser developer tools (F12)
    - Check Console tab for errors
    - Common errors: Livewire not loaded, Alpine.js conflicts

3. **Livewire Integration**:

    ```blade
    {{-- Ensure Livewire is loaded first --}}
    @livewireStyles
    @SimpleDatatablesStyle
    
    @livewireScripts
    @SimpleDatatablesScript
    ```

## Table Display Issues

### Table Not Showing

**Problem**: Table component renders but shows no content.

**Debugging Steps**:

1. **Check Component Registration**:

    ```php
    // Ensure your component extends Component and uses HasTable
    use Livewire\Component;
    use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasTable;

    class UserList extends Component
    {
        use HasTable;
    }
    ```

2. **Verify Query**:

    ```php
    public function table(Table $table): Table
    {
        // Debug your query
        $query = User::query();
        dd($query->get()); // Temporary debug

        return $table
            ->query($query)
            ->schema([/* columns */]);
    }
    ```

3. **Check Blade Template**:
    ```blade
    <div>
        {{ $this->table }}
    </div>
    ```

### Empty Table with Data

**Problem**: Database has data but table shows "No records found".

**Solutions**:

1. **Check Query Scope**:

    ```php
    // Make sure your query isn't too restrictive
    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()) // Remove any restrictive where clauses temporarily
            ->schema([/* columns */]);
    }
    ```

2. **Verify Column Names**:

    ```php
    // Ensure column names match database fields
    TextColumn::make('name'), // Should match 'name' column in database
    ```

3. **Check Model Configuration**:
    ```php
    // Verify your model is properly configured
    class User extends Model
    {
        protected $table = 'users'; // Correct table name
        protected $fillable = ['name', 'email']; // Include necessary fields
    }
    ```

## Search Issues

### Search Not Working

**Problem**: Search input appears but doesn't filter results.

**Solutions**:

1. **Mark Columns as Searchable**:

    ```php
    TextColumn::make('name')
        ->searchable(), // Add this
    ```

2. **Check Database Indexes**:

    ```php
    // Add indexes for better search performance
    Schema::table('users', function (Blueprint $table) {
        $table->index(['name', 'email']);
    });
    ```

3. **Verify Search Configuration**:
    ```php
    // config/simple-datatables-and-forms.php
    'search' => [
        'min_characters' => 2, // Minimum characters to trigger search
        'debounce_time' => 300, // Debounce time in milliseconds
    ],
    ```

### Slow Search Performance

**Problem**: Search takes too long to execute.

**Solutions**:

1. **Add Database Indexes**:

    ```sql
    -- Add indexes for searchable columns
    CREATE INDEX idx_users_name ON users(name);
    CREATE INDEX idx_users_email ON users(email);
    ```

2. **Limit Search Columns**:

    ```php
    TextColumn::make('name')
        ->searchable(['name']), // Only search specific columns
    ```

3. **Enable Full-Text Search** (MySQL):

    ```sql
    -- Create full-text index
    ALTER TABLE users ADD FULLTEXT(name, email);
    ```

    ```php
    // config/simple-datatables-and-forms.php
    'search' => [
        'enable_fulltext' => true,
    ],
    ```

## Form Issues

### Form Not Displaying

**Problem**: Form component renders but shows no form fields.

**Solutions**:

1. **Check HasForm Trait**:

    ```php
    use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasForm;

    class CreateUser extends Component
    {
        use HasForm;

        public function mount(): void
        {
            $this->mountForm(); // Don't forget this
        }
    }
    ```

2. **Verify Form Schema**:
    ```php
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                InputField::make('name')->required(),
                // Add more fields
            ]);
    }
    ```

### Form Validation Not Working

**Problem**: Form submits without validation or validation messages don't appear.

**Solutions**:

1. **Call Validation in Save Method**:

    ```php
    public function save()
    {
        $this->validate(); // Add this line

        // Your save logic
    }
    ```

2. **Check Validation Rules**:

    ```php
    InputField::make('email')
        ->email()
        ->rules(['required', 'email', 'unique:users,email'])
        ->required(),
    ```

3. **Display Validation Errors**:

    ```blade
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    {{ $this->form }}
    ```

## Performance Issues

### Slow Table Loading

**Problem**: Tables take too long to load, especially with large datasets.

**Solutions**:

1. **Enable Pagination**:

    ```php
    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->schema([/* columns */])
            ->paginate(25); // Limit results per page
    }
    ```

2. **Optimize Database Queries**:

    ```php
    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->select(['id', 'name', 'email']) // Only select needed columns
                    ->with(['profile']) // Eager load relationships
            )
            ->schema([/* columns */]);
    }
    ```

3. **Enable Caching**:
    ```php
    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->schema([/* columns */])
            ->cache(3600); // Cache for 1 hour
    }
    ```

### Memory Issues

**Problem**: PHP runs out of memory when processing large datasets.

**Solutions**:

1. **Increase Memory Limit**:

    ```php
    // config/simple-datatables-and-forms.php
    'export' => [
        'memory_limit' => '512M',
    ],
    ```

2. **Use Chunking for Exports**:

    ```php
    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->schema([/* columns */])
            ->export([
                'chunk_size' => 1000,
            ]);
    }
    ```

3. **Limit Export Rows**:
    ```php
    'export' => [
        'max_rows' => 10000,
    ],
    ```

## Export Issues

### Export Not Working

**Problem**: Export button appears but downloads don't work.

**Solutions**:

1. **Check Required Packages**:

    ```bash
    # For Excel export
    composer require phpoffice/phpspreadsheet

    # For PDF export
    composer require barryvdh/laravel-dompdf
    ```

2. **Verify Export Configuration**:

    ```php
    // config/simple-datatables-and-forms.php
    'export' => [
        'enable' => true,
        'formats' => ['csv', 'excel', 'pdf'],
    ],
    ```

3. **Check File Permissions**:
    ```bash
    # Ensure storage directory is writable
    chmod -R 775 storage/
    chown -R www-data:www-data storage/
    ```

### Export Timeout

**Problem**: Large exports timeout before completing.

**Solutions**:

1. **Increase Timeout**:

    ```php
    'export' => [
        'timeout' => 300, // 5 minutes
    ],
    ```

2. **Use Queue for Large Exports**:
    ```php
    'export' => [
        'queue' => true,
        'queue_threshold' => 5000,
    ],
    ```

## Livewire Integration Issues

### Component Not Updating

**Problem**: Changes don't reflect in the component.

**Solutions**:

1. **Check Wire Directives**:

    ```blade
    {{-- Ensure proper wire directives --}}
    <input wire:model.live="search" />
    ```

2. **Verify Component Properties**:

    ```php
    class UserList extends Component
    {
        use HasTable;

        // Make sure properties are public or have proper getters
        public $search = '';
    }
    ```

3. **Clear Livewire Cache**:
    ```bash
    php artisan livewire:clear-cache
    ```

### Action Methods Not Found

**Problem**: Clicking actions results in "Method not found" errors.

**Solutions**:

1. **Verify Method Names**:

    ```php
    // In your action definition
    DeleteAction::make('delete')->action('deleteUser'),

    // Ensure method exists in component
    public function deleteUser($id)
    {
        // Method implementation
    }
    ```

2. **Check Method Visibility**:

    ```php
    // Methods must be public
    public function deleteUser($id) // ✓ Correct
    {
        // Implementation
    }

    private function deleteUser($id) // ✗ Wrong - not accessible
    {
        // Implementation
    }
    ```

## Common Error Messages

### "Class not found" Errors

```
Class 'Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasTable' not found
```

**Solution**: Ensure package is properly installed and autoloaded:

```bash
composer dump-autoload
```

### "Method does not exist" Errors

```
Method Livewire\Component::table does not exist
```

**Solution**: Add the HasTable trait:

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasTable;

class YourComponent extends Component
{
    use HasTable; // Add this line
}
```

### "View not found" Errors

```
View [livewire.your-component] not found
```

**Solution**: Create the missing view file or check the view path:

```php
public function render()
{
    return view('livewire.your-component'); // Ensure this file exists
}
```

## Debug Mode

Enable debug mode for detailed error information:

```php
// config/simple-datatables-and-forms.php
'debug' => env('APP_DEBUG', false),
```

Or temporarily in your component:

```php
public function table(Table $table): Table
{
    // Add debug information
    logger('Table query:', ['query' => User::query()->toSql()]);

    return $table
        ->query(User::query())
        ->schema([/* columns */]);
}
```

## Getting Help

If you're still experiencing issues:

1. **Check the GitHub Issues
   **: [Laravel Simple Datatables And Forms Issues](https://github.com/milenmk/laravel-simple-datatables-and-forms/issues)

2. **Create a Minimal Reproduction**: Create a simple example that demonstrates the issue

3. **Provide System Information**:
    - PHP version
    - Laravel version
    - Livewire version
    - Package version
    - Browser and version (for frontend issues)

4. **Include Error Messages**: Copy the complete error message and stack trace

5. **Share Relevant Code**: Include the component code and any related configuration

## Prevention Tips

1. **Keep Dependencies Updated**: Regularly update Laravel, Livewire, and the package
2. **Use Version Constraints**: Specify compatible versions in composer.json
3. **Test in Development**: Always test new features in development before production
4. **Monitor Performance**: Use Laravel Telescope or similar tools to monitor performance
5. **Backup Before Updates**: Always backup your application before major updates

For more specific issues, refer to the individual documentation sections:

- [Tables Documentation](tables/)
- [Forms Documentation](forms/)
- [Configuration Guide](tables/configuration.md)
