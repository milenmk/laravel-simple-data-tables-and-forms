# Getting Started with Data Tables

This guide will help you create your first data table using Laravel Simple Datatables And Forms.

## Prerequisites

Before you begin, ensure you have:

- Laravel 10.x or higher installed
- Livewire 3.x installed and configured
- Laravel Simple Datatables package installed And Forms

## Creating Your First Table

### Method 1: Using Artisan Command (Recommended)

The fastest way to create a table is using the provided Artisan command:

```bash
# Basic table generation
php artisan make:milenmk-datatable UserList User

# Generate with auto-generated columns based on model
php artisan make:milenmk-datatable UserList User --generate
```

This creates:

- A Livewire component at `app/Livewire/UserList.php`
- A Blade view at `resources/views/livewire/user-list.blade.php`
- Auto-generated columns (when using `--generate` flag)

### Method 2: Manual Creation

#### Step 1: Create a Livewire Component

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasTable;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Table;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ActionColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\EditAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\DeleteAction;
use App\Models\User;

class UserList extends Component
{
    use HasTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->heading('Users Management')
            ->schema([
                TextColumn::make('id')->label('ID')->sortable(),

                TextColumn::make('name')->label('Full Name')->searchable()->sortable(),

                TextColumn::make('email')->label('Email Address')->searchable()->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->format(fn($value) => $value->format('M d, Y'))
                    ->sortable(),

                ActionColumn::make('actions')
                    ->label('Actions')
                    ->actions([
                        EditAction::make('edit')
                            ->label('Edit')
                            ->icon('heroicon-o-pencil-square')
                            ->url(fn($row) => route('users.edit', $row)),

                        DeleteAction::make('delete')
                            ->label('Delete')
                            ->icon('heroicon-o-trash')
                            ->action('deleteUser')
                            ->requiresConfirmation(),
                    ]),
            ])
            ->striped()
            ->paginate();
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            $this->notification()->success('User deleted successfully');
        }
    }

    public function render()
    {
        return view('livewire.user-list');
    }
}
```

#### Step 2: Create the Blade View

Create `resources/views/livewire/user-list.blade.php`:

```blade
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage your application users</p>
    </div>

    {{ $this->table }}
</div>
```

#### Step 3: Add Route

Add a route to display your table:

```php
// routes/web.php
use App\Livewire\UserList;

Route::get('/users', UserList::class)->name('users.index');
```

## Understanding the Table Structure

### Basic Table Configuration

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())           // Base query
        ->heading('Users Management')     // Table title
        ->schema([                       // Column definitions
            // Columns go here
        ])
        ->striped()                      // Alternating row colors
        ->paginate()                     // Enable pagination
        ->perPage(25);                   // Items per page
}
```

### Column Types

#### TextColumn

The most common column type for displaying text data:

```php
TextColumn::make('name')
    ->label('Full Name')
    ->searchable()          // Enable search on this column
    ->sortable()            // Enable sorting
    ->format(fn($value) => ucwords($value)), // Custom formatting
```

#### ActionColumn

For displaying action buttons:

```php
ActionColumn::make('actions')
    ->label('Actions')
    ->actions([
        EditAction::make('edit')->url(fn($row) => route('users.edit', $row)),
        DeleteAction::make('delete')->action('deleteUser'),
    ]),
```

### Adding Search and Filters

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\SelectFilter;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\DateFilter;

public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([
            // columns...
        ])
        ->filters([
            SelectFilter::make('role')
                ->options([
                    'admin' => 'Administrator',
                    'user' => 'Regular User',
                ])
                ->label('User Role'),

            DateFilter::make('created_at')
                ->label('Registration Date'),
        ]);
}
```

## Customizing Appearance

### Table Styling Options

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([/* columns */])
        ->striped()              // Alternating row colors
        ->hover()                // Hover effects
        ->bordered()             // Table borders
        ->compact()              // Smaller padding
        ->responsive();          // Mobile-friendly
}
```

### Column Alignment and Styling

```php
TextColumn::make('amount')
    ->label('Amount')
    ->align('right')            // left, center, right
    ->headerAlign('center')     // Header alignment
    ->color('text-green-600')   // Text color
    ->background('bg-gray-50')  // Background color
    ->weight('font-bold'),      // Font weight
```

## Performance Optimization

### Enable Caching

```php
public function table(Table $table): Table
{
    return $table
        ->query(User::query())
        ->schema([/* columns */])
        ->cache(3600); // Cache for 1 hour
}
```

### Optimize Queries

```php
public function table(Table $table): Table
{
    return $table
        ->query(
            User::query()
                ->with(['profile', 'roles']) // Eager load relationships
                ->select(['id', 'name', 'email', 'created_at']) // Select only needed columns
        )
        ->schema([/* columns */]);
}
```

