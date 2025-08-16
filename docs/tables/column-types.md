# Table Column Types

Laravel Simple Datatables And Forms provides various column types to display different kinds of data in your tables.
Each column type is optimized for specific data types and use cases.

## TextColumn

The most versatile column type for displaying text-based data.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TextColumn;

TextColumn::make('name')
    ->label('Full Name')
    ->searchable()
    ->sortable(),
```

### Text Formatting

```php
// Custom formatting
TextColumn::make('title')
    ->format(fn($value) => ucwords($value)),

// Date formatting
TextColumn::make('created_at')
    ->format(fn($value) => $value->format('M d, Y')),

// Currency formatting
TextColumn::make('price')
    ->format(fn($value) => '$' . number_format($value, 2)),

// Custom value calculation
TextColumn::make('full_name')
    ->value(fn($row) => $row->first_name . ' ' . $row->last_name),
```

### Text Styling

```php
TextColumn::make('status')
    ->color('text-green-600')      // Text color
    ->background('bg-green-50')    // Background color
    ->weight('font-bold')          // Font weight
    ->align('center')              // Text alignment
    ->wrap(false),                 // Disable text wrapping
```

### Conditional Styling

```php
TextColumn::make('status')
    ->color(fn($row) => match($row->status) {
        'active' => 'text-green-600',
        'inactive' => 'text-red-600',
        'pending' => 'text-yellow-600',
        default => 'text-gray-600',
    })
    ->background(fn($row) => match($row->status) {
        'active' => 'bg-green-50',
        'inactive' => 'bg-red-50',
        'pending' => 'bg-yellow-50',
        default => 'bg-gray-50',
    }),
```

## ToggleColumn

Interactive toggle switches for boolean values that can be changed directly in the table.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ToggleColumn;

ToggleColumn::make('is_active')
    ->label('Active Status')
    ->onLabel('Active')
    ->offLabel('Inactive'),
```

### Toggle Configuration

```php
ToggleColumn::make('featured')
    ->label('Featured')
    ->color('green')               // Toggle color: green, blue, red, yellow
    ->size('lg')                   // Size: sm, md, lg
    ->disabled(fn($row) => $row->is_locked), // Conditionally disable
    ->updateUsing('toggleFeatured'), // Custom update method
```

### Handling Toggle Updates

```php
// In your Livewire component
public function toggleFeatured($id, $value)
{
    $record = YourModel::find($id);
    $record->update(['featured' => $value]);

    $this->notification()->success('Status updated successfully');
}
```

## CheckBoxColumn

Checkboxes for selection and boolean values.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\CheckBoxColumn;

CheckBoxColumn::make('selected')
    ->label('Select')
    ->bulk(),                      // Enable bulk selection
```

### Checkbox Configuration

```php
CheckBoxColumn::make('terms_accepted')
    ->label('Terms Accepted')
    ->disabled(fn($row) => $row->is_locked)
    ->checkedValue(1)
    ->uncheckedValue(0)
    ->updateUsing('updateTermsAccepted'),
```

## ProgressColumn

Visual progress bars for numeric values with min/max ranges.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ProgressColumn;

ProgressColumn::make('completion')
    ->label('Progress')
    ->min(0)
    ->max(100)
    ->color('primary'),            // primary, success, warning, danger
```

### Progress Configuration

```php
ProgressColumn::make('score')
    ->label('Test Score')
    ->min(0)
    ->max(100)
    ->color(fn($value) => match(true) {
        $value >= 90 => 'success',
        $value >= 70 => 'primary',
        $value >= 50 => 'warning',
        default => 'danger',
    })
    ->showValue()                  // Display numeric value on progress bar
    ->format(fn($value) => $value . '%'),
```

## IconColumn

Display icons, particularly useful for boolean states and status indicators.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\IconColumn;

IconColumn::make('is_verified')
    ->boolean()                    // Use for boolean values
    ->label('Verified'),
```

### Boolean Icon Configuration

```php
IconColumn::make('status')
    ->boolean()
    ->trueIcon('heroicon-o-check-circle')
    ->falseIcon('heroicon-o-x-circle')
    ->trueColor('text-green-600')
    ->falseColor('text-red-600'),

// Or use the combined method
IconColumn::make('status')
    ->boolean()
    ->true('heroicon-o-check-circle', 'text-green-600')
    ->false('heroicon-o-x-circle', 'text-red-600'),
```

### Custom Icon Logic

```php
IconColumn::make('priority')
    ->icon(fn($row) => match($row->priority) {
        'high' => 'heroicon-o-exclamation-triangle',
        'medium' => 'heroicon-o-minus-circle',
        'low' => 'heroicon-o-information-circle',
    })
    ->color(fn($row) => match($row->priority) {
        'high' => 'text-red-600',
        'medium' => 'text-yellow-600',
        'low' => 'text-blue-600',
    }),
```

## ActionColumn

Display action buttons for row-specific operations.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ActionColumn;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\EditAction;
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions\DeleteAction;

ActionColumn::make('actions')
    ->label('Actions')
    ->actions([
        EditAction::make('edit')
            ->url(fn($row) => route('users.edit', $row)),
        DeleteAction::make('delete')
            ->action('deleteRecord'),
    ]),
```

### Action Grouping

```php
ActionColumn::make('actions')
    ->groupActions()               // Group actions in dropdown
    ->actions([
        EditAction::make('edit'),
        DeleteAction::make('delete'),
        ViewAction::make('view'),
    ]),
```

For detailed information about actions, see the [Actions Documentation](actions.md).

## ImageColumn

Display images with various sizing and styling options.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\ImageColumn;

