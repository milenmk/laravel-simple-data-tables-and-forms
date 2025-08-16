# Searchable Select Fields

The Laravel Simple DataTables And Forms package now includes enhanced searchable select fields that provide a modern,
user-friendly alternative to standard HTML select elements.

## Features

- **Search Functionality**: Type to filter options in real-time
- **Multiple Selection**: Select multiple options with visual tags
- **Keyboard Navigation**: Full keyboard support for accessibility
- **Dark Mode**: Automatic dark mode support
- **Responsive Design**: Works on all screen sizes
- **Alpine.js Integration**: Lightweight, no external dependencies
- **Livewire Compatible**: Seamless integration with Livewire forms

## Basic Usage

### Simple Searchable Select

```php
SelectField::make('country')
    ->label('Country')
    ->options([
        'us' => 'United States',
        'ca' => 'Canada',
        'uk' => 'United Kingdom',
        // ... more options
    ])
    ->searchable() // Enable search functionality
    ->placeholder('Search for a country...')
    ->required();
```

### Multiple Selection

```php
SelectField::make('skills')
    ->label('Skills')
    ->options([
        'php' => 'PHP',
        'javascript' => 'JavaScript',
        'python' => 'Python',
        // ... more options
    ])
    ->multiple() // Enable multiple selection
    ->searchable()
    ->placeholder('Select multiple skills...');
```

## Configuration Options

### Available Methods

| Method          | Description                        | Example                         |
| --------------- | ---------------------------------- | ------------------------------- |
| `searchable()`  | Enable search functionality        | `->searchable()`                |
| `multiple()`    | Enable multiple selection          | `->multiple()`                  |
| `placeholder()` | Set placeholder text               | `->placeholder('Search...')`    |
| `emptyOption()` | Add empty option for single select | `->emptyOption('Choose one')`   |
| `options()`     | Set available options              | `->options(['key' => 'value'])` |

### Advanced Configuration

```php
SelectField::make('categories')
    ->label('Categories')
    ->options(Category::pluck('name', 'id')->toArray())
    ->searchable()
    ->multiple()
    ->placeholder('Search and select categories...')
    ->helperText('You can select multiple categories')
    ->required();
```

## User Interface

### Single Select

- Displays selected option text
- Shows placeholder when no selection
- Dropdown with search input
- Checkmark indicates selected option

### Multiple Select

- Shows selected options as removable tags
- Displays count when many options selected
- Individual remove buttons on tags
- Clear all button when selections exist

## Keyboard Navigation

| Key                | Action                          |
| ------------------ | ------------------------------- |
| `Enter` or `Space` | Open/close dropdown             |
| `↑` `↓`            | Navigate through options        |
| `Enter`            | Select highlighted option       |
| `Escape`           | Close dropdown                  |
| `Backspace`        | Remove last selected (multiple) |

## Styling

The searchable select uses Tailwind CSS classes and follows the package's design system:

- Consistent with other form fields
- Dark mode support
- Focus states and transitions
- Responsive design
- Accessibility features

### Custom Styling

You can customize the appearance by modifying the CSS classes in your published views or by adding custom CSS:

```css
/* Custom searchable select styling */
.searchable-select-trigger {
    /* Custom trigger button styles */
}

.searchable-select-dropdown {
    /* Custom dropdown styles */
}

.searchable-select-tag {
    /* Custom tag styles for multiple select */
}
```

## JavaScript Integration

The searchable select is powered by an Alpine.js component that automatically registers when the package JavaScript is
loaded:

```html
@SimpleDatatablesScript
```

### Manual Registration

If you need to register the component manually:

```javascript
document.addEventListener('alpine:init', () => {
    Alpine.data('searchableSelect', searchableSelect);
});
```

## Examples

### E-commerce Product Form

```php
SelectField::make('category_id')
    ->label('Product Category')
    ->options(Category::pluck('name', 'id')->toArray())
    ->searchable()
    ->required()
    ->helperText('Choose the main category for this product'),

SelectField::make('tags')
    ->label('Product Tags')
    ->options(Tag::pluck('name', 'id')->toArray())
    ->multiple()
    ->searchable()
    ->placeholder('Add relevant tags...')
```

### User Management Form

```php
SelectField::make('roles')
    ->label('User Roles')
    ->options(Role::pluck('name', 'id')->toArray())
    ->multiple()
    ->searchable()
    ->placeholder('Assign roles to user...')
    ->required(),

SelectField::make('department_id')
    ->label('Department')
    ->options(Department::pluck('name', 'id')->toArray())
    ->searchable()
    ->emptyOption('Select department')
```

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Performance Considerations

- Options are filtered client-side for fast response
- Large option lists (1000+) may impact performance
- Consider server-side filtering for very large datasets
- Lazy loading options is recommended for relationship-based selects

## Accessibility

The searchable select includes full accessibility support:

- ARIA labels and descriptions
- Keyboard navigation
- Screen reader compatibility
- Focus management
- High contrast support

## Migration from Regular Selects

To convert existing select fields to searchable selects, simply add the `->searchable()` method:

```php
// Before
SelectField::make('status')
    ->options(['active' => 'Active', 'inactive' => 'Inactive'])

// After
SelectField::make('status')
    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
    ->searchable() // Add this line
```

The regular select will still be used as a fallback if JavaScript is disabled.

## Troubleshooting

### Common Issues

1. **Dropdown not appearing**: Ensure `@SimpleDatatablesScript` is included
2. **Styling issues**: Check that `@SimpleDatatablesStyle` is included
3. **Alpine.js conflicts**: Ensure Alpine.js is loaded before the package script
4. **Livewire sync issues**: Use `@entangle().defer` for better performance

### Debug Mode

Enable debug mode to see console logs:

```javascript
// Add to your app.js
window.searchableSelectDebug = true;
```

## Future Enhancements

Planned features for future releases:

- Server-side search for large datasets
- Custom option templates
- Grouping options
- Async option loading
- Virtual scrolling for performance