## Next Steps

Now that you have a basic table working, explore these advanced features:

1. **[Advanced Search](search.md)** - Implement complex search functionality
2. **[Data Export](export.md)** - Add CSV, Excel, and PDF export capabilities
3. **[Filters and Grouping](filters-and-grouping.md)** - Create advanced filtering options
4. **[Actions](actions.md)** - Add custom actions and bulk operations
5. **[Configuration](configuration.md)** - Customize global settings

## Troubleshooting

### Common Issues

**Table not displaying:**

- Ensure you've included `@SimpleDatatablesStyle` and `@SimpleDatatablesScript` in your layout
- Check that the Livewire component is properly registered

**Search not working:**

- Verify that columns are marked as `searchable()`
- Check database indexes for better performance

**Styling issues:**

- Ensure Tailwind CSS is properly configured
- Check that the package views are included in your Tailwind content paths

**Performance issues:**

- Enable caching for large datasets
- Use `select()` to limit queried columns
- Add database indexes for searchable/sortable columns

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

# Generate table component
php artisan make:milenmk-datatable UserList User --generate
```

### Essential Layout Setup

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

### Common Column Types Cheat Sheet

```php
// Text Column with all common options
TextColumn::make('name')
    ->label('Full Name')
    ->searchable()
    ->sortable()
    ->format(fn($value) => ucwords($value))
    ->color('text-gray-900')
    ->align('left'),

// Toggle Column
ToggleColumn::make('is_active')
    ->label('Active')
    ->onLabel('Yes')
    ->offLabel('No'),

// Icon Column for boolean states
IconColumn::make('is_verified')
    ->boolean()
    ->label('Verified')
    ->trueIcon('check-circle')
    ->falseIcon('x-circle')
    ->trueColor('text-green-600')
    ->falseColor('text-red-600'),

// Progress Column
ProgressColumn::make('completion')
    ->label('Progress')
    ->min(0)
    ->max(100)
    ->color('primary'),

// Action Column
ActionColumn::make('actions')
    ->label('Actions')
    ->actions([
        EditAction::make('edit')
            ->label('Edit')
            ->icon('pencil')
            ->url(fn($row) => route('users.edit', $row)),
        DeleteAction::make('delete')
            ->label('Delete')
            ->icon('trash')
            ->action('deleteUser')
            ->actionView('icon'),
    ]),
```

### Filters Quick Setup

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\SelectFilter;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\DateFilter;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Filters\TernaryFilter;

->filters([
    SelectFilter::make('status')
        ->options(['active' => 'Active', 'inactive' => 'Inactive'])
        ->label('Status'),

    DateFilter::make('created_at')
        ->label('Registration Date'),

    TernaryFilter::make('is_verified')
        ->label('Email Verified'),
])
```

### Export Configuration

```php
->export([
    'formats' => ['csv', 'excel', 'pdf'],
    'filename' => 'users_export',
    'max_rows' => 10000,
])
```

### Table Styling Options

```php
->striped()          // Alternating row colors
->hover()            // Hover effects
->bordered()         // Table borders
->compact()          // Smaller padding
->responsive()       // Mobile-friendly
```

### Performance Optimization Patterns

```php
// Eager loading relationships
->query(User::query()->with(['profile', 'roles']))

// Select specific columns only
->query(User::query()->select(['id', 'name', 'email']))

// Enable caching
->cache(3600) // Cache for 1 hour

// Pagination
->paginate(25)
```

### Conditional Logic Examples

```php
// Conditional column styling
TextColumn::make('status')
    ->color(fn($row) => $row->status === 'active' ? 'text-green-600' : 'text-red-600'),

// Conditional column visibility
TextColumn::make('admin_notes')
    ->visible(fn() => auth()->user()->isAdmin()),

// Dynamic column content
TextColumn::make('full_name')
    ->value(fn($row) => $row->first_name . ' ' . $row->last_name),
```

### Common Troubleshooting Solutions

```php
// Fix: Table not loading data
public function table(Table $table): Table
{
    return $table
        ->query(User::query()) // Don't forget the query!
        ->schema([...]);
}

// Fix: Search not working
TextColumn::make('name')->searchable(), // Mark columns as searchable

// Fix: Performance issues
Schema::table('users', function (Blueprint $table) {
    $table->index(['name', 'email']); // Add database indexes
});
```

### Configuration Quick Reference

```php
// config/simple-datatables-and-forms.php
'pagination' => [
    'per_page' => 25,
    'options' => [10, 25, 50, 100],
],

'search' => [
    'min_characters' => 2,
    'debounce_time' => 300,
],

'export' => [
    'formats' => ['csv', 'excel', 'pdf'],
    'max_rows' => 10000,
],

'cache' => [
    'enable' => true,
    'lifetime' => 3600,
],
```