ImageColumn::make('avatar')
    ->label('Profile Picture')
    ->disk('public')               // Storage disk
    ->size(50)                     // Size in pixels
    ->circular(),                  // Circular images
```

### Image Configuration

```php
ImageColumn::make('product_image')
    ->label('Product')
    ->disk('s3')
    ->size(80)
    ->square()                     // Square aspect ratio
    ->defaultImageUrl('/images/placeholder.png')
    ->tooltip(fn($row) => $row->name), // Show tooltip on hover
```

## BadgeColumn

Display colored badges for status, categories, or tags.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\BadgeColumn;

BadgeColumn::make('status')
    ->label('Status')
    ->colors([
        'active' => 'success',
        'inactive' => 'danger',
        'pending' => 'warning',
    ]),
```

### Badge Configuration

```php
BadgeColumn::make('category')
    ->label('Category')
    ->color(fn($value) => match($value) {
        'electronics' => 'blue',
        'clothing' => 'green',
        'books' => 'purple',
        default => 'gray',
    })
    ->size('lg')                   // sm, md, lg
    ->icon(fn($value) => match($value) {
        'electronics' => 'heroicon-o-cpu-chip',
        'clothing' => 'heroicon-o-shirt',
        'books' => 'heroicon-o-book-open',
    }),
```

## TagsColumn

Display multiple tags or labels for a single record.

### Basic Usage

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\TagsColumn;

TagsColumn::make('tags')
    ->label('Tags')
    ->separator(',')               // How tags are separated in data
    ->limit(3),                    // Maximum tags to display
```

### Tags Configuration

```php
TagsColumn::make('skills')
    ->label('Skills')
    ->color('blue')
    ->size('sm')
    ->limit(5)
    ->moreText('and {count} more')  // Text for additional tags
    ->expandable(),                 // Allow expanding to see all tags
```

## Common Column Options

All column types support these common options:

### Visibility and Layout

```php
TextColumn::make('name')
    ->visible(true)                // Show/hide column
    ->hidden()                     // Hide column but keep for export
    ->exportOnly()                 // Only show in exports
    ->columnSpan(2)                // Span multiple columns
    ->grow()                       // Allow column to grow
    ->shrink(),                    // Allow column to shrink
```

### Alignment and Styling

```php
TextColumn::make('amount')
    ->align('right')               // left, center, right
    ->headerAlign('center')        // Header alignment
    ->verticalAlign('top')         // top, middle, bottom
    ->width('200px')               // Fixed width
    ->minWidth('100px')            // Minimum width
    ->maxWidth('300px'),           // Maximum width
```

### Search and Sort

```php
TextColumn::make('name')
    ->searchable()                 // Enable search
    ->sortable()                   // Enable sorting
    ->searchable(['first_name', 'last_name']) // Search multiple columns
    ->sortUsing('custom_sort_method'), // Custom sort logic
```

### Tooltips and Help

```php
TextColumn::make('code')
    ->tooltip('Internal reference code')
    ->tooltip(fn($row) => "Created: {$row->created_at}")
    ->helperText('This is additional help text')
    ->copyable()                   // Allow copying value to clipboard
    ->copyMessage('Code copied!'), // Custom copy message
```

### Export Control

```php
TextColumn::make('name')
    ->exportable()                 // Include in exports
    ->exportable(false)            // Exclude from exports
    ->exportValue(fn($row) => strtoupper($row->name)) // Custom export value
    ->exportFormat(fn($value) => $value . ' (exported)'), // Export formatting
```

## Custom Column Types

You can create custom column types by extending the base column class:

```php
use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\Column;

class CurrencyColumn extends Column
{
    protected string $view = 'components.currency-column';

    public function currency(string $currency = 'USD'): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function format($value): string
    {
        return number_format($value, 2) . ' ' . $this->currency;
    }
}
```

Then use it in your tables:

```php
CurrencyColumn::make('price')
    ->currency('EUR')
    ->label('Price'),
```

## Performance Considerations

### Database Optimization

```php
// Use select to limit queried columns
public function table(Table $table): Table
{
    return $table
        ->query(
            User::query()->select(['id', 'name', 'email', 'status'])
        )
        ->schema([
            TextColumn::make('name'),
            TextColumn::make('email'),
            BadgeColumn::make('status'),
        ]);
}
```

### Eager Loading

```php
// Eager load relationships for related data
public function table(Table $table): Table
{
    return $table
        ->query(
            User::query()->with(['profile', 'roles'])
        )
        ->schema([
            TextColumn::make('name'),
            TextColumn::make('profile.bio'),
            TagsColumn::make('roles.name'),
        ]);
}
```

### Caching

```php
// Cache expensive calculations
TextColumn::make('calculated_field')
    ->value(function ($row) {
        return cache()->remember(
            "user_{$row->id}_calculation",
            3600,
            fn() => $this->expensiveCalculation($row)
        );
    }),
```

## Best Practices

1. **Choose the Right Column Type**: Use specific column types (BadgeColumn, IconColumn) instead of generic TextColumn
   when appropriate.

2. **Optimize Database Queries**: Use `select()` to limit columns and eager load relationships.

3. **Implement Proper Caching**: Cache expensive calculations and database queries.

4. **Use Conditional Logic**: Apply conditional styling and visibility based on data.

5. **Provide Clear Labels**: Use descriptive labels that users can understand.

6. **Consider Mobile Experience**: Test column layouts on mobile devices and use responsive design.

7. **Implement Proper Authorization**: Hide sensitive columns based on user permissions.

8. **Use Export Control**: Carefully control what data is included in exports.

For more advanced table features, see:

- [Actions Documentation](actions.md)
- [Search Documentation](search.md)
- [Export Documentation](export.md)
- [Configuration Documentation](configuration.md)
