# Relationship Select Fields

The Laravel Simple DataTables And Forms package now supports relationship-based select fields that automatically load
options from Eloquent relationships with advanced search and customization capabilities.

## Features

- **Eloquent Relationship Integration**: Automatically load options from model relationships
- **Custom Option Labels**: Use closures to format how options are displayed to users
- **Multi-Column Search**: Search across multiple database columns simultaneously
- **Query Modifications**: Apply custom constraints and ordering to relationship queries
- **Multiple Selection**: Select multiple related records with visual tags
- **Performance Optimized**: Efficient querying with proper relationship handling
- **Validation Integration**: Automatic validation rules for relationship fields

## Basic Usage

### Simple Relationship Select

```php
SelectField::make('user_id')
    ->label('User')
    ->relationship('user', 'name') // relationship method, display column
    ->searchable()
    ->required();
```

### With Form Model Context

```php
public function form(Form $form): Form
{
    return $form
        ->model(Product::class) // Required for relationship context
        ->sections([
            Section::make('basic_info')
                ->fields([
                    SelectField::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->required(),
                ])
        ]);
}
```

## Advanced Features

### Custom Option Labels

Use closures to customize how options are displayed:

```php
SelectField::make('user_id')
    ->label('User')
    ->relationship('user', 'name')
    ->getOptionLabelFromRecordUsing(fn(User $record) => $record->full_name)
    ->searchable();
```

#### Complex Label Formatting

```php
SelectField::make('user_id')
    ->label('User')
    ->relationship('user', 'name')
    ->getOptionLabelFromRecordUsing(function (User $record) {
        return "{$record->name} ({$record->email}) - {$record->department}";
    })
    ->searchable();
```

### Multi-Column Search

Specify which columns to search when filtering options:

```php
SelectField::make('user_id')
    ->label('User')
    ->relationship('user', 'name')
    ->searchable(['users.name', 'users.email', 'users.first_name', 'users.last_name'])
    ->getOptionLabelFromRecordUsing(fn(User $record) => "{$record->name} - {$record->email}");
```

### Multiple Selection

Enable multiple selection for many-to-many relationships:

```php
SelectField::make('tags')
    ->label('Product Tags')
    ->relationship('tags', 'name')
    ->multiple()
    ->searchable(['tags.name', 'tags.description'])
    ->getOptionLabelFromRecordUsing(fn(Tag $record) => "{$record->name} ({$record->type})")
    ->placeholder('Search and select multiple tags...');
```

### Query Modifications

Apply custom constraints to the relationship query:

```php
SelectField::make('active_user_id')
    ->label('Active Users')
    ->relationship('user', 'name')
    ->modifyQueryUsing(function ($query) {
        return $query->where('is_active', true)->where('role', '!=', 'admin')->orderBy('name');
    })
    ->searchable(['users.name', 'users.email']);
```

#### Complex Query Modifications

```php
SelectField::make('recent_products')
    ->label('Recent Products')
    ->relationship('products', 'name')
    ->modifyQueryUsing(function ($query) {
        return $query
            ->where('created_at', '>=', now()->subDays(30))
            ->where('is_published', true)
            ->with('category')
            ->orderBy('created_at', 'desc');
    })
    ->multiple()
    ->searchable(['products.name', 'products.sku'])
    ->getOptionLabelFromRecordUsing(
        fn(Product $record) => "{$record->name} (SKU: {$record->sku}) - {$record->category->name}",
    );
```

## Complete Example

Here's a comprehensive example showing all features:

```php
SelectField::make('user_id')
    ->label('User')
    ->relationship('user', 'name')
    ->getOptionLabelFromRecordUsing(function (User $record) {
        return "{$record->name} ({$record->email}) - {$record->department}";
    })
    ->searchable(['users.name', 'users.email', 'users.first_name', 'users.last_name', 'users.department'])
    ->modifyQueryUsing(function ($query) {
        return $query->where('is_active', true)->whereNotNull('email_verified_at')->orderBy('name');
    })
    ->placeholder('Search by name, email, or department...')
    ->required()
    ->helperText('Only active, verified users are shown');
```

## Model Requirements

### Form Model

The form must have a model class set for relationship context:

```php
public function form(Form $form): Form
{
    return $form
        ->model(Product::class) // This is required
        ->sections([...]);
}
```

### Relationship Methods

Your model must have the relationship method defined:

```php
class Product extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
```

### Database Structure

Ensure your database has the necessary foreign key columns:

```php
// products table
Schema::table('products', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained();
    $table->foreignId('category_id')->constrained();
});

// product_tag pivot table for many-to-many
Schema::create('product_tag', function (Blueprint $table) {
    $table->foreignId('product_id')->constrained();
    $table->foreignId('tag_id')->constrained();
});
```

## Validation

Relationship select fields automatically generate appropriate validation rules:

```php
// Single relationship
'formData.user_id' => 'required|exists:users,id'

// Multiple relationship
'formData.tags' => 'nullable|array'
'formData.tags.*' => 'exists:tags,id'
```

### Custom Validation

You can add additional validation rules:

```php
SelectField::make('user_id')
    ->relationship('user', 'name')
    ->rules(['required', 'exists:users,id', 'different:current_user_id'])
    ->searchable();
```

## Performance Considerations

### Eager Loading

For better performance with custom labels, consider eager loading:

```php
SelectField::make('user_id')
    ->relationship('user', 'name')
    ->modifyQueryUsing(fn($query) => $query->with('department', 'role'))
    ->getOptionLabelFromRecordUsing(function (User $record) {
        return "{$record->name} - {$record->department->name} ({$record->role->name})";
    });
```

### Large Datasets

For very large datasets, consider:

1. **Pagination**: Implement server-side pagination for options
2. **Caching**: Cache frequently accessed relationship options
3. **Indexing**: Ensure searchable columns are properly indexed

```php
// Example with query optimization
SelectField::make('user_id')
    ->relationship('user', 'name')
    ->modifyQueryUsing(function ($query) {
        return $query
            ->select('id', 'name', 'email') // Only select needed columns
            ->where('is_active', true)
            ->limit(100) // Limit results for performance
            ->orderBy('name');
    })
    ->searchable(['users.name']);
```

## Error Handling

The relationship select fields include comprehensive error handling:

```php
// Logs errors and returns empty array if relationship fails
try {
    $options = $field->getRelationshipOptions();
} catch (\Throwable $e) {
    \Log::error('SelectField relationship options failed: ' . $e->getMessage());
    return [];
}
```

## Security Considerations

- **Model Validation**: Only allows relationships on properly configured models
- **Query Sanitization**: All search inputs are sanitized
- **Access Control**: Respects model scopes and query constraints
- **Validation**: Automatic validation ensures only valid related records can be selected

## Troubleshooting

### Common Issues

1. **Relationship not found**: Ensure the relationship method exists on your model
2. **Empty options**: Check that the relationship returns records and the display column exists
3. **Search not working**: Verify searchable columns exist and are properly named
4. **Performance issues**: Consider adding database indexes on searchable columns

### Debug Mode

Enable debug logging to troubleshoot issues:

```php
// In your form component
public function mount()
{
    if (app()->environment('local')) {
        \Log::info('Form model class: ' . $this->getFormInstance()->getModelClass());
    }

    $this->mountForm();
}
```

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Future Enhancements

Planned features for future releases:

- **Server-side Search**: Real-time search with AJAX for large datasets
- **Dependent Selects**: Automatic filtering based on other field values
- **Relationship Caching**: Intelligent caching of relationship options
- **Custom Templates**: Customizable option and tag templates
- **Bulk Operations**: Bulk select/deselect functionality
